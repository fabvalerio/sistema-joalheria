<?php
// Garante que não há saída antes do início da sessão
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inicia a sessão apenas se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está autenticado
if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
    // Redireciona para a página de login se não estiver autenticado
    $url = "https://" . $_SERVER['HTTP_HOST'] . "/sistema-joias/";
    header("Location: " . $url . "login.php");
    exit();
}

$dir = '../../';

// Incluir arquivos necessários APÓS verificar a sessão
include $dir.'db/db.class.php';
include $dir.'App/php/htaccess.php';
include $dir.'App/php/function.php';
include $dir.'App/php/notify.php';

// Controlador e ação padrão
$controller = $_GET['controller'] ?? 'Home';
$action = $_GET['action'] ?? 'index';

// Finaliza o buffer de saída para evitar erros
ob_end_flush();



use App\Models\ImpressaoEtiquetas\Controller;

$controller = new Controller();

// Pegar IDs da URL
$ids_string = isset($_GET['ids']) ? $_GET['ids'] : '';
$produtos_com_quantidade = [];
$ids = [];

// Processar formato id:quantidade ou apenas id (retrocompatibilidade)
if (!empty($ids_string)) {
    $items = explode(',', $ids_string);
    foreach ($items as $item) {
        $item = trim($item);
        if (strpos($item, ':') !== false) {
            // Formato novo: id:quantidade
            list($id, $qty) = explode(':', $item);
            $id = intval($id);
            $qty = max(1, min(999, intval($qty))); // Limitar entre 1 e 999
            if ($id > 0) {
                $produtos_com_quantidade[$id] = $qty;
                $ids[] = $id;
            }
        } else {
            // Formato antigo: apenas id (assume quantidade 1)
            $id = intval($item);
            if ($id > 0) {
                $produtos_com_quantidade[$id] = 1;
                $ids[] = $id;
            }
        }
    }
}

if (empty($ids)) {
    echo '<div class="alert alert-warning">Nenhum produto selecionado!</div>';
    echo '<a href="' . $url . '!/' . $link[1] . '/listar" class="btn btn-primary">Voltar</a>';
    exit;
}

// Buscar produtos
$produtos = $controller->buscarPorIds($ids);

if (empty($produtos)) {
    echo '<div class="alert alert-danger">Produtos não encontrados!</div>';
    echo '<a href="' . $url . '!/' . $link[1] . '/listar" class="btn btn-primary">Voltar</a>';
    exit;
}

// Função para resumir texto da etiqueta
function resumirTextoEtiqueta($texto) {
    $palavras = explode(' ', $texto);
    $palavrasResumidas = array_map(function($palavra) {
        if (mb_strlen($palavra) > 6) {
            return mb_substr($palavra, 0, 6) . '.';
        }
        return $palavra;
    }, $palavras);
    return implode(' ', $palavrasResumidas);
}

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impressão de Etiquetas</title>

<style>
    /* Configuração para impressora Elgin L42Pro - Etiquetas 40mm x 25mm */
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }
    
    @page {
        size: 40mm 25mm; /* Volta para tamanho individual */
        margin: 0;
    }
    
    .etiqueta-preview-container {
        display: block;
        width: 40mm; /* Largura para uma única coluna */
    }
    
    .etiqueta-preview {
        width: 40mm;
        height: 25mm;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding: 1.5mm;
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
        page-break-after: always; /* Quebra página após cada etiqueta */
    }
    
    /* Etiqueta esquerda: texto à esquerda, barcode à direita */
    .etiqueta-preview.esquerda {
        flex-direction: row;
    }
    
    /* Etiqueta direita: barcode à esquerda, texto à direita */
    .etiqueta-preview.direita {
        flex-direction: row-reverse;
    }
    
    /* Remove regra anterior de quebra a cada 2 */
    .etiqueta-preview:last-child {
        page-break-after: auto;
    }
    
    .area-texto {
        width: 18mm;
        flex-shrink: 0;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    
    .texto-principal {
        font-size: 7pt;
        font-weight: bold;
        line-height: 1.1;
        margin-bottom: 1mm;
        word-wrap: break-word;
    }
    
    .texto-codigo {
        font-size: 6pt;
        font-weight: normal;
        margin-top: 0.5mm;
    }
    
    .area-barcode {
        width: 19mm;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .barcode-svg {
        max-width: 19mm;
        height: auto;
        max-height: 18mm;
    }

    /* Configurações específicas para impressão */
    @media print {
        body {
            margin: 0;
            padding: 0;
        }
        
        .etiqueta-preview {
            margin: 0;
            border: none;
        }
        
        /* Remove qualquer margem adicional */
        .etiqueta-preview-container {
            margin: 0;
            padding: 0;
        }
    }

</style>

</head>
<body>


        <div class="etiqueta-preview-container">
            <?php 
            $indexGlobal = 0;
            foreach ($produtos as $produto): 
                $quantidade = $produtos_com_quantidade[$produto['id']] ?? 1;
                $ean13 = $controller->gerarEAN13($produto['id']);
                
                // Gerar N etiquetas conforme quantidade
                for ($i = 0; $i < $quantidade; $i++): 
                    $barcodeId = $produto['id'] . '-' . $i;
                    $lado = ($indexGlobal % 2 === 0) ? 'esquerda' : 'direita';
            ?>
                    <div class="etiqueta-preview <?= $lado ?>">
                        <div class="area-texto">
                            <div class="texto-principal">
                                <?= htmlspecialchars(resumirTextoEtiqueta($produto['descricao_etiqueta'])) ?>
                            </div>
                            <div class="texto-codigo">
                                <?= htmlspecialchars($produto['id']) ?>
                            </div>
                        </div>
                        <div class="area-barcode">
                            <svg class="barcode-svg" id="barcode-<?= $barcodeId ?>"></svg>
                        </div>
                    </div>
            <?php 
                    $indexGlobal++;
                endfor;
            endforeach; 
            ?>
        </div>


<!-- Biblioteca para gerar código de barras -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
// Gerar códigos de barras otimizados para Elgin L42Pro
<?php 
foreach ($produtos as $produto): 
    $quantidade = $produtos_com_quantidade[$produto['id']] ?? 1;
    $ean13 = $controller->gerarEAN13($produto['id']);
    for ($i = 0; $i < $quantidade; $i++):
        $barcodeId = $produto['id'] . '-' . $i;
?>
    JsBarcode("#barcode-<?= $barcodeId ?>", "<?= $ean13 ?>", {
        format: "EAN13",
        width: 1.2,
        height: 40,
        displayValue: true,
        fontSize: 8,
        margin: 0,
        textMargin: 0.5,
        fontOptions: "bold"
    });
<?php 
    endfor;
endforeach; 
?>


// Função para imprimir
function imprimirEtiquetas() {
    // Aguarda um pouco para garantir que os códigos de barras foram gerados
    setTimeout(() => {
        window.print();
    }, 500);
}

// Chama a função de impressão após o DOM estar carregado
if (document.readyState === 'complete') {
    imprimirEtiquetas();
} else {
    window.addEventListener('load', imprimirEtiquetas);
}
</script>

</body>
</html>