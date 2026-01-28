/**
 * Quadro de Vagas Kombo - JavaScript do Frontend
 *
 * Funcionalidade do accordion com suporte a acessibilidade.
 *
 * @package Quadro_Vagas_Kombo
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Inicializa funcionalidade do accordion Kombo Vagas
     */
    function initKomboAccordion() {
        // Obtem todos os cabecalhos de accordion
        var accordionHeaders = document.querySelectorAll('.kombo-accordion-header');

        accordionHeaders.forEach(function(header) {
            // Pula se ja foi inicializado
            if (header.dataset.komboInitialized) {
                return;
            }

            header.dataset.komboInitialized = 'true';

            header.addEventListener('click', function(event) {
                event.preventDefault();
                toggleAccordionPanel(this);
            });

            // Suporte a teclado
            header.addEventListener('keydown', function(event) {
                handleAccordionKeydown(event, this);
            });
        });
    }

    /**
     * Alterna painel do accordion
     *
     * @param {HTMLElement} header - Elemento do cabecalho do accordion
     */
    function toggleAccordionPanel(header) {
        var panel = document.getElementById(header.getAttribute('aria-controls'));
        var isExpanded = header.getAttribute('aria-expanded') === 'true';

        // Alterna estado
        header.setAttribute('aria-expanded', !isExpanded);

        if (isExpanded) {
            // Fecha painel
            panel.setAttribute('hidden', '');
        } else {
            // Abre painel
            panel.removeAttribute('hidden');

            // Opcional: Fecha outros paineis no mesmo accordion
            var accordion = header.closest('.kombo-vagas-accordion');
            if (accordion) {
                var otherHeaders = accordion.querySelectorAll('.kombo-accordion-header');
                otherHeaders.forEach(function(otherHeader) {
                    if (otherHeader !== header && otherHeader.getAttribute('aria-expanded') === 'true') {
                        var otherPanel = document.getElementById(otherHeader.getAttribute('aria-controls'));
                        otherHeader.setAttribute('aria-expanded', 'false');
                        if (otherPanel) {
                            otherPanel.setAttribute('hidden', '');
                        }
                    }
                });
            }
        }
    }

    /**
     * Manipula navegacao por teclado do accordion
     *
     * @param {KeyboardEvent} event - Evento de teclado
     * @param {HTMLElement} header - Elemento do cabecalho do accordion
     */
    function handleAccordionKeydown(event, header) {
        var accordion = header.closest('.kombo-vagas-accordion');
        if (!accordion) {
            return;
        }

        var headers = Array.from(accordion.querySelectorAll('.kombo-accordion-header'));
        var currentIndex = headers.indexOf(header);
        var targetIndex = -1;

        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                targetIndex = (currentIndex + 1) % headers.length;
                break;

            case 'ArrowUp':
                event.preventDefault();
                targetIndex = (currentIndex - 1 + headers.length) % headers.length;
                break;

            case 'Home':
                event.preventDefault();
                targetIndex = 0;
                break;

            case 'End':
                event.preventDefault();
                targetIndex = headers.length - 1;
                break;

            case 'Enter':
            case ' ':
                event.preventDefault();
                toggleAccordionPanel(header);
                break;
        }

        if (targetIndex >= 0) {
            headers[targetIndex].focus();
        }
    }

    /**
     * Inicializa quando o DOM esta pronto
     */
    $(document).ready(function() {
        initKomboAccordion();
    });

    /**
     * Re-inicializa apos o frontend do Elementor estar pronto (para preview no editor)
     */
    $(window).on('elementor/frontend/init', function() {
        if (typeof elementorFrontend !== 'undefined') {
            elementorFrontend.hooks.addAction('frontend/element_ready/kombo-vagas.default', function($element) {
                initKomboAccordion();
            });
        }
    });

    /**
     * Expoe funcao para re-inicializacao externa se necessario
     */
    window.KomboVagas = window.KomboVagas || {};
    window.KomboVagas.initAccordion = initKomboAccordion;

})(jQuery);
