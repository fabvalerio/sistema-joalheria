<?php

use App\Models\Produtos\Controller;

// ID do produto a ser visualizado
$id = $link[3];

// Instanciar o Controller
$controller = new Controller();

// Buscar os dados do produto
$produto = $controller->ver($id);

// Verificar se o produto foi encontrado
if (!$produto) {
    echo notify('danger', "Produto não encontrado.");
    exit;
}


$idProduto = isset($id) ? $id : '000000';
?>

<style>
        .codigo-container {
            margin-top: 50px;
        }
        .btn-imprimir {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn-imprimir:hover {
            background-color: #218838;
        }

        #etiqueta{
            width: 42mm;
            height: 25mm;
            border: 1px solid black;
        }

    </style>


<h2>Código de Barras para o Produto: <?php echo htmlspecialchars($idProduto); ?></h2>

<div class="codigo-container">
    
        <script src="https://cdn.jsdelivr.net/jsbarcode/3.6.0/JsBarcode.all.min.js"></script>
        <script>
            window.onload = function() {
                var codigo = '<?php echo $idProduto; ?>';
                if(codigo) {
                    JsBarcode('#codBarras', codigo, {
                            background: null,        // Remove fundo branco
                            margin: 0,               // Remove margens
                            marginTop: 0,
                            marginBottom: 0,
                            marginLeft: 0,
                            marginRight: 0,
                            lineColor: "#000000",    // Cor das barras
                            width: 0.8,                // Diminui a espessura das linhas (pode ajustar entre 0.8 e 1.5)
                            height: 40,              // Altura do código de barras (ajuste conforme necessário)
                            displayValue: false      // Oculta os números abaixo do código, se quiser deixar visual mais limpo
                        });
                }
            };

            
        </script>
            <p>Etiqueta 42mm x 12mm</p>
            <p>Antes de imprimir, verifica se está na impressora correta.</p>
        <div id="etiqueta" class="align-self-center">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-6 text-center"><svg id="codBarras"></svg></div>
                <div class="col-6" style="font-size: 12px;">Código <br> <h2><?php echo $produto['id']; ?></h2></div>
            </div>
        </div>
</div>

<button class="btn-imprimir btn-primary" onclick="imprimirEtiqueta();">Imprimir</button>

<script>
function imprimirEtiqueta() {
    // Obter o conteúdo da etiqueta
    var conteudo = document.getElementById("etiqueta").innerHTML;
    
    // Abrir uma nova janela
    var janelaImpressao = window.open('', '', 'width=600,height=400');
    
    // Escrever o conteúdo da etiqueta na nova janela
    janelaImpressao.document.write('<html><head><title>Imprimir Etiqueta</title>');
    // Se necessário, insira aqui estilos específicos para impressão
    janelaImpressao.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">');
    janelaImpressao.document.write('<style>body{margin:0; padding:0;} .etiqueta{width:42mm; height:25mm; display:flex; align-items:center; justify-content:center;}</style>');
    janelaImpressao.document.write('</head><body>');
    janelaImpressao.document.write('<div class="etiqueta">' + conteudo + '</div>');
    janelaImpressao.document.write('</body></html>');
    
    // Fechar a escrita do documento e focar na janela
    janelaImpressao.document.close();
    janelaImpressao.focus();
    
    // Aguardar um instante para garantir o carregamento do conteúdo e, então, imprimir
    setTimeout(function() {
        janelaImpressao.print();
        janelaImpressao.close();
    }, 500);
}
</script>