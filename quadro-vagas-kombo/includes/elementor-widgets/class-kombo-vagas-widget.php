<?php
/**
 * Kombo Vagas Elementor Widget
 *
 * Exibe vagas de emprego do Kombo em layouts personalizaveis.
 *
 * @package Quadro_Vagas_Kombo
 * @since 1.0.0
 */

// Seguranca: Impede acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Kombo_Vagas_Widget
 *
 * Widget Elementor para exibir vagas do Kombo.
 *
 * @since 1.0.0
 */
class Kombo_Vagas_Widget extends \Elementor\Widget_Base {

    /**
     * Retorna o nome do widget
     *
     * @return string
     */
    public function get_name(): string {
        return 'kombo-vagas';
    }

    /**
     * Retorna o titulo do widget
     *
     * @return string
     */
    public function get_title(): string {
        return esc_html__( 'Quadro de Vagas Kombo', 'quadro-vagas-kombo' );
    }

    /**
     * Retorna o icone do widget
     *
     * @return string
     */
    public function get_icon(): string {
        return 'eicon-posts-grid';
    }

    /**
     * Retorna as categorias do widget
     *
     * @return array
     */
    public function get_categories(): array {
        return array( 'kombo-widgets', 'general' );
    }

    /**
     * Retorna palavras-chave do widget
     *
     * @return array
     */
    public function get_keywords(): array {
        return array( 'vagas', 'empregos', 'jobs', 'kombo', 'rh', 'trabalhe conosco', 'curriculo' );
    }

    /**
     * Retorna dependencias de estilos
     *
     * @return array
     */
    public function get_style_depends(): array {
        return array( 'kombo-vagas-widget' );
    }

    /**
     * Retorna dependencias de scripts
     *
     * @return array
     */
    public function get_script_depends(): array {
        return array( 'kombo-vagas-widget' );
    }

    /**
     * Registra controles do widget
     *
     * @return void
     */
    protected function register_controls(): void {
        $this->register_content_controls();
        $this->register_display_controls();
        $this->register_filter_controls();
        $this->register_frontend_filter_controls();
        $this->register_button_controls();
        $this->register_advanced_controls();
        $this->register_style_card_controls();
        $this->register_style_title_controls();
        $this->register_style_info_controls();
        $this->register_style_button_controls();
        $this->register_style_filter_controls();
        $this->register_style_spacing_controls();
    }

