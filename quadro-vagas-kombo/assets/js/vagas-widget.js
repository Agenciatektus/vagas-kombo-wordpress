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
     * Inicializa funcionalidade de filtros frontend
     */
    function initKomboFilters() {
        var filterWrappers = document.querySelectorAll('.kombo-filters-wrapper');

        filterWrappers.forEach(function(filterWrapper) {
            // Pula se ja foi inicializado
            if (filterWrapper.dataset.komboFiltersInitialized) {
                return;
            }

            filterWrapper.dataset.komboFiltersInitialized = 'true';

            // Obtem widget wrapper
            var widgetWrapper = filterWrapper.closest('.kombo-vagas-wrapper');
            if (!widgetWrapper) {
                return;
            }

            // Obtem elementos de filtro
            var locationInput = filterWrapper.querySelector('.kombo-filter-location');
            var areaInput = filterWrapper.querySelector('.kombo-filter-area');
            var resetButton = filterWrapper.querySelector('.kombo-filter-reset');
            var resultsCounter = filterWrapper.querySelector('.kombo-filter-count');

            // Obtem todos os itens de vaga
            var jobItems = widgetWrapper.querySelectorAll(
                '.kombo-vaga-card, .kombo-vaga-item, .kombo-accordion-item'
            );

            var totalJobs = jobItems.length;

            /**
             * Aplica filtros aos itens de vaga
             */
            function applyFilters() {
                var locationValue = locationInput ? locationInput.value.toLowerCase().trim() : '';
                var areaValue = areaInput ? areaInput.value.toLowerCase().trim() : '';

                var visibleCount = 0;

                jobItems.forEach(function(item) {
                    var location = (item.getAttribute('data-location') || '').toLowerCase();
                    var city = (item.getAttribute('data-city') || '').toLowerCase();
                    var state = (item.getAttribute('data-state') || '').toLowerCase();
                    var area = (item.getAttribute('data-area') || '').toLowerCase();

                    var matchesLocation = true;
                    var matchesArea = true;

                    // Filtro de localizacao (verifica cidade, estado ou localizacao completa)
                    if (locationValue) {
                        matchesLocation =
                            location.indexOf(locationValue) !== -1 ||
                            city.indexOf(locationValue) !== -1 ||
                            state.indexOf(locationValue) !== -1;
                    }

                    // Filtro de area
                    if (areaValue) {
                        matchesArea = area.indexOf(areaValue) !== -1;
                    }

                    // Mostra/esconde item baseado nos filtros
                    var matches = matchesLocation && matchesArea;

                    if (matches) {
                        item.style.display = '';
                        item.removeAttribute('hidden');
                        item.removeAttribute('aria-hidden');
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                        item.setAttribute('hidden', '');
                        item.setAttribute('aria-hidden', 'true');
                    }
                });

                // Atualiza contador de resultados
                updateResultsCounter(visibleCount, totalJobs);
            }

            /**
             * Atualiza contador de resultados
             */
            function updateResultsCounter(visible, total) {
                if (!resultsCounter) {
                    return;
                }

                if (visible === total) {
                    resultsCounter.textContent = total + ' vagas encontradas';
                } else {
                    resultsCounter.textContent = visible + ' de ' + total + ' vagas';
                }
            }

            /**
             * Limpa todos os filtros
             */
            function resetFilters() {
                if (locationInput) {
                    locationInput.value = '';
                }
                if (areaInput) {
                    areaInput.value = '';
                }
                applyFilters();

                // Foca no primeiro campo de filtro
                if (locationInput) {
                    locationInput.focus();
                } else if (areaInput) {
                    areaInput.focus();
                }
            }

            // Event listeners
            if (locationInput) {
                locationInput.addEventListener('input', applyFilters);
                locationInput.addEventListener('keyup', function(e) {
                    if (e.key === 'Escape') {
                        resetFilters();
                    }
                });
            }

            if (areaInput) {
                areaInput.addEventListener('input', applyFilters);
                areaInput.addEventListener('keyup', function(e) {
                    if (e.key === 'Escape') {
                        resetFilters();
                    }
                });
            }

            if (resetButton) {
                resetButton.addEventListener('click', resetFilters);
            }

            // Inicializa contador
            updateResultsCounter(totalJobs, totalJobs);
        });
    }

    /**
     * Inicializa quando o DOM esta pronto
     */
    $(document).ready(function() {
        initKomboAccordion();
        initKomboFilters();
    });

    /**
     * Re-inicializa apos o frontend do Elementor estar pronto (para preview no editor)
     */
    $(window).on('elementor/frontend/init', function() {
        if (typeof elementorFrontend !== 'undefined') {
            elementorFrontend.hooks.addAction('frontend/element_ready/kombo-vagas.default', function($element) {
                initKomboAccordion();
                initKomboFilters();
            });
        }
    });

    /**
     * Expoe funcoes para re-inicializacao externa se necessario
     */
    window.KomboVagas = window.KomboVagas || {};
    window.KomboVagas.initAccordion = initKomboAccordion;
    window.KomboVagas.initFilters = initKomboFilters;

})(jQuery);
