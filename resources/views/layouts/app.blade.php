    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('open-url-in-new-tab', event => {
                window.open(event.detail.url, '_blank');
            });

            // Aplicar los porcentajes a las barras de progreso
            setTimeout(function() {
                const cards = document.querySelectorAll('.filament-stats-card');
                cards.forEach(card => {
                    if (card.classList.contains('combustible-card')) {
                        const descText = card.querySelector('.filament-stats-card-description')
                            .textContent;
                        const percent = parseFloat(descText);
                        card.style.setProperty('--percent-width', percent + '%');
                    }
                    if (card.classList.contains('electricidad-card')) {
                        const descText = card.querySelector('.filament-stats-card-description')
                            .textContent;
                        const percent = parseFloat(descText);
                        card.style.setProperty('--percent-width', percent + '%');
                    }
                    if (card.classList.contains('residuos-card')) {
                        const descText = card.querySelector('.filament-stats-card-description')
                            .textContent;
                        const percent = parseFloat(descText);
                        card.style.setProperty('--percent-width', percent + '%');
                    }
                });
            }, 500);
        });
    </script>

    <style>
        /* Estilos para los widgets de huella de carbono */
        .filament-stats-card {
            position: relative;
            overflow: hidden;
        }

        .filament-stats-card .filament-stats-card-description {
            font-weight: 500;
        }

        .combustible-card::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            background-color: rgb(245, 158, 11);
            width: var(--percent-width, 0%);
            transition: width 1s ease-in-out;
        }

        .electricidad-card::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            background-color: rgb(239, 68, 68);
            width: var(--percent-width, 0%);
            transition: width 1s ease-in-out;
        }

        .residuos-card::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            background-color: rgb(107, 114, 128);
            width: var(--percent-width, 0%);
            transition: width 1s ease-in-out;
        }
    </style>
