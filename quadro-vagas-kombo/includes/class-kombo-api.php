<?php
/**
 * Kombo API Handler
 *
 * Responsavel por buscar e processar vagas do feed XML do Kombo.
 *
 * @package Quadro_Vagas_Kombo
 * @since 1.0.0
 */

// Seguranca: Impede acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Kombo_API
 *
 * Responsavel por buscar e processar o feed XML de vagas do Kombo.
 *
 * @since 1.0.0
 */
class Kombo_API {

    /**
     * URL base do feed Kombo
     *
     * @var string
     */
    const FEED_BASE_URL = 'https://www.kombo.com.br/feed.php';

    /**
     * Timeout padrao para requisicoes (segundos)
     *
     * @var int
     */
    const DEFAULT_TIMEOUT = 15;

    /**
     * Busca vagas do Kombo
     *
     * @param string $cid   Codigo CID da empresa
     * @param int    $limit Numero maximo de vagas (0 = todas)
     * @return array|WP_Error Array de vagas ou WP_Error em caso de falha
     */
    public function get_vagas( $cid, $limit = 0 ) {
        // Valida CID
        if ( empty( $cid ) ) {
            return new WP_Error(
                'invalid_cid',
                __( 'CID Kombo invalido ou nao informado.', 'quadro-vagas-kombo' )
            );
        }

        // Sanitiza CID (string codificada em base64)
        $cid = sanitize_text_field( $cid );

        // Constroi URL do feed
        $feed_url = add_query_arg( 'codigo', $cid, self::FEED_BASE_URL );

        // Busca o feed
        $response = wp_remote_get(
            $feed_url,
            array(
                'timeout'    => self::DEFAULT_TIMEOUT,
                'sslverify'  => true,
                'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
                'headers'    => array(
                    'Accept' => 'application/xml, text/xml, application/rss+xml',
                ),
            )
        );

        // Verifica erros de requisicao
        if ( is_wp_error( $response ) ) {
            return new WP_Error(
                'request_failed',
                sprintf(
                    /* translators: %s: mensagem de erro */
                    __( 'Erro ao conectar com Kombo: %s', 'quadro-vagas-kombo' ),
                    $response->get_error_message()
                )
            );
        }

        // Verifica codigo de resposta HTTP
        $response_code = wp_remote_retrieve_response_code( $response );
        if ( 200 !== $response_code ) {
            return new WP_Error(
                'http_error',
                sprintf(
                    /* translators: %d: codigo HTTP */
                    __( 'Kombo retornou codigo HTTP %d', 'quadro-vagas-kombo' ),
                    $response_code
                )
            );
        }

        // Obtem corpo da resposta
        $body = wp_remote_retrieve_body( $response );
        if ( empty( $body ) ) {
            return new WP_Error(
                'empty_response',
                __( 'Resposta vazia do servidor Kombo.', 'quadro-vagas-kombo' )
            );
        }

        // Processa XML
        $vagas = $this->parse_xml_feed( $body );

        if ( is_wp_error( $vagas ) ) {
            return $vagas;
        }

        // Aplica limite se especificado
        if ( $limit > 0 && count( $vagas ) > $limit ) {
            $vagas = array_slice( $vagas, 0, $limit );
        }

        return $vagas;
    }

    /**
     * Processa feed XML em array de vagas
     *
     * @param string $xml_content Conteudo XML bruto
     * @return array|WP_Error Vagas processadas ou erro
     */
    private function parse_xml_feed( $xml_content ) {
        // Suprime erros XML para tratamento manual
        libxml_use_internal_errors( true );

        // Corrige encoding - converte para UTF-8 se necessario
        $xml_content = $this->fix_encoding( $xml_content );

        // Tenta carregar como SimpleXML
        $xml = simplexml_load_string( $xml_content, 'SimpleXMLElement', LIBXML_NOCDATA );

        if ( false === $xml ) {
            $errors = libxml_get_errors();
            libxml_clear_errors();

            return new WP_Error(
                'xml_parse_error',
                __( 'Erro ao processar XML do feed Kombo.', 'quadro-vagas-kombo' )
            );
        }

        $vagas = array();

        // Processa estrutura RSS (esperado: rss > channel > item)
        $items = array();

        if ( isset( $xml->channel->item ) ) {
            $items = $xml->channel->item;
        } elseif ( isset( $xml->item ) ) {
            $items = $xml->item;
        } elseif ( isset( $xml->vagas->vaga ) ) {
            $items = $xml->vagas->vaga;
        }

        foreach ( $items as $item ) {
            $vaga = $this->parse_vaga_item( $item );
            if ( $vaga ) {
                $vagas[] = $vaga;
            }
        }

        return $vagas;
    }

