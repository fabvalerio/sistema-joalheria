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
        height: 2cm;
        /* border: 1px solid #ccc; */
        margin: 10px;
        display: inline-block;
        position: relative;
        background: white;
        /* box-shadow: 0 2px 4px rgba(0,0,0,0.1); */
    }
    
    .etiqueta-preview.direita {
        margin-right: 0;
        margin-left: auto;
    }
    
    .etiqueta-preview.esquerda {
        margin-left: 0;
        margin-right: auto;
    }
    
    .etiqueta-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
        padding: 20px;
        /* background: #f5f5f5; */
        width: 350px;
        margin: 0 auto;
    }
    
    .area-impressao {
        position: absolute;
        left: 0;
        top: 0;
        width: 4cm;
        height: 2cm;
        border-right: 1px dashed #999;
        display: flex;
    }
    
    .area-impressao.direita {
        left: auto;
        right: 0;
        border-right: none;
        border-left: 1px dashed #999;
    }
    
    .area-impressao.esquerda {
        left: 0;
        right: auto;
        border-right: 1px dashed #999;
        border-left: none;
    }
    
    .area-texto {
        width: 2cm;
        height: 1.9cm;
        padding: 5px;
        font-size: 6pt;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        word-wrap: break-word;
        overflow: hidden;
        border-right: 1px solid #ddd;
    }
    
    .area-barcode {
        width: 2cm;
        height: 2cm;
        /* display: flex; */
        align-items: center;
        justify-content: center;
        padding: 2px;
        text-align: center;
        font-size: 12px;
    }
    
    .area-vazia {
        position: absolute;
        right: 0;
        top: 0;
        width: 4cm;
        height: 2cm;
        /* background: #f9f9f9; */
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 8pt;
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
        width: 100%;
        height: 60%;
    }
    
    @media print {
        body * {
            visibility: hidden;
        }
        .etiqueta-preview-container,
        .etiqueta-preview-container * {
            visibility: visible;
        }
        .etiqueta-preview-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            margin: 0;
        }
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

        <div class="text-center mb-3">
            <a href="<?= $url ?>!/<?= $link[1] ?>/listar" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <button onclick="imprimirEtiquetas()" class="btn btn-success btn-lg">
                <i class="fas fa-print"></i> Confirmar Impressão
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
            ?>
                    <div class="etiqueta-preview <?= $lado ?>">
                        <div class="area-impressao <?= $lado ?>">
                            <div class="area-texto">
                                <?= htmlspecialchars(resumirTextoEtiqueta($produto['descricao_etiqueta'])) ?>
                            </div>
                            <div class="area-barcode">
                                <div><?= $produto['id'] ?></div>
                                <svg class="barcode-svg" id="barcode-<?= $barcodeId ?>"></svg>
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
    window.print();
}
</script>
