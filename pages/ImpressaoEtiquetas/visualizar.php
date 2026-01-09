<?php

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
        padding: 0.25cm;
        background: #f5f5f5;
        width: 8.5cm;
        margin: 0 auto;
    }
    
    .area-impressao {
        position: absolute;
        left: 0;
        top: 0;
        width: 4cm;
        height: 1.1cm;
        display: flex;
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 8px;
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
        border-right: 1px solid #ddd;
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
    
    .border-etiqueta{
        border: 1px solid #ccc;
        width: 100%;
        font-size: 8px;
        height: 0.3cm;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: #fff;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .border-etiqueta2{
        height: 0.4cm;
        border: 1px solid #ccc;
        width: 100%;
        border-radius: 4px;
    }
</style>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <i class="fas fa-eye"></i> Visualização das Etiquetas
        </h3>
        <?php 
        $totalEtiquetas = array_sum($produtos_com_quantidade);
        $totalProdutos = count($produtos);
        ?>
        <span class="badge bg-light text-primary"><?= $totalProdutos ?> produto(s), <?= $totalEtiquetas ?> etiqueta(s)</span>
    </div>

    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Informações:</strong>
            <ul class="mb-0 mt-2">
                <li>Cada etiqueta tem <strong>8cm de largura</strong> por <strong>2cm de altura</strong></li>
                <li>A área de impressão ocupa os primeiros <strong>4cm</strong> (metade esquerda)</li>
                <li>Os 4cm são divididos em: <strong>2cm para o texto</strong> + <strong>2cm para o código de barras</strong></li>
                <li>A metade direita (4cm) permanece em branco</li>
            </ul>
        </div>

        <div class="text-center mb-3">
            <a href="<?= $url ?>!/<?= $link[1] ?>/listar" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <button onclick="imprimirEtiquetas()" class="btn btn-success btn-lg">
                <i class="fas fa-print"></i> Imprimir Etiquetas
            </button>
        </div>

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
                    <div class="etiqueta-preview <?= $lado ?> <?= $encaixeTop ?> <?= $enter ?>">
                        <div class="area-impressao <?= $lado ?>">
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
                            <?php echo ($indexGlobal % 2 === 0) ? '<div class="border-etiqueta2"></div>' : '<div class="border-etiqueta2"></div>';   ?>
                            <div class="border-etiqueta">
                                (área em branco)
                            </div>
                            <?php echo ($indexGlobal % 2 === 0) ? '' : '<div class="border-etiqueta2"></div>';   ?>
                            
                        </div>
                    </div>
            <?php 
                    $indexGlobal++;
                endfor;
            endforeach; 
            ?>
        </div>

        <div class="text-center mt-3">
            <button onclick="imprimirEtiquetas()" class="btn btn-success btn-lg">
                <i class="fas fa-print"></i> Imprimir Etiquetas
            </button>
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
function imprimirEtiquetas() {
    const ids = '<?= $ids_string ?>';
    window.open('<?= $url ?>pages/<?= $link[1] ?>/imprimir.php?ids=' + ids, '_new');
}
</script>
