    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('open-url-in-new-tab', event => {
                window.open(event.detail.url, '_blank');
            });
        });
    </script>
