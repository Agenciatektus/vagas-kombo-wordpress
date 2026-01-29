<?php
/**
 * Sistema de Atualizacao Automatica via GitHub
 *
 * Verifica e instala atualizacoes do plugin diretamente do GitHub Releases.
 *
 * @package Quadro_Vagas_Kombo
 * @since 1.0.0
 */

// Seguranca: Impede acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe Kombo_Updater
 *
 * Gerencia atualizacoes automaticas do plugin via GitHub.
 *
 * @since 1.0.0
 */
class Kombo_Updater {

	/**
	 * Basename do plugin (ex: quadro-vagas-kombo/quadro-vagas-kombo.php)
	 *
	 * @var string
	 */
	private $plugin_basename;

	/**
	 * Slug do plugin (ex: quadro-vagas-kombo)
	 *
	 * @var string
	 */
	private $plugin_slug;

	/**
	 * Versao atual do plugin
	 *
	 * @var string
	 */
	private $version;

	/**
	 * URL do repositorio GitHub (ex: https://github.com/user/repo)
	 *
	 * @var string
	 */
	private $github_repo_url;

	/**
	 * Usuario/Organizacao do GitHub
	 *
	 * @var string
	 */
	private $github_user;

	/**
	 * Nome do repositorio no GitHub
	 *
	 * @var string
	 */
	private $github_repo;

	/**
	 * Construtor
	 *
	 * @param string $plugin_file Caminho completo do arquivo principal do plugin
	 * @param string $version     Versao atual do plugin
	 * @param string $github_url  URL do repositorio GitHub
	 */
	public function __construct( $plugin_file, $version, $github_url ) {
		$this->plugin_basename = plugin_basename( $plugin_file );
		$this->plugin_slug     = dirname( $this->plugin_basename );
		$this->version         = $version;
		$this->github_repo_url = rtrim( $github_url, '/' );

		// Extrai usuario e repositorio da URL
		if ( preg_match( '#github\.com[:/]([^/]+)/([^/\.]+)#', $github_url, $matches ) ) {
			$this->github_user = $matches[1];
			$this->github_repo = $matches[2];
		}

		// Registra hooks do WordPress
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_info' ), 20, 3 );
		add_filter( 'upgrader_source_selection', array( $this, 'fix_plugin_folder' ), 10, 4 );
	}

	/**
	 * Verifica se ha atualizacoes disponiveis
	 *
	 * @param object $transient Objeto transient do WordPress
	 * @return object Transient modificado
	 */
	public function check_for_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		// Busca ultima release no GitHub
		$release = $this->get_latest_release();

		if ( ! $release || is_wp_error( $release ) ) {
			return $transient;
		}

		// Compara versoes
		if ( version_compare( $this->version, $release->version, '<' ) ) {
			$plugin_data = array(
				'slug'        => $this->plugin_slug,
				'new_version' => $release->version,
				'package'     => $release->download_url,
				'url'         => $release->html_url,
				'tested'      => $release->tested_up_to ?? '',
				'requires'    => $release->requires_at_least ?? '',
				'requires_php' => $release->requires_php ?? '',
			);

			$transient->response[ $this->plugin_basename ] = (object) $plugin_data;
		}