    /**
     * Registra controles da aba Conteudo - Secao Principal
     *
     * @return void
     */
    private function register_content_controls(): void {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => esc_html__( 'Conteudo', 'quadro-vagas-kombo' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        // CID Kombo
        $this->add_control(
            'cid_kombo',
            array(
                'label'       => esc_html__( 'CID Kombo', 'quadro-vagas-kombo' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => esc_html__( 'Ex: ABC123XYZ', 'quadro-vagas-kombo' ),
                'description' => esc_html__( 'Codigo do cliente Kombo (encontrado em Dados Cadastrais)', 'quadro-vagas-kombo' ),
                'label_block' => true,
            )
        );

        // Layout
        $this->add_control(
            'layout',
            array(
                'label'   => esc_html__( 'Layout', 'quadro-vagas-kombo' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => array(
                    'grid'      => esc_html__( 'Grid (Cards)', 'quadro-vagas-kombo' ),
                    'lista'     => esc_html__( 'Lista', 'quadro-vagas-kombo' ),
                    'accordion' => esc_html__( 'Accordion', 'quadro-vagas-kombo' ),
                ),
            )
        );

        // Colunas (condicional ao layout grid)
        $this->add_responsive_control(
            'columns',
            array(
                'label'          => esc_html__( 'Colunas', 'quadro-vagas-kombo' ),
                'type'           => \Elementor\Controls_Manager::SLIDER,
                'range'          => array(
                    'px' => array(
                        'min' => 1,
                        'max' => 4,
                    ),
                ),
                'default'        => array(
                    'size' => 3,
                ),
                'tablet_default' => array(
                    'size' => 2,
                ),
                'mobile_default' => array(
                    'size' => 1,
                ),
                'condition'      => array(
                    'layout' => 'grid',
                ),
                'selectors'      => array(
                    '{{WRAPPER}} .kombo-vagas-grid' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
                ),
            )
        );

        // Limite de vagas
        $this->add_control(
            'limit',
            array(
                'label'       => esc_html__( 'Limite de Vagas', 'quadro-vagas-kombo' ),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 9,
                'min'         => 0,
                'description' => esc_html__( '0 = exibir todas as vagas', 'quadro-vagas-kombo' ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Registra controles de exibicao
     *
     * @return void
     */
    private function register_display_controls(): void {
        $this->start_controls_section(
            'section_display',
            array(
                'label' => esc_html__( 'Opcoes de Exibicao', 'quadro-vagas-kombo' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        // Exibir ramo de atividade
        $this->add_control(
            'show_ramo',
            array(
                'label'        => esc_html__( 'Exibir Ramo de Atividade', 'quadro-vagas-kombo' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Sim', 'quadro-vagas-kombo' ),
                'label_off'    => esc_html__( 'Nao', 'quadro-vagas-kombo' ),
                'return_value' => 'yes',
                'default'      => '',
            )
        );

        // Exibir cidade
        $this->add_control(
            'show_cidade',
            array(
                'label'        => esc_html__( 'Exibir Cidade', 'quadro-vagas-kombo' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Sim', 'quadro-vagas-kombo' ),
                'label_off'    => esc_html__( 'Nao', 'quadro-vagas-kombo' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        // Exibir numero de vagas
        $this->add_control(
            'show_num_vagas',
            array(
                'label'        => esc_html__( 'Exibir Numero de Vagas', 'quadro-vagas-kombo' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Sim', 'quadro-vagas-kombo' ),
                'label_off'    => esc_html__( 'Nao', 'quadro-vagas-kombo' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        // Exibir data de abertura
        $this->add_control(
            'show_data',
            array(
                'label'        => esc_html__( 'Exibir Data de Abertura', 'quadro-vagas-kombo' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Sim', 'quadro-vagas-kombo' ),
                'label_off'    => esc_html__( 'Nao', 'quadro-vagas-kombo' ),
                'return_value' => 'yes',
                'default'      => '',
            )
        );

        $this->end_controls_section();
    }

    /**
     * Registra controles de filtros
     *
     * @return void
     */
    private function register_filter_controls(): void {
        $this->start_controls_section(
            'section_filters',
            array(
                'label' => esc_html__( 'Filtros', 'quadro-vagas-kombo' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        // Filtrar por localidade
        $this->add_control(
            'filter_location',
            array(
                'label'       => esc_html__( 'Filtrar por Localizacao', 'quadro-vagas-kombo' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => esc_html__( 'Ex: Salvador, Sao Paulo/SP', 'quadro-vagas-kombo' ),
                'description' => esc_html__( 'Deixe vazio para exibir todas. Pode usar cidade ou cidade/UF', 'quadro-vagas-kombo' ),
                'label_block' => true,
            )
        );

        // Filtrar por ramo/categoria
        $this->add_control(
            'filter_category',
            array(
                'label'       => esc_html__( 'Filtrar por Ramo/Area', 'quadro-vagas-kombo' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => esc_html__( 'Ex: Recursos Humanos', 'quadro-vagas-kombo' ),
                'description' => esc_html__( 'Deixe vazio para exibir todas', 'quadro-vagas-kombo' ),
                'label_block' => true,
            )
        );

        // Filtrar por numero minimo de vagas
        $this->add_control(
            'filter_min_positions',
            array(
                'label'       => esc_html__( 'Numero Minimo de Vagas', 'quadro-vagas-kombo' ),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 0,
                'min'         => 0,
                'description' => esc_html__( '0 = sem filtro', 'quadro-vagas-kombo' ),
            )
        );

        // Filtrar por data (ultimos X dias)
        $this->add_control(
            'filter_days',
            array(
                'label'       => esc_html__( 'Vagas dos Ultimos (dias)', 'quadro-vagas-kombo' ),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 0,
                'min'         => 0,
                'description' => esc_html__( '0 = sem filtro. Ex: 30 para ultimos 30 dias', 'quadro-vagas-kombo' ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Registra controles de filtros frontend (para visitantes)
     *
     * @return void
     */
    private function register_frontend_filter_controls(): void {
        $this->start_controls_section(
            'section_frontend_filters',
            array(
                'label' => esc_html__( 'Filtros Interativos (Frontend)', 'quadro-vagas-kombo' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        // Toggle para ativar filtros frontend
        $this->add_control(
            'enable_frontend_filters',
            array(
                'label'        => esc_html__( 'Ativar Filtros Interativos', 'quadro-vagas-kombo' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Sim', 'quadro-vagas-kombo' ),
                'label_off'    => esc_html__( 'Nao', 'quadro-vagas-kombo' ),
                'return_value' => 'yes',
                'default'      => '',
                'description'  => esc_html__( 'Exibe campos de filtro acima das vagas para visitantes filtrarem em tempo real', 'quadro-vagas-kombo' ),
            )
        );

        // Campos de filtro a exibir
        $this->add_control(
            'frontend_filter_fields',
            array(
                'label'       => esc_html__( 'Campos de Filtro', 'quadro-vagas-kombo' ),
                'type'        => \Elementor\Controls_Manager::SELECT2,
                'multiple'    => true,
                'options'     => array(
                    'location' => esc_html__( 'Cidade/Localizacao', 'quadro-vagas-kombo' ),
                    'area'     => esc_html__( 'Ramo/Area', 'quadro-vagas-kombo' ),
                ),
                'default'     => array( 'location', 'area' ),
                'condition'   => array(
                    'enable_frontend_filters' => 'yes',
                ),
            )
        );

        // Label localizacao
        $this->add_control(
            'filter_label_location',
            array(
                'label'       => esc_html__( 'Label - Localizacao', 'quadro-vagas-kombo' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__( 'Filtrar por cidade', 'quadro-vagas-kombo' ),
                'condition'   => array(
                    'enable_frontend_filters' => 'yes',
                    'frontend_filter_fields'  => 'location',
                ),
            )
        );

        // Label area
        $this->add_control(
            'filter_label_area',
            array(
                'label'       => esc_html__( 'Label - Ramo/Area', 'quadro-vagas-kombo' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__( 'Filtrar por area', 'quadro-vagas-kombo' ),
                'condition'   => array(
                    'enable_frontend_filters' => 'yes',
                    'frontend_filter_fields'  => 'area',
                ),
            )
        );

        // Texto botao limpar
        $this->add_control(
            'filter_reset_text',
            array(
                'label'     => esc_html__( 'Texto do Botao Limpar', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => esc_html__( 'Limpar Filtros', 'quadro-vagas-kombo' ),
                'condition' => array(
                    'enable_frontend_filters' => 'yes',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Registra controles do botao
     *
     * @return void
     */
    private function register_button_controls(): void {
        $this->start_controls_section(
            'section_button',
            array(
                'label' => esc_html__( 'Botao', 'quadro-vagas-kombo' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        // Texto do botao
        $this->add_control(
            'button_text',
            array(
                'label'   => esc_html__( 'Texto do Botao', 'quadro-vagas-kombo' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Candidatar-se', 'quadro-vagas-kombo' ),
            )
        );

        // URL de destino customizada
        $this->add_control(
            'custom_url',
            array(
                'label'       => esc_html__( 'URL de Destino', 'quadro-vagas-kombo' ),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://...', 'quadro-vagas-kombo' ),
                'description' => esc_html__( 'Deixe vazio para usar o cadastro padrao do Kombo', 'quadro-vagas-kombo' ),
                'default'     => array(
                    'url' => '',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Registra controles avancados
     *
     * @return void
     */
    private function register_advanced_controls(): void {
        $this->start_controls_section(
            'section_advanced',
            array(
                'label' => esc_html__( 'Configuracoes Avancadas', 'quadro-vagas-kombo' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        // Duracao do cache
        $this->add_control(
            'cache_duration',
            array(
                'label'       => esc_html__( 'Duracao do Cache (minutos)', 'quadro-vagas-kombo' ),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'default'     => 30,
                'min'         => 1,
                'max'         => 1440,
                'description' => esc_html__( 'Tempo que as vagas ficam em cache para melhorar performance', 'quadro-vagas-kombo' ),
            )
        );

        // Mensagem sem vagas
        $this->add_control(
            'no_jobs_message',
            array(
                'label'       => esc_html__( 'Mensagem sem Vagas', 'quadro-vagas-kombo' ),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'default'     => esc_html__( 'No momento nao ha vagas disponiveis. Cadastre seu curriculo para futuras oportunidades.', 'quadro-vagas-kombo' ),
                'placeholder' => esc_html__( 'Mensagem exibida quando nao ha vagas...', 'quadro-vagas-kombo' ),
            )
        );

        // Classes CSS customizadas
        $this->add_control(
            'custom_css_class',
            array(
                'label'       => esc_html__( 'Classes CSS Customizadas', 'quadro-vagas-kombo' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'classe1 classe2', 'quadro-vagas-kombo' ),
                'description' => esc_html__( 'Classes separadas por espaco', 'quadro-vagas-kombo' ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Registra controles de estilo do card
     *
     * @return void
     */
    private function register_style_card_controls(): void {
        $this->start_controls_section(
            'section_style_card',
            array(
                'label' => esc_html__( 'Card', 'quadro-vagas-kombo' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        // Cor de fundo do card
        $this->add_control(
            'card_background',
            array(
                'label'     => esc_html__( 'Cor de Fundo', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array(
                    '{{WRAPPER}} .kombo-vaga-card, {{WRAPPER}} .kombo-vaga-item, {{WRAPPER}} .kombo-accordion-item' => 'background-color: {{VALUE}};',
                ),
            )
        );

        // Espessura da borda
        $this->add_control(
            'card_border_width',
            array(
                'label'      => esc_html__( 'Espessura da Borda', 'quadro-vagas-kombo' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px' ),
                'default'    => array(
                    'top'    => '0',
                    'right'  => '0',
                    'bottom' => '0',
                    'left'   => '0',
                    'unit'   => 'px',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .kombo-vaga-card, {{WRAPPER}} .kombo-vaga-item, {{WRAPPER}} .kombo-accordion-item' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style: solid;',
                ),
            )
        );

        // Cor da borda
        $this->add_control(
            'card_border_color',
            array(
                'label'     => esc_html__( 'Cor da Borda', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#E0E0E0',
                'selectors' => array(
                    '{{WRAPPER}} .kombo-vaga-card, {{WRAPPER}} .kombo-vaga-item, {{WRAPPER}} .kombo-accordion-item' => 'border-color: {{VALUE}};',
                ),
            )
        );

        // Raio da borda do card
        $this->add_control(
            'card_border_radius',
            array(
                'label'      => esc_html__( 'Raio da Borda', 'quadro-vagas-kombo' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'default'    => array(
                    'top'    => '8',
                    'right'  => '8',
                    'bottom' => '8',
                    'left'   => '8',
                    'unit'   => 'px',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .kombo-vaga-card, {{WRAPPER}} .kombo-vaga-item, {{WRAPPER}} .kombo-accordion-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        // Padding do card
        $this->add_responsive_control(
            'card_padding',
            array(
                'label'      => esc_html__( 'Padding', 'quadro-vagas-kombo' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'default'    => array(
                    'top'    => '20',
                    'right'  => '20',
                    'bottom' => '20',
                    'left'   => '20',
                    'unit'   => 'px',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .kombo-vaga-card, {{WRAPPER}} .kombo-vaga-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        // Sombra do card
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'card_box_shadow',
                'label'    => esc_html__( 'Sombra', 'quadro-vagas-kombo' ),
                'selector' => '{{WRAPPER}} .kombo-vaga-card, {{WRAPPER}} .kombo-vaga-item, {{WRAPPER}} .kombo-accordion-item',
            )
        );

        // Cabecalho de efeito hover
        $this->add_control(
            'card_hover_heading',
            array(
                'label'     => esc_html__( 'Efeito Hover', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        // Cor de fundo no hover
        $this->add_control(
            'card_hover_background',
            array(
                'label'     => esc_html__( 'Cor de Fundo (Hover)', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .kombo-vaga-card:hover, {{WRAPPER}} .kombo-vaga-item:hover' => 'background-color: {{VALUE}};',
                ),
            )
        );

        // Efeito de elevacao
        $this->add_control(
            'card_hover_transform',
            array(
                'label'        => esc_html__( 'Efeito de Elevacao', 'quadro-vagas-kombo' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Sim', 'quadro-vagas-kombo' ),
                'label_off'    => esc_html__( 'Nao', 'quadro-vagas-kombo' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->end_controls_section();
    }

    /**
     * Registra controles de estilo do titulo
     *
     * @return void
     */
    private function register_style_title_controls(): void {
        $this->start_controls_section(
            'section_style_title',
            array(
                'label' => esc_html__( 'Titulo da Vaga', 'quadro-vagas-kombo' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        // Cor do titulo
        $this->add_control(
            'title_color',
            array(
                'label'     => esc_html__( 'Cor', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#8B1818',
                'selectors' => array(
                    '{{WRAPPER}} .kombo-vaga-title' => 'color: {{VALUE}};',
                ),
            )
        );

        // Tipografia do titulo
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => esc_html__( 'Tipografia', 'quadro-vagas-kombo' ),
                'selector' => '{{WRAPPER}} .kombo-vaga-title',
            )
        );

        // Margem do titulo
        $this->add_responsive_control(
            'title_margin',
            array(
                'label'      => esc_html__( 'Margem', 'quadro-vagas-kombo' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .kombo-vaga-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Registra controles de estilo das informacoes
     *
     * @return void
     */
    private function register_style_info_controls(): void {
        $this->start_controls_section(
            'section_style_info',
            array(
                'label' => esc_html__( 'Informacoes Secundarias', 'quadro-vagas-kombo' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        // Cor das informacoes
        $this->add_control(
            'info_color',
            array(
                'label'     => esc_html__( 'Cor', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#666666',
                'selectors' => array(
                    '{{WRAPPER}} .kombo-vaga-info' => 'color: {{VALUE}};',
                ),
            )
        );

        // Tipografia das informacoes
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'info_typography',
                'label'    => esc_html__( 'Tipografia', 'quadro-vagas-kombo' ),
                'selector' => '{{WRAPPER}} .kombo-vaga-info',
            )
        );

        // Cor do icone
        $this->add_control(
            'info_icon_color',
            array(
                'label'     => esc_html__( 'Cor do Icone', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#8B1818',
                'selectors' => array(
                    '{{WRAPPER}} .kombo-info-icon' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Registra controles de estilo do botao
     *
     * @return void
     */
    private function register_style_button_controls(): void {
        $this->start_controls_section(
            'section_style_button',
            array(
                'label' => esc_html__( 'Botao', 'quadro-vagas-kombo' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        // Cor de fundo do botao
        $this->add_control(
            'button_background',
            array(
                'label'     => esc_html__( 'Cor de Fundo', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#8B1818',
                'selectors' => array(
                    '{{WRAPPER}} .kombo-vaga-button' => 'background-color: {{VALUE}};',
                ),
            )
        );

        // Cor do texto do botao
        $this->add_control(
            'button_text_color',
            array(
                'label'     => esc_html__( 'Cor do Texto', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array(
                    '{{WRAPPER}} .kombo-vaga-button' => 'color: {{VALUE}};',
                ),
            )
        );

        // Cor de fundo no hover
        $this->add_control(
            'button_hover_background',
            array(
                'label'     => esc_html__( 'Cor de Fundo (Hover)', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#6B0F0F',
                'selectors' => array(
                    '{{WRAPPER}} .kombo-vaga-button:hover, {{WRAPPER}} .kombo-vaga-button:focus' => 'background-color: {{VALUE}};',
                ),
            )
        );

        // Cor do texto no hover
        $this->add_control(
            'button_hover_text_color',
            array(
                'label'     => esc_html__( 'Cor do Texto (Hover)', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array(
                    '{{WRAPPER}} .kombo-vaga-button:hover, {{WRAPPER}} .kombo-vaga-button:focus' => 'color: {{VALUE}};',
                ),
            )
        );

        // Tipografia do botao
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'button_typography',
                'label'    => esc_html__( 'Tipografia', 'quadro-vagas-kombo' ),
                'selector' => '{{WRAPPER}} .kombo-vaga-button',
            )
        );

        // Raio da borda do botao
        $this->add_control(
            'button_border_radius',
            array(
                'label'      => esc_html__( 'Raio da Borda', 'quadro-vagas-kombo' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'default'    => array(
                    'top'    => '4',
                    'right'  => '4',
                    'bottom' => '4',
                    'left'   => '4',
                    'unit'   => 'px',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .kombo-vaga-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        // Padding do botao
        $this->add_responsive_control(
            'button_padding',
            array(
                'label'      => esc_html__( 'Padding', 'quadro-vagas-kombo' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em' ),
                'default'    => array(
                    'top'    => '10',
                    'right'  => '20',
                    'bottom' => '10',
                    'left'   => '20',
                    'unit'   => 'px',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .kombo-vaga-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        // Largura total do botao
        $this->add_control(
            'button_full_width',
            array(
                'label'        => esc_html__( 'Largura Total', 'quadro-vagas-kombo' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Sim', 'quadro-vagas-kombo' ),
                'label_off'    => esc_html__( 'Nao', 'quadro-vagas-kombo' ),
                'return_value' => 'yes',
                'default'      => '',
                'selectors'    => array(
                    '{{WRAPPER}} .kombo-vaga-button' => 'width: 100%; display: block; text-align: center;',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Registra controles de estilo dos filtros frontend
     *
     * @return void
     */
    private function register_style_filter_controls(): void {
        $this->start_controls_section(
            'section_style_filters',
            array(
                'label'     => esc_html__( 'Filtros Interativos', 'quadro-vagas-kombo' ),
                'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'enable_frontend_filters' => 'yes',
                ),
            )
        );

        // Cor de fundo do container
        $this->add_control(
            'filter_background',
            array(
                'label'     => esc_html__( 'Cor de Fundo', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#F5F5F5',
                'selectors' => array(
                    '{{WRAPPER}} .kombo-filters-wrapper' => 'background-color: {{VALUE}};',
                ),
            )
        );

        // Cor da borda dos inputs
        $this->add_control(
            'filter_input_border_color',
            array(
                'label'     => esc_html__( 'Cor da Borda do Campo', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#CCCCCC',
                'selectors' => array(
                    '{{WRAPPER}} .kombo-filter-input' => 'border-color: {{VALUE}};',
                ),
            )
        );

        // Cor do botão limpar
        $this->add_control(
            'filter_reset_background',
            array(
                'label'     => esc_html__( 'Cor do Botao Limpar', 'quadro-vagas-kombo' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#8B1818',
                'selectors' => array(
                    '{{WRAPPER}} .kombo-filter-reset' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Registra controles de espacamento
     *
     * @return void
     */
    private function register_style_spacing_controls(): void {
        $this->start_controls_section(
            'section_style_spacing',
            array(
                'label' => esc_html__( 'Espacamento', 'quadro-vagas-kombo' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        // Gap entre cards
        $this->add_responsive_control(
            'cards_gap',
            array(
                'label'      => esc_html__( 'Espaco entre Cards', 'quadro-vagas-kombo' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 50,
                    ),
                ),
                'default'    => array(
                    'size' => 20,
                    'unit' => 'px',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .kombo-vagas-grid'      => 'gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .kombo-vagas-lista'     => 'gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .kombo-vagas-accordion' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Renderiza interface de filtros frontend
     *
     * @param array $settings Configuracoes do widget
     * @return void
     */
    private function render_frontend_filters( $settings ): void {
        if ( 'yes' !== $settings['enable_frontend_filters'] ) {
            return;
        }

        $filter_fields = isset( $settings['frontend_filter_fields'] ) ? $settings['frontend_filter_fields'] : array();
        $widget_id     = $this->get_id();

        echo '<div class="kombo-filters-wrapper" role="search" aria-label="' . esc_attr__( 'Filtrar vagas', 'quadro-vagas-kombo' ) . '">';
        echo '<div class="kombo-filters-container">';

        // Filtro de localização
        if ( in_array( 'location', $filter_fields, true ) ) {
            $label = ! empty( $settings['filter_label_location'] )
                ? $settings['filter_label_location']
                : __( 'Filtrar por cidade', 'quadro-vagas-kombo' );

            echo '<div class="kombo-filter-field">';
            echo '<label for="kombo-filter-location-' . esc_attr( $widget_id ) . '" class="kombo-filter-label">';
            echo esc_html( $label );
            echo '</label>';
            echo '<input type="text" ';
            echo 'id="kombo-filter-location-' . esc_attr( $widget_id ) . '" ';
            echo 'class="kombo-filter-input kombo-filter-location" ';
            echo 'placeholder="' . esc_attr( $label ) . '" ';
            echo 'aria-label="' . esc_attr( $label ) . '" ';
            echo 'autocomplete="off">';
            echo '</div>';
        }

        // Filtro de área
        if ( in_array( 'area', $filter_fields, true ) ) {
            $label = ! empty( $settings['filter_label_area'] )
                ? $settings['filter_label_area']
                : __( 'Filtrar por area', 'quadro-vagas-kombo' );

            echo '<div class="kombo-filter-field">';
            echo '<label for="kombo-filter-area-' . esc_attr( $widget_id ) . '" class="kombo-filter-label">';
            echo esc_html( $label );
            echo '</label>';
            echo '<input type="text" ';
            echo 'id="kombo-filter-area-' . esc_attr( $widget_id ) . '" ';
            echo 'class="kombo-filter-input kombo-filter-area" ';
            echo 'placeholder="' . esc_attr( $label ) . '" ';
            echo 'aria-label="' . esc_attr( $label ) . '" ';
            echo 'autocomplete="off">';
            echo '</div>';
        }

        // Botão Reset
        $reset_text = ! empty( $settings['filter_reset_text'] )
            ? $settings['filter_reset_text']
            : __( 'Limpar Filtros', 'quadro-vagas-kombo' );

        echo '<div class="kombo-filter-field kombo-filter-actions">';
        echo '<button type="button" class="kombo-filter-reset" aria-label="' . esc_attr( $reset_text ) . '">';
        echo esc_html( $reset_text );
        echo '</button>';
        echo '</div>';

        echo '</div>'; // .kombo-filters-container

        // Contador de resultados
        echo '<div class="kombo-filter-results" role="status" aria-live="polite">';
        echo '<span class="kombo-filter-count"></span>';
        echo '</div>';

        echo '</div>'; // .kombo-filters-wrapper
    }

    /**
     * Constroi atributos de dados para filtragem frontend
     *
     * @param array $vaga Dados da vaga
     * @return string Atributos HTML data-*
     */
    private function build_filter_data_attributes( $vaga ): string {
        $attrs = array(
            'data-location' => ! empty( $vaga['localizacao'] ) ? esc_attr( strtolower( $vaga['localizacao'] ) ) : '',
            'data-city'     => ! empty( $vaga['cidade'] ) ? esc_attr( strtolower( $vaga['cidade'] ) ) : '',
            'data-state'    => ! empty( $vaga['estado'] ) ? esc_attr( strtolower( $vaga['estado'] ) ) : '',
            'data-area'     => ! empty( $vaga['ramo_atividade'] ) ? esc_attr( strtolower( $vaga['ramo_atividade'] ) ) : '',
        );

        return implode(
            ' ',
            array_map(
                function ( $key, $value ) {
                    return $key . '="' . $value . '"';
                },
                array_keys( $attrs ),
                $attrs
            )
        );
    }

    /**
     * Renderiza saida do widget no frontend
     *
     * @return void
     */
    protected function render(): void {
        $settings = $this->get_settings_for_display();

        // Obtem configuracoes do widget
        $cid            = sanitize_text_field( $settings['cid_kombo'] );
        $layout         = sanitize_key( $settings['layout'] );
        $limit          = absint( $settings['limit'] );
        $cache_duration = absint( $settings['cache_duration'] );
        $custom_class   = isset( $settings['custom_css_class'] ) ? sanitize_text_field( $settings['custom_css_class'] ) : '';

        // Valida CID
        if ( empty( $cid ) ) {
            $this->render_error( __( 'CID Kombo nao configurado.', 'quadro-vagas-kombo' ) );
            return;
        }

        // Inicializa API e Cache
        $api   = new Kombo_API();
        $cache = new Kombo_Cache();

        // Busca vagas com caching
        $vagas = $cache->get_or_fetch(
            $cid,
            $limit,
            $cache_duration,
            function() use ( $api, $cid, $limit ) {
                return $api->get_vagas( $cid, $limit );
            }
        );

        // Trata erros
        if ( is_wp_error( $vagas ) ) {
            $this->render_error( $vagas->get_error_message() );
            return;
        }

        // Aplica filtros
        $vagas = $this->apply_filters( $vagas, $settings );

        // Trata ausencia de vagas apos filtros
        if ( empty( $vagas ) ) {
            $this->render_no_jobs( $settings );
            return;
        }

        // Renderiza baseado no layout
        $wrapper_classes = array( 'kombo-vagas-wrapper' );
        if ( ! empty( $custom_class ) ) {
            $wrapper_classes[] = esc_attr( $custom_class );
        }

        echo '<div class="' . esc_attr( implode( ' ', $wrapper_classes ) ) . '">';

        // Renderiza filtros frontend se ativados
        $this->render_frontend_filters( $settings );

        switch ( $layout ) {
            case 'lista':
                $this->render_lista_layout( $vagas, $settings, $cid, $api );
                break;
            case 'accordion':
                $this->render_accordion_layout( $vagas, $settings, $cid, $api );
                break;
            case 'grid':
            default:
                $this->render_grid_layout( $vagas, $settings, $cid, $api );
                break;
        }

        echo '</div>';
    }

    /**
     * Aplica filtros nas vagas
     *
     * @param array $vagas    Array de vagas
     * @param array $settings Configuracoes do widget
     * @return array Vagas filtradas
     */
    private function apply_filters( $vagas, $settings ): array {
        if ( empty( $vagas ) ) {
            return $vagas;
        }

        $filtered = array();

        foreach ( $vagas as $vaga ) {
            // Filtro por localizacao
            if ( ! empty( $settings['filter_location'] ) ) {
                $filter_location = sanitize_text_field( $settings['filter_location'] );
                $vaga_location   = ! empty( $vaga['localizacao'] ) ? $vaga['localizacao'] : '';

                // Verifica se a localizacao da vaga contem o filtro (case-insensitive)
                if ( stripos( $vaga_location, $filter_location ) === false ) {
                    continue; // Pula esta vaga
                }
            }

            // Filtro por ramo/categoria
            if ( ! empty( $settings['filter_category'] ) ) {
                $filter_category = sanitize_text_field( $settings['filter_category'] );
                $vaga_category   = ! empty( $vaga['ramo_atividade'] ) ? $vaga['ramo_atividade'] : '';

                // Verifica se o ramo da vaga contem o filtro (case-insensitive)
                if ( stripos( $vaga_category, $filter_category ) === false ) {
                    continue; // Pula esta vaga
                }
            }

            // Filtro por numero minimo de vagas
            if ( ! empty( $settings['filter_min_positions'] ) && $settings['filter_min_positions'] > 0 ) {
                $min_positions = absint( $settings['filter_min_positions'] );
                $vaga_positions = isset( $vaga['num_vagas'] ) ? absint( $vaga['num_vagas'] ) : 1;

                if ( $vaga_positions < $min_positions ) {
                    continue; // Pula esta vaga
                }
            }

            // Filtro por data (ultimos X dias)
            if ( ! empty( $settings['filter_days'] ) && $settings['filter_days'] > 0 ) {
                $filter_days = absint( $settings['filter_days'] );

                if ( ! empty( $vaga['data_abertura'] ) ) {
                    $vaga_timestamp = strtotime( $vaga['data_abertura'] );
                    $cutoff_date    = strtotime( '-' . $filter_days . ' days' );

                    if ( $vaga_timestamp && $vaga_timestamp < $cutoff_date ) {
                        continue; // Pula esta vaga
                    }
                }
            }

            // Se passou por todos os filtros, inclui na lista
            $filtered[] = $vaga;
        }

        return $filtered;
    }

    /**
     * Renderiza layout Grid
     *
     * @param array     $vagas    Array de vagas
     * @param array     $settings Configuracoes do widget
     * @param string    $cid      Codigo CID
     * @param Kombo_API $api      Instancia da API
     * @return void
     */
    private function render_grid_layout( $vagas, $settings, $cid, $api ): void {
        $hover_class = 'yes' === $settings['card_hover_transform'] ? 'kombo-card-hover' : '';

        echo '<div class="kombo-vagas-grid" role="list" aria-label="' . esc_attr__( 'Lista de vagas de emprego', 'quadro-vagas-kombo' ) . '">';

        foreach ( $vagas as $vaga ) {
            $application_url = $this->get_application_url( $settings, $cid, $vaga, $api );
            $data_attrs      = $this->build_filter_data_attributes( $vaga );

            echo '<article class="kombo-vaga-card ' . esc_attr( $hover_class ) . '" role="listitem" ' . $data_attrs . '>';

            // Titulo
            echo '<h3 class="kombo-vaga-title">' . esc_html( $vaga['titulo'] ) . '</h3>';

            // Informacoes
            echo '<div class="kombo-vaga-info-wrapper">';
            $this->render_vaga_info( $vaga, $settings );
            echo '</div>';

            // Botao
            $this->render_button( $application_url, $settings );

            echo '</article>';
        }

        echo '</div>';
    }

    /**
     * Renderiza layout Lista
     *
     * @param array     $vagas    Array de vagas
     * @param array     $settings Configuracoes do widget
     * @param string    $cid      Codigo CID
     * @param Kombo_API $api      Instancia da API
     * @return void
     */
    private function render_lista_layout( $vagas, $settings, $cid, $api ): void {
        echo '<div class="kombo-vagas-lista" role="list" aria-label="' . esc_attr__( 'Lista de vagas de emprego', 'quadro-vagas-kombo' ) . '">';

        foreach ( $vagas as $vaga ) {
            $application_url = $this->get_application_url( $settings, $cid, $vaga, $api );
            $data_attrs      = $this->build_filter_data_attributes( $vaga );

            echo '<div class="kombo-vaga-item" role="listitem" ' . $data_attrs . '>';

            echo '<div class="kombo-vaga-content">';
            echo '<h3 class="kombo-vaga-title">' . esc_html( $vaga['titulo'] ) . '</h3>';
            echo '<div class="kombo-vaga-info-wrapper kombo-vaga-info-inline">';
            $this->render_vaga_info( $vaga, $settings );
            echo '</div>';
            echo '</div>';

            echo '<div class="kombo-vaga-action">';
            $this->render_button( $application_url, $settings );
            echo '</div>';

            echo '</div>';
        }

        echo '</div>';
    }

    /**
     * Renderiza layout Accordion
     *
     * @param array     $vagas    Array de vagas
     * @param array     $settings Configuracoes do widget
     * @param string    $cid      Codigo CID
     * @param Kombo_API $api      Instancia da API
     * @return void
     */
    private function render_accordion_layout( $vagas, $settings, $cid, $api ): void {
        $widget_id = $this->get_id();

        echo '<div class="kombo-vagas-accordion" role="list" aria-label="' . esc_attr__( 'Lista de vagas de emprego', 'quadro-vagas-kombo' ) . '">';

        foreach ( $vagas as $index => $vaga ) {
            $application_url = $this->get_application_url( $settings, $cid, $vaga, $api );
            $item_id         = $widget_id . '-' . $index;
            $data_attrs      = $this->build_filter_data_attributes( $vaga );

            echo '<div class="kombo-accordion-item" role="listitem" ' . $data_attrs . '>';

            // Cabecalho do accordion
            echo '<button class="kombo-accordion-header" ';
            echo 'aria-expanded="false" ';
            echo 'aria-controls="kombo-panel-' . esc_attr( $item_id ) . '" ';
            echo 'id="kombo-header-' . esc_attr( $item_id ) . '">';
            echo '<span class="kombo-vaga-title">' . esc_html( $vaga['titulo'] ) . '</span>';
            echo '<span class="kombo-accordion-icon" aria-hidden="true"></span>';
            echo '</button>';

            // Painel do accordion
            echo '<div class="kombo-accordion-panel" ';
            echo 'id="kombo-panel-' . esc_attr( $item_id ) . '" ';
            echo 'role="region" ';
            echo 'aria-labelledby="kombo-header-' . esc_attr( $item_id ) . '" ';
            echo 'hidden>';

            echo '<div class="kombo-vaga-info-wrapper">';
            $this->render_vaga_info( $vaga, $settings );
            echo '</div>';

            if ( ! empty( $vaga['descricao'] ) ) {
                echo '<div class="kombo-vaga-description">';
                echo wp_kses_post( $vaga['descricao'] );
                echo '</div>';
            }

            $this->render_button( $application_url, $settings );

            echo '</div>'; // panel
            echo '</div>'; // item
        }

        echo '</div>';
    }

    /**
     * Renderiza informacoes da vaga
     *
     * @param array $vaga     Dados da vaga
     * @param array $settings Configuracoes do widget
     * @return void
     */
    private function render_vaga_info( $vaga, $settings ): void {
        // Cidade/Localizacao
        if ( 'yes' === $settings['show_cidade'] && ! empty( $vaga['localizacao'] ) ) {
            echo '<span class="kombo-vaga-info kombo-vaga-cidade">';
            echo '<span class="kombo-info-icon" aria-hidden="true">&#128205;</span>';
            echo '<span class="kombo-info-label sr-only">' . esc_html__( 'Localizacao:', 'quadro-vagas-kombo' ) . '</span>';
            echo esc_html( $vaga['localizacao'] );
            echo '</span>';
        }

        // Numero de vagas
        if ( 'yes' === $settings['show_num_vagas'] && ! empty( $vaga['num_vagas'] ) ) {
            echo '<span class="kombo-vaga-info kombo-vaga-num">';
            echo '<span class="kombo-info-icon" aria-hidden="true">&#128101;</span>';
            echo '<span class="kombo-info-label sr-only">' . esc_html__( 'Vagas:', 'quadro-vagas-kombo' ) . '</span>';
            echo sprintf(
                /* translators: %d: numero de vagas */
                esc_html( _n( '%d vaga', '%d vagas', $vaga['num_vagas'], 'quadro-vagas-kombo' ) ),
                $vaga['num_vagas']
            );
            echo '</span>';
        }

        // Ramo de atividade
        if ( 'yes' === $settings['show_ramo'] && ! empty( $vaga['ramo_atividade'] ) ) {
            echo '<span class="kombo-vaga-info kombo-vaga-ramo">';
            echo '<span class="kombo-info-icon" aria-hidden="true">&#128188;</span>';
            echo '<span class="kombo-info-label sr-only">' . esc_html__( 'Area:', 'quadro-vagas-kombo' ) . '</span>';
            echo esc_html( $vaga['ramo_atividade'] );
            echo '</span>';
        }

        // Data de abertura
        if ( 'yes' === $settings['show_data'] && ! empty( $vaga['data_formatada'] ) ) {
            echo '<span class="kombo-vaga-info kombo-vaga-data">';
            echo '<span class="kombo-info-icon" aria-hidden="true">&#128197;</span>';
            echo '<span class="kombo-info-label sr-only">' . esc_html__( 'Data:', 'quadro-vagas-kombo' ) . '</span>';
            echo esc_html( $vaga['data_formatada'] );
            echo '</span>';
        }
    }

    /**
     * Renderiza botao de candidatura
     *
     * @param string $url      URL de destino
     * @param array  $settings Configuracoes do widget
     * @return void
     */
    private function render_button( $url, $settings ): void {
        $button_text = ! empty( $settings['button_text'] )
            ? $settings['button_text']
            : __( 'Candidatar-se', 'quadro-vagas-kombo' );

        echo '<a href="' . esc_url( $url ) . '" ';
        echo 'class="kombo-vaga-button" ';
        echo 'target="_blank" ';
        echo 'rel="noopener noreferrer" ';
        echo 'aria-label="' . esc_attr( sprintf( __( 'Candidatar-se a vaga', 'quadro-vagas-kombo' ) ) ) . '">';
        echo esc_html( $button_text );
        echo '</a>';
    }

    /**
     * Obtem URL de candidatura
     *
     * @param array     $settings Configuracoes do widget
     * @param string    $cid      Codigo CID
     * @param array     $vaga     Dados da vaga
     * @param Kombo_API $api      Instancia da API
     * @return string URL
     */
    private function get_application_url( $settings, $cid, $vaga, $api ): string {
        // Verifica URL customizada
        if ( ! empty( $settings['custom_url']['url'] ) ) {
            $url = $settings['custom_url']['url'];
            // Adiciona codigo da vaga como parametro se disponivel
            if ( ! empty( $vaga['codigo'] ) ) {
                $url = add_query_arg( 'vaga', $vaga['codigo'], $url );
            }
            return esc_url( $url );
        }

        // Usa link proprio da vaga se disponivel
        if ( ! empty( $vaga['link'] ) ) {
            return esc_url( $vaga['link'] );
        }

        // Padrao: pagina de cadastro do Kombo
        return $api->get_application_url( $cid, isset( $vaga['codigo'] ) ? $vaga['codigo'] : '' );
    }

    /**
     * Renderiza mensagem de erro
     *
     * @param string $message Mensagem de erro
     * @return void
     */
    private function render_error( $message ): void {
        // Mostra erros apenas para usuarios com permissao de edicao
        if ( current_user_can( 'edit_posts' ) ) {
            echo '<div class="kombo-vagas-error" role="alert">';
            echo '<p><strong>' . esc_html__( 'Erro:', 'quadro-vagas-kombo' ) . '</strong> ';
            echo esc_html( $message );
            echo '</p></div>';
        }
    }

    /**
     * Renderiza mensagem de ausencia de vagas
     *
     * @param array $settings Configuracoes do widget
     * @return void
     */
    private function render_no_jobs( $settings ): void {
        $message = ! empty( $settings['no_jobs_message'] )
            ? $settings['no_jobs_message']
            : __( 'No momento nao ha vagas disponiveis.', 'quadro-vagas-kombo' );

        echo '<div class="kombo-vagas-empty" role="status">';
        echo '<p>' . esc_html( $message ) . '</p>';
        echo '</div>';
    }
}
