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

<style>
    .etiqueta-preview {
        width: 8cm;
        height: 1.1cm;
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
        max-width: 8.5cm;
    }
    
    .area-impressao {
        position: absolute;
        left: 0;
        top: 0;
        width: 4cm;
        height: 1.1cm;
        display: flex;
        box-sizing: border-box;
    }
    
    .area-impressao.direita {
        left: auto;
        right: 0;
        z-index: 100;
    }
    
    .area-impressao.esquerda {
        left: 0;
        right: auto;
    }
    
    .area-texto {
        width: 2cm;
        height: 1.1cm;
        padding: 3px;
        font-size: 5pt;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        word-wrap: break-word;
        overflow: hidden;
        box-sizing: border-box;
    }
    
    .area-barcode {
        width: 2cm;
        height: 1.1cm;
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
        height: 1.1cm;
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
        margin-top: -0.4cm;
    }

    .marginBottom{
        margin-top: 0.4cm!important;
    }

</style>

<div>
    <div>

        <div class="etiqueta-preview-container">
            <?php 
            $indexGlobal = 0;
            foreach ($produtos as $produto): 
                $quantidade = $produtos_com_quantidade[$produto['id']] ?? 1;
                $ean13 = $controller->gerarEAN13($produto['id']);
                
                // Gerar N etiquetas conforme quantidade
                for ($i = 0; $i < $quantidade; $i++): 
                    $lado = ($indexGlobal % 2 === 0) ? 'direita' : 'esquerda';
                    $barcodeId = $produto['id'] . '-' . $i;

                    $encaixeTop = ($indexGlobal > 0 ) ? 'marginTop' : '';
                    if($indexGlobal > 0){
                        $enter = ($indexGlobal % 2 === 0) ? 'marginBottom' : '';
                    }
            ?>
                    <div class="etiqueta-preview <?= $lado ?? '' ?> <?= $encaixeTop ?? '' ?> <?= $enter ?? '' ?>">
                        <div class="area-impressao <?= $lado ?? '' ?>">
                            <div class="area-texto">
                                <?= htmlspecialchars(resumirTextoEtiqueta($produto['descricao_etiqueta'])) ?>
                            </div>
                            <div class="area-barcode">
                                <div><?= $produto['id'] ?></div>
                                <div>
                                    <svg class="barcode-svg" id="barcode-<?= $barcodeId ?>"></svg>
                                </div>
                            </div>
                        </div>
                        <div class="area-vazia <?= $lado ?>">
                           
                        </div>
                    </div>
            <?php 
                    $indexGlobal++;
                endfor;
            endforeach; 
            ?>
        </div>

    </div>
</div>

<!-- Biblioteca para gerar código de barras -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

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
        format: "EAN13",
        width: 1,
        height: 40,
        displayValue: true,
        fontSize: 10,
        margin: 0
    });
<?php 
    endfor;
endforeach; 
?>


// Função para imprimir
    window.print();
</script>