    /**
     * Processa item individual de vaga do XML
     *
     * @param SimpleXMLElement $item Elemento XML do item
     * @return array|null Dados da vaga processados ou null se invalido
     */
    private function parse_vaga_item( $item ) {
        // Extrai dados com fallbacks para diferentes estruturas XML possiveis
        $titulo_raw = $this->get_xml_value( $item, array( 'title', 'titulo', 'vaga', 'cargo' ) );

        // Limpa HTML e corrige encoding do titulo
        $titulo = $this->clean_html( $titulo_raw );

        // Valida campos obrigatorios
        if ( empty( $titulo ) ) {
            return null;
        }

        // Extrai descricao que pode conter informacoes adicionais
        $descricao_raw = $this->get_xml_value( $item, array( 'description', 'descricao' ) );

        // Tenta extrair informacoes da descricao
        $parsed_info = $this->parse_description( $descricao_raw );

        // Limpa descricao para exibicao
        $descricao_limpa = $this->clean_html( $descricao_raw );

        $vaga = array(
            'codigo'         => $this->get_xml_value( $item, array( 'codigo', 'code', 'id', 'guid' ) ),
            'titulo'         => $titulo,
            'cidade'         => ! empty( $parsed_info['cidade'] ) ? $parsed_info['cidade'] : $this->get_xml_value( $item, array( 'cidade', 'city', 'location' ) ),
            'estado'         => ! empty( $parsed_info['estado'] ) ? $parsed_info['estado'] : $this->get_xml_value( $item, array( 'estado', 'uf', 'state' ) ),
            'ramo_atividade' => ! empty( $parsed_info['ramo'] ) ? $parsed_info['ramo'] : $this->get_xml_value( $item, array( 'ramo', 'ramo_atividade', 'area', 'setor' ) ),
            'num_vagas'      => ! empty( $parsed_info['num_vagas'] ) ? (int) $parsed_info['num_vagas'] : (int) $this->get_xml_value( $item, array( 'num_vagas', 'vagas', 'quantidade', 'qty' ) ),
            'data_abertura'  => $this->get_xml_value( $item, array( 'pubDate', 'data_abertura', 'data', 'date' ) ),
            'link'           => $this->get_xml_value( $item, array( 'link', 'url' ) ),
            'descricao'      => $descricao_limpa,
        );

        // Formata localizacao (cidade/estado)
        if ( ! empty( $vaga['cidade'] ) && ! empty( $vaga['estado'] ) ) {
            $vaga['localizacao'] = sprintf( '%s/%s', $vaga['cidade'], $vaga['estado'] );
        } elseif ( ! empty( $vaga['cidade'] ) ) {
            $vaga['localizacao'] = $vaga['cidade'];
        } else {
            $vaga['localizacao'] = '';
        }

        // Formata data
        if ( ! empty( $vaga['data_abertura'] ) ) {
            $timestamp = strtotime( $vaga['data_abertura'] );
            if ( $timestamp ) {
                $vaga['data_formatada'] = date_i18n( 'd/m/Y', $timestamp );
            } else {
                $vaga['data_formatada'] = '';
            }
        } else {
            $vaga['data_formatada'] = '';
        }

        // Padrao num_vagas para 1 se nao especificado
        if ( $vaga['num_vagas'] < 1 ) {
            $vaga['num_vagas'] = 1;
        }

        return $vaga;
    }

    /**
     * Corrige encoding do conteudo XML para UTF-8
     *
     * @param string $content Conteudo XML
     * @return string Conteudo com encoding corrigido
     */
    private function fix_encoding( $content ) {
        // Detecta encoding atual
        $encoding = mb_detect_encoding( $content, array( 'UTF-8', 'ISO-8859-1', 'Windows-1252' ), true );

        // Se nao for UTF-8, converte
        if ( $encoding && $encoding !== 'UTF-8' ) {
            $content = mb_convert_encoding( $content, 'UTF-8', $encoding );
        }

        // Remove BOM se existir
        $content = preg_replace( '/^\xEF\xBB\xBF/', '', $content );

        // Corrige declaracao de encoding no XML
        $content = preg_replace( '/encoding=["\'](?:ISO-8859-1|Windows-1252|iso-8859-1)["\']/', 'encoding="UTF-8"', $content );

        return $content;
    }

    /**
     * Limpa HTML e converte para texto plano
     *
     * @param string $html Conteudo HTML
     * @return string Texto limpo
     */
    private function clean_html( $html ) {
        // Converte <br> para quebra de linha
        $text = preg_replace( '/<br\s*\/?>/i', "\n", $html );

        // Remove todas as tags HTML
        $text = strip_tags( $text );

        // Decodifica entidades HTML
        $text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );

        // Remove espacos extras e quebras de linha multiplas
        $text = preg_replace( '/[ \t]+/', ' ', $text );
        $text = preg_replace( '/\n\s*\n/', "\n", $text );

        return trim( $text );
    }

    /**
     * Extrai informacoes da descricao da vaga
     *
     * O feed Kombo pode incluir informacoes formatadas na descricao como:
     * - Area: Nome da Area
     * - Cidade/UF: Cidade/Estado
     * - Numero de vagas: X
     * - Ramo de atividade da empresa: Nome
     *
     * @param string $description Descricao da vaga
     * @return array Informacoes extraidas
     */
    private function parse_description( $description ) {
        $info = array(
            'cidade'    => '',
            'estado'    => '',
            'ramo'      => '',
            'num_vagas' => 0,
            'area'      => '',
        );

        if ( empty( $description ) ) {
            return $info;
        }

        // Limpa HTML antes de processar
        $description = $this->clean_html( $description );

        // Extrai Cidade/UF
        if ( preg_match( '/Cidade\/UF:\s*([^\/\n]+)\/([A-Z]{2})/i', $description, $matches ) ) {
            $info['cidade'] = trim( $matches[1] );
            $info['estado'] = trim( $matches[2] );
        } elseif ( preg_match( '/Cidade\/UF:\s*([^\n]+)/i', $description, $matches ) ) {
            $location = trim( $matches[1] );
            // Tenta separar cidade/estado
            if ( preg_match( '/(.+)\/([A-Z]{2})$/i', $location, $loc_matches ) ) {
                $info['cidade'] = trim( $loc_matches[1] );
                $info['estado'] = trim( $loc_matches[2] );
            } else {
                $info['cidade'] = $location;
            }
        }

        // Extrai Ramo de atividade
        if ( preg_match( '/Ramo de atividade(?:\s*da empresa)?:\s*([^\n]+)/i', $description, $matches ) ) {
            $info['ramo'] = trim( $matches[1] );
        }

        // Extrai Numero de vagas
        if ( preg_match( '/N[uú]mero de vagas:\s*(\d+)/i', $description, $matches ) ) {
            $info['num_vagas'] = (int) $matches[1];
        }

        // Extrai Area
        if ( preg_match( '/[AÁ]rea:\s*([^\n]+)/i', $description, $matches ) ) {
            $info['area'] = trim( $matches[1] );
        }

        return $info;
    }

    /**
     * Obtem valor do elemento XML tentando multiplos nomes de tags
     *
     * @param SimpleXMLElement $item      Elemento XML
     * @param array            $tag_names Nomes de tags possiveis
     * @return string Valor ou string vazia
     */
    private function get_xml_value( $item, $tag_names ) {
        foreach ( $tag_names as $tag ) {
            if ( isset( $item->$tag ) ) {
                $value = (string) $item->$tag;
                return trim( $value );
            }
        }
        return '';
    }

    /**
     * Constroi URL de candidatura para uma vaga
     *
     * @param string $cid       Codigo CID da empresa
     * @param string $vaga_code Codigo da vaga (opcional)
     * @return string URL de candidatura
     */
    public function get_application_url( $cid, $vaga_code = '' ) {
        $base_url = 'https://www.kombo.com.br/curriculo/cadastro-curriculo-gratis';

        $url = add_query_arg( 'cid', sanitize_text_field( $cid ), $base_url );

        if ( ! empty( $vaga_code ) ) {
            $url = add_query_arg( 'vaga', sanitize_text_field( $vaga_code ), $url );
        }

        return esc_url( $url );
    }

    /**
     * Constroi URL do quadro de vagas padrao Kombo
     *
     * @param string $cid Codigo CID da empresa
     * @return string URL do quadro de vagas
     */
    public function get_job_board_url( $cid ) {
        $base_url = 'https://www.kombo.com.br/curriculo/buscar-vagas-emprego';
        return esc_url( add_query_arg( 'cid', sanitize_text_field( $cid ), $base_url ) );
    }

    /**
     * Constroi URL da pagina inicial do trabalhe conosco
     *
     * @param string $cid Codigo CID da empresa
     * @return string URL da pagina inicial
     */
    public function get_home_url( $cid ) {
        $base_url = 'https://www.kombo.com.br/curriculo';
        return esc_url( add_query_arg( 'cid', sanitize_text_field( $cid ), $base_url ) );
    }

    /**
     * Testa conexao com a API Kombo
     *
     * @param string $cid Codigo CID da empresa
     * @return bool|WP_Error True se conexao OK, WP_Error em caso de falha
     */
    public function test_connection( $cid ) {
        $result = $this->get_vagas( $cid, 1 );

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        return true;
    }
}
