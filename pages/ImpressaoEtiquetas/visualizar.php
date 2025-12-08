<?php

use App\Models\ImpressaoEtiquetas\Controller;

$controller = new Controller();

// Pegar IDs da URL
$ids_string = isset($_GET['ids']) ? $_GET['ids'] : '';
$ids = array_filter(explode(',', $ids_string), 'is_numeric');

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
        border: 1px solid #ccc;
        margin: 10px;
        display: inline-block;
        position: relative;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .etiqueta-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
        padding: 20px;
        background: #f5f5f5;
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
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2px;
    }
    
    .area-vazia {
        position: absolute;
        right: 0;
        top: 0;
        width: 4cm;
        height: 2cm;
        background: #f9f9f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 8pt;
    }
    
    .barcode-svg {
        width: 100%;
        height: 100%;
    }
</style>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <i class="fas fa-eye"></i> Visualização das Etiquetas
        </h3>
        <span class="badge bg-light text-primary"><?= count($produtos) ?> etiqueta(s)</span>
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
            <?php foreach ($produtos as $produto): ?>
                <?php
                $ean13 = $controller->gerarEAN13($produto['id']);
                ?>
                <div class="etiqueta-preview">
                    <div class="area-impressao">
                        <div class="area-texto">
                            <?= htmlspecialchars(resumirTextoEtiqueta($produto['descricao_etiqueta'])) ?>
                        </div>
                        <div class="area-barcode">
                            <svg class="barcode-svg" id="barcode-<?= $produto['id'] ?>"></svg>
                        </div>
                    </div>
                    <div class="area-vazia">
                        (área em branco)
                    </div>
                </div>
            <?php endforeach; ?>
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
<?php foreach ($produtos as $produto): ?>
    <?php $ean13 = $controller->gerarEAN13($produto['id']); ?>
    JsBarcode("#barcode-<?= $produto['id'] ?>", "<?= $ean13 ?>", {
        format: "EAN13",
        width: 1,
        height: 40,
        displayValue: true,
        fontSize: 10,
        margin: 0
    });
<?php endforeach; ?>

// Função para imprimir
function imprimirEtiquetas() {
    const ids = '<?= implode(',', $ids) ?>';
    window.open('<?= $url ?>!/<?= $link[1] ?>/imprimir&ids=' + ids, '_blank');
}
</script>
