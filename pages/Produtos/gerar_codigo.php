
        <script src="https://cdn.jsdelivr.net/jsbarcode/3.6.0/JsBarcode.all.min.js"></script>
        <script>
            window.onload = function() {
                // Captura o parâmetro da URL
                var urlParams = new URLSearchParams(window.location.search);
                var codigo = urlParams.get('codigo'); // Substitua 'codigo' pelo nome do parâmetro desejado

                // Se o parâmetro "codigo" existir, gera o código de barras automaticamente
                if(codigo) {
                    JsBarcode('#codBarras', codigo);
                }
            };
        </script>
        <svg id="codBarras"></svg>
