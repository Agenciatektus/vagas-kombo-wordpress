<?php
/**
 * Plugin Name: Quadro de Vagas Kombo
 * Plugin URI: https://valorh.com.br
 * Description: Exibe vagas de emprego do Kombo.com.br atraves de um widget Elementor personalizado.
 * Version: 1.0.0
 * Author: ValorH Consultoria
 * Author URI: https://valorh.com.br
 * Text Domain: quadro-vagas-kombo
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Elementor tested up to: 3.18
 * Elementor Pro tested up to: 3.18
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Quadro_Vagas_Kombo
 */

// Seguranca: Impede acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Constantes do plugin
define( 'KOMBO_VAGAS_VERSION', '1.0.0' );
define( 'KOMBO_VAGAS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'KOMBO_VAGAS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'KOMBO_VAGAS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'KOMBO_VAGAS_MINIMUM_ELEMENTOR_VERSION', '3.0.0' );
define( 'KOMBO_VAGAS_MINIMUM_PHP_VERSION', '7.4' );

/**
 * Classe principal do plugin Quadro de Vagas Kombo
 *
 * @since 1.0.0
 */
final class Quadro_Vagas_Kombo {

    /**
     * Instancia singleton
     *
     * @var Quadro_Vagas_Kombo|null
     */
    private static $_instance = null;

    /**
     * Retorna a instancia singleton do plugin
     *
     * @return Quadro_Vagas_Kombo
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Construtor - inicializa o plugin
     */
    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
        register_activation_hook( __FILE__, array( $this, 'on_activation' ) );
        register_deactivation_hook( __FILE__, array( $this, 'on_deactivation' ) );
    }

    /**
     * Callback executado quando todos os plugins estao carregados
     *
     * @return void
     */
    public function on_plugins_loaded() {
        // Carrega traducoes
        load_plugin_textdomain(
            'quadro-vagas-kombo',
            false,
            dirname( KOMBO_VAGAS_PLUGIN_BASENAME ) . '/languages'
        );

        // Verifica compatibilidade e inicializa
        if ( $this->is_compatible() ) {
            $this->load_dependencies();
            add_action( 'elementor/init', array( $this, 'init_elementor' ) );
        }
    }

    /**
     * Verifica se o ambiente e compativel com o plugin
     *
     * @return bool
     */
    private function is_compatible() {
        // Verifica se o Elementor esta instalado e ativado
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', array( $this, 'admin_notice_missing_elementor' ) );
            return false;
        }

        // Verifica versao do Elementor
        if ( ! version_compare( ELEMENTOR_VERSION, KOMBO_VAGAS_MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
            return false;
        }

        // Verifica versao do PHP
        if ( version_compare( PHP_VERSION, KOMBO_VAGAS_MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
            return false;
        }

        return true;
    }

    /**
     * Carrega as dependencias do plugin
     *
     * @return void
     */
    private function load_dependencies() {
        require_once KOMBO_VAGAS_PLUGIN_DIR . 'includes/class-kombo-cache.php';
        require_once KOMBO_VAGAS_PLUGIN_DIR . 'includes/class-kombo-api.php';
        require_once KOMBO_VAGAS_PLUGIN_DIR . 'includes/class-kombo-updater.php';

        // Inicializa sistema de atualizacao automatica
        $this->init_auto_updater();
    }

    /**
     * Inicializa sistema de atualizacao automatica via GitHub
     *
     * @return void
     */
    private function init_auto_updater() {
        new Kombo_Updater(
            __FILE__,
            KOMBO_VAGAS_VERSION,
            'https://github.com/Agenciatektus/vagas-kombo-wordpress'
        );
    }

    /**
     * Inicializa a integracao com o Elementor
     *
     * @return void
     */
    public function init_elementor() {
        // Registra categoria de widgets
        add_action( 'elementor/elements/categories_registered', array( $this, 'register_widget_category' ) );

        // Registra widgets
        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

        // Enfileira assets do frontend
        add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_styles' ) );
        add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        // Enfileira assets do editor
        add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_editor_styles' ) );
    }

    /**
     * Registra categoria personalizada de widgets
     *
     * @param \Elementor\Elements_Manager $elements_manager Gerenciador de elementos do Elementor
     * @return void
     */
    public function register_widget_category( $elements_manager ) {
        $elements_manager->add_category(
            'kombo-widgets',
            array(
                'title' => esc_html__( 'Kombo', 'quadro-vagas-kombo' ),
                'icon'  => 'fa fa-briefcase',
            )
        );
    }

    /**
     * Registra os widgets do plugin
     *
     * @param \Elementor\Widgets_Manager $widgets_manager Gerenciador de widgets do Elementor
     * @return void
     */
    public function register_widgets( $widgets_manager ) {
        require_once KOMBO_VAGAS_PLUGIN_DIR . 'includes/elementor-widgets/class-kombo-vagas-widget.php';
        $widgets_manager->register( new Kombo_Vagas_Widget() );
    }

    /**
     * Enfileira estilos do frontend
     *
     * @return void
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'kombo-vagas-widget',
            KOMBO_VAGAS_PLUGIN_URL . 'assets/css/vagas-widget.css',
            array(),
            KOMBO_VAGAS_VERSION
        );
    }

    /**
     * Enfileira scripts do frontend
     *
     * @return void
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'kombo-vagas-widget',
            KOMBO_VAGAS_PLUGIN_URL . 'assets/js/vagas-widget.js',
            array( 'jquery' ),
            KOMBO_VAGAS_VERSION,
            true
        );
    }

    /**
     * Enfileira estilos do editor Elementor
     *
     * @return void
     */
    public function enqueue_editor_styles() {
        wp_enqueue_style(
            'kombo-vagas-editor',
            KOMBO_VAGAS_PLUGIN_URL . 'assets/css/vagas-widget.css',
            array(),
            KOMBO_VAGAS_VERSION
        );
    }

    /**
     * Callback de ativacao do plugin
     *
     * @return void
     */
    public function on_activation() {
        // Limpa cache do rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Callback de desativacao do plugin
     *
     * @return void
     */
    public function on_deactivation() {
        // Limpa todos os caches do Kombo
        if ( class_exists( 'Kombo_Cache' ) ) {
            $cache = new Kombo_Cache();
            $cache->clear_all();
        }

        // Limpa cache do rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Aviso de administrador: Elementor nao instalado
     *
     * @return void
     */
    public function admin_notice_missing_elementor() {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__( '"%1$s" requer o plugin "%2$s" instalado e ativado.', 'quadro-vagas-kombo' ),
            '<strong>' . esc_html__( 'Quadro de Vagas Kombo', 'quadro-vagas-kombo' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'quadro-vagas-kombo' ) . '</strong>'
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }

    /**
     * Aviso de administrador: Versao minima do Elementor
     *
     * @return void
     */
    public function admin_notice_minimum_elementor_version() {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__( '"%1$s" requer "%2$s" versao %3$s ou superior.', 'quadro-vagas-kombo' ),
            '<strong>' . esc_html__( 'Quadro de Vagas Kombo', 'quadro-vagas-kombo' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'quadro-vagas-kombo' ) . '</strong>',
            KOMBO_VAGAS_MINIMUM_ELEMENTOR_VERSION
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }

    /**
     * Aviso de administrador: Versao minima do PHP
     *
     * @return void
     */
    public function admin_notice_minimum_php_version() {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__( '"%1$s" requer "%2$s" versao %3$s ou superior.', 'quadro-vagas-kombo' ),
            '<strong>' . esc_html__( 'Quadro de Vagas Kombo', 'quadro-vagas-kombo' ) . '</strong>',
            '<strong>' . esc_html__( 'PHP', 'quadro-vagas-kombo' ) . '</strong>',
            KOMBO_VAGAS_MINIMUM_PHP_VERSION
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }
}

// Inicializa o plugin
Quadro_Vagas_Kombo::instance();
