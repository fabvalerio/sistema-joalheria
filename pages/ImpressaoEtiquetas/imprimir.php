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
    body{
        margin: 0;
        padding: 0;
        font-family: 'Times New Roman', Helvetica, sans-serif;
    }
    .etiqueta-preview {
        width: 8.4cm;
        height: 1cm;
        display: inline-block;
        position: relative;
    }
    
    .etiqueta-preview.direita {
        margin-right: 0;
    }
    
    .etiqueta-preview.esquerda {
        margin-left: 0;
    }
    
    .etiqueta-preview-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        max-width: 8.4cm;
        margin: 0 auto;
    }
    
    .area-impressao {
        position: absolute;
        left: 0;
        top: 0;
        width: 4cm;
        height: 1cm;
        display: flex;
        box-sizing: border-box;
    }
    
    .area-impressao.direita {
        left: auto;
        right: 0;
        z-index: 100;
        padding: 1mm 0 0 0;
}

    
    .area-impressao.esquerda {
        left: 5px;
        right: auto;
    }
    
    .area-texto {
        width: 2cm;
        height: 1.1cm;
        padding: 3px 4px 3px 0px;
        font-size: 7pt;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: left;
        word-wrap: break-word;
        overflow: hidden;
        box-sizing: border-box;
    }
    
    .area-barcode {
        width: 2cm;
        height: 1cm;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2px;
        text-align: center;
        font-size: 7px;
        box-sizing: border-box;
        overflow: hidden;
    }
    
    .area-vazia {
        position: absolute;
        right: 0;
        top: 0;
        width: 4cm;
        height: 1cm;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        color: #999;
        font-size: 8pt;
        box-sizing: border-box;
        flex-direction: column;
    }
    
    .area-vazia.direita {
        left: 0;
        right: auto;
    }
    
    .area-vazia.esquerda {
        left: auto;
        right: 0;
    }
    
    .barcode-svg {
        max-width: 51px;
        height: auto;
    }

    .marginTop{
        margin-top: -0.18cm; /* alterei de -0.4cm */
    }

     .folha{
         height: 18mm;
    } 


    /* Configurações específicas para impressão */
    @media print {
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
                    $lado = ($indexGlobal % 2 === 0) ? 'direita' : 'esquerda marginTop';
                    $barcodeId = $produto['id'] . '-' . $i;
                    $encaixeTop = ($indexGlobal > 0 ) ? 'marginTop' : '';
                    if($indexGlobal > 0){
                        $enter = ($indexGlobal % 2 === 0) ? 'marginBottom' : '';
                    }
                    if( $lado == 'direita' ){ echo '<div class="folha">'; }

                    if($indexGlobal % 2 === 0){
            ?>
                    <div class="etiqueta-preview ">
                        <div class="area-impressao <?= $lado ?? '' ?>">
                            <div class="area-texto">
                                <?= htmlspecialchars(resumirTextoEtiqueta($produto['descricao_etiqueta'])) ?>
                            </div>
                            <div class="area-barcode">
                                <div style="font-size: 12px; margin-bottom: 5px;"><?= str_pad($produto['id'], 6, '0', STR_PAD_LEFT) ?></div>
                                <div>
                                    <svg class="barcode-svg" id="barcode-<?= $barcodeId ?>"></svg>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="area-vazia <?= $lado ?>"></div> -->
                    </div>
            <?php 
                    } else {
            ?>
                    <div class="etiqueta-preview ">
                        <div class="area-impressao <?= $lado ?? '' ?>">
                            <div class="area-barcode">
                                <div style="font-size: 12px; margin-bottom: 5px;"><?= str_pad($produto['id'], 6, '0', STR_PAD_LEFT) ?></div>
                                <div>
                                    <svg class="barcode-svg" id="barcode-<?= $barcodeId ?>"></svg>
                                </div>
                            </div>
                            <div class="area-texto">
                                <?= htmlspecialchars(resumirTextoEtiqueta($produto['descricao_etiqueta'])) ?>
                            </div>
                        </div>
                        <!-- <div class="area-vazia <?= $lado ?>"></div> -->
                    </div>
            <?php
                    }
                    $indexGlobal++;
                    if( $lado == 'esquerda' ){ echo '</div>';}
                endfor;
            endforeach; 
            ?>
        </div>


<!-- Biblioteca para gerar código de barras -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11/dist/JsBarcode.all.min.js"></script>

<script>
// Gerar códigos de barras
<?php 
foreach ($produtos as $produto): 
    $quantidade = $produtos_com_quantidade[$produto['id']] ?? 1;
    $ean13 = $controller->gerarEAN13($produto['id']);
    for ($i = 0; $i < $quantidade; $i++):
        $barcodeId = $produto['id'] . '-' . $i;
?>
    JsBarcode("#barcode-<?= $barcodeId ?>", "<?= $ean13 ?>", {
        format: "pharmacode",
        // width: 1.2,
        height: 20,
        displayValue: false,
        // fontSize: 8,
        margin: 0,
        // textMargin: 1,
        // fontOptions: "bold"
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