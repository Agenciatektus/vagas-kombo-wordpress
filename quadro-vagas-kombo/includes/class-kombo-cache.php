<?php
/**
 * Kombo Cache Handler
 *
 * Gerencia o cache de respostas da API Kombo usando WordPress Transients API.
 *
 * @package Quadro_Vagas_Kombo
 * @since 1.0.0
 */

// Seguranca: Impede acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Kombo_Cache
 *
 * Responsavel pelo cache das vagas usando Transients do WordPress.
 *
 * @since 1.0.0
 */
class Kombo_Cache {

    /**
     * Prefixo para chaves de cache
     *
     * @var string
     */
    const CACHE_PREFIX = 'kombo_vagas_';

    /**
     * Duracao padrao do cache em segundos (30 minutos)
     *
     * @var int
     */
    const DEFAULT_DURATION = 1800;

    /**
     * Obtem dados do cache ou busca dados frescos
     *
     * @param string   $cid            Codigo CID da empresa
     * @param int      $limit          Limite de vagas
     * @param int      $cache_duration Duracao do cache em minutos
     * @param callable $fetch_callback Callback para buscar dados frescos
     * @return array|WP_Error Dados cacheados ou frescos
     */
    public function get_or_fetch( $cid, $limit, $cache_duration, $fetch_callback ) {
        $cache_key = $this->generate_cache_key( $cid, $limit );

        // Tenta obter dados do cache
        $cached_data = $this->get( $cache_key );

        if ( false !== $cached_data ) {
            return $cached_data;
        }

        // Busca dados frescos
        $fresh_data = call_user_func( $fetch_callback );

        // Cacheia apenas respostas bem-sucedidas
        if ( ! is_wp_error( $fresh_data ) ) {
            $duration_seconds = $this->minutes_to_seconds( $cache_duration );
            $this->set( $cache_key, $fresh_data, $duration_seconds );
        }

        return $fresh_data;
    }

    /**
     * Obtem dados do cache
     *
     * @param string $cache_key Chave do cache
     * @return mixed|false Dados cacheados ou false se nao encontrado/expirado
     */
    public function get( $cache_key ) {
        return get_transient( $cache_key );
    }

    /**
     * Armazena dados no cache
     *
     * @param string $cache_key Chave do cache
     * @param mixed  $data      Dados para cachear
     * @param int    $duration  Duracao em segundos
     * @return bool Sucesso
     */
    public function set( $cache_key, $data, $duration = self::DEFAULT_DURATION ) {
        return set_transient( $cache_key, $data, $duration );
    }

    /**
     * Remove dados do cache
     *
     * @param string $cache_key Chave do cache
     * @return bool Sucesso
     */
    public function delete( $cache_key ) {
        return delete_transient( $cache_key );
    }

    /**
     * Limpa todas as entradas de cache do Kombo
     *
     * @return int Numero de entradas removidas
     */
    public function clear_all() {
        global $wpdb;

        $prefix = self::CACHE_PREFIX;

        // Para transients armazenados na tabela options
        $deleted = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                '_transient_' . $prefix . '%',
                '_transient_timeout_' . $prefix . '%'
            )
        );

        // Limpa object cache se disponivel
        if ( function_exists( 'wp_cache_flush_group' ) ) {
            wp_cache_flush_group( 'transients' );
        }

        return $deleted;
    }

    /**
     * Limpa cache para um CID especifico
     *
     * @param string $cid Codigo CID da empresa
     * @return bool Sucesso
     */
    public function clear_for_cid( $cid ) {
        global $wpdb;

        // Gera padrao de chave para este CID
        $cid_hash = md5( sanitize_text_field( $cid ) );
        $cache_key_pattern = self::CACHE_PREFIX . substr( $cid_hash, 0, 8 );

        $deleted = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                '_transient_' . $cache_key_pattern . '%',
                '_transient_timeout_' . $cache_key_pattern . '%'
            )
        );

        return $deleted > 0;
    }

    /**
     * Gera uma chave de cache unica baseada nos parametros
     *
     * @param string $cid   Codigo CID da empresa
     * @param int    $limit Limite de vagas
     * @return string Chave do cache
     */
    private function generate_cache_key( $cid, $limit ) {
        $key_data = array(
            'cid'   => sanitize_text_field( $cid ),
            'limit' => absint( $limit ),
        );

        // Cria um hash dos parametros para uma chave mais curta e consistente
        $hash = md5( wp_json_encode( $key_data ) );

        return self::CACHE_PREFIX . substr( $hash, 0, 16 );
    }

    /**
     * Converte minutos para segundos
     *
     * @param int $minutes Duracao em minutos
     * @return int Duracao em segundos
     */
    private function minutes_to_seconds( $minutes ) {
        $minutes = absint( $minutes );

        // Minimo 1 minuto, maximo 24 horas
        $minutes = max( 1, min( $minutes, 1440 ) );

        return $minutes * MINUTE_IN_SECONDS;
    }

    /**
     * Verifica se existe cache valido
     *
     * @param string $cid   Codigo CID da empresa
     * @param int    $limit Limite de vagas
     * @return bool True se existe cache valido
     */
    public function has_valid_cache( $cid, $limit ) {
        $cache_key = $this->generate_cache_key( $cid, $limit );
        return false !== $this->get( $cache_key );
    }

    /**
     * Obtem informacoes sobre o cache atual
     *
     * @param string $cid   Codigo CID da empresa
     * @param int    $limit Limite de vagas
     * @return array|false Informacoes do cache ou false se nao existe
     */
    public function get_cache_info( $cid, $limit ) {
        $cache_key        = $this->generate_cache_key( $cid, $limit );
        $timeout_key      = '_transient_timeout_' . $cache_key;
        $timeout          = get_option( $timeout_key );

        if ( false === $timeout ) {
            return false;
        }

        $remaining = $timeout - time();

        return array(
            'cache_key'      => $cache_key,
            'expires_at'     => $timeout,
            'remaining_time' => max( 0, $remaining ),
            'is_expired'     => $remaining <= 0,
        );
    }
}