		return $transient;
	}

	/**
	 * Busca informacoes da ultima release no GitHub
	 *
	 * @return object|WP_Error|false Dados da release ou erro
	 */
	private function get_latest_release() {
		// Verifica cache
		$cache_key = 'kombo_updater_' . md5( $this->github_repo_url );
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		// Busca releases da API do GitHub
		$api_url = sprintf(
			'https://api.github.com/repos/%s/%s/releases/latest',
			$this->github_user,
			$this->github_repo
		);

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout' => 15,
				'headers' => array(
					'Accept' => 'application/vnd.github.v3+json',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body );

		if ( empty( $data->tag_name ) ) {
			return false;
		}

		// Processa dados da release
		$release = (object) array(
			'version'      => ltrim( $data->tag_name, 'v' ),
			'download_url' => $data->zipball_url ?? '',
			'html_url'     => $data->html_url ?? $this->github_repo_url,
			'body'         => $data->body ?? '',
			'name'         => $data->name ?? $data->tag_name,
		);

		// Procura asset ZIP se disponivel (melhor que zipball)
		if ( ! empty( $data->assets ) ) {
			foreach ( $data->assets as $asset ) {
				if ( preg_match( '/\.zip$/i', $asset->name ) ) {
					$release->download_url = $asset->browser_download_url;
					break;
				}
			}
		}

		// Cacheia por 12 horas
		set_transient( $cache_key, $release, 12 * HOUR_IN_SECONDS );

		return $release;
	}

	/**
	 * Fornece informacoes do plugin para a tela de atualizacao
	 *
	 * @param false|object|array $result Resultado da API
	 * @param string             $action Acao sendo executada
	 * @param object             $args   Argumentos da requisicao
	 * @return false|object Informacoes do plugin
	 */
	public function plugin_info( $result, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $result;
		}

		if ( $this->plugin_slug !== $args->slug ) {
			return $result;
		}

		$release = $this->get_latest_release();

		if ( ! $release || is_wp_error( $release ) ) {
			return $result;
		}

		// Monta informacoes do plugin
		$plugin_info = array(
			'name'              => 'Quadro de Vagas Kombo',
			'slug'              => $this->plugin_slug,
			'version'           => $release->version,
			'author'            => '<a href="https://agenciatektus.com.br">Agência Tektus</a>',
			'homepage'          => $this->github_repo_url,
			'requires'          => '6.0',
			'tested'            => '6.9',
			'requires_php'      => '7.4',
			'download_link'     => $release->download_url,
			'sections'          => array(
				'description'  => 'Plugin WordPress com widget Elementor para integrar vagas de emprego do Kombo.com.br.',
				'changelog'    => $this->format_changelog( $release->body ),
			),
			'banners'           => array(),
			'external'          => true,
		);

		return (object) $plugin_info;
	}

	/**
	 * Formata changelog do GitHub para HTML
	 *
	 * @param string $markdown Texto em markdown
	 * @return string HTML formatado
	 */
	private function format_changelog( $markdown ) {
		if ( empty( $markdown ) ) {
			return '<p>Veja o changelog completo no <a href="' . esc_url( $this->github_repo_url ) . '/releases" target="_blank">GitHub</a>.</p>';
		}

		// Conversao basica de markdown para HTML
		$html = wpautop( $markdown );
		$html = str_replace( '**', '<strong>', $html );
		$html = str_replace( '**', '</strong>', $html );

		return $html;
	}

	/**
	 * Corrige nome da pasta apos download do GitHub
	 *
	 * O GitHub cria pastas com sufixo (ex: plugin-main), precisamos renomear
	 *
	 * @param string      $source        Caminho da pasta fonte
	 * @param string      $remote_source Caminho remoto
	 * @param WP_Upgrader $upgrader      Instancia do upgrader
	 * @param array       $hook_extra    Informacoes extras
	 * @return string|WP_Error Novo caminho ou erro
	 */
	public function fix_plugin_folder( $source, $remote_source, $upgrader, $hook_extra ) {
		global $wp_filesystem;

		// Verifica se e nosso plugin
		if ( ! isset( $hook_extra['plugin'] ) || $hook_extra['plugin'] !== $this->plugin_basename ) {
			return $source;
		}

		// Define novo caminho
		$new_source = trailingslashit( $remote_source ) . $this->plugin_slug;

		// Renomeia pasta
		if ( $wp_filesystem->move( $source, $new_source ) ) {
			return $new_source;
		}

		return new WP_Error( 'rename_failed', __( 'Nao foi possivel renomear a pasta do plugin.', 'quadro-vagas-kombo' ) );
	}

	/**
	 * Limpa cache de atualizacoes
	 *
	 * @return void
	 */
	public function clear_cache() {
		$cache_key = 'kombo_updater_' . md5( $this->github_repo_url );
		delete_transient( $cache_key );
	}
}
