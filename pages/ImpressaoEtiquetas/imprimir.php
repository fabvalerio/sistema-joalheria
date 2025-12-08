<?php

use App\Models\ImpressaoEtiquetas\Controller;

$controller = new Controller();

// Pegar IDs da URL
$ids_string = isset($_GET['ids']) ? $_GET['ids'] : '';
$ids = array_filter(explode(',', $ids_string), 'is_numeric');

if (empty($ids)) {
    echo '<div class="alert alert-warning">Nenhum produto selecionado!</div>';
    exit;
}

// Buscar produtos
$produtos = $controller->buscarPorIds($ids);

if (empty($produtos)) {
    echo '<div class="alert alert-danger">Produtos não encontrados!</div>';
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

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impressão de Etiquetas - JoiaRara</title>
    
    <style>
        /* Reset e configurações gerais */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: white;
        }
        
        /* Container das etiquetas */
        .etiquetas-container {
            width: 100%;
            padding: 10mm;
        }
        
        /* Cada etiqueta */
        .etiqueta {
            width: 80mm; /* 8cm */
            height: 20mm; /* 2cm */
            display: inline-block;
            position: relative;
            page-break-inside: avoid;
            margin-bottom: 5mm;
            border: 1px dashed #ccc;
        }
        
        /* Área de impressão (metade esquerda) */
        .area-impressao {
            position: absolute;
            left: 0;
            top: 0;
            width: 40mm; /* 4cm */
            height: 20mm; /* 2cm */
            display: flex;
            border-right: 1px solid #000;
        }
        
        /* Área do texto (2cm) */
        .area-texto {
            width: 20mm; /* 2cm */
            height: 19mm; /* 1.9cm */
            padding: 2mm;
            font-size: 6pt;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            word-wrap: break-word;
            overflow: hidden;
            line-height: 1.2;
            border-right: 1px solid #ddd;
        }
        
        /* Área do código de barras (2cm) */
        .area-barcode {
            width: 20mm; /* 2cm */
            height: 20mm; /* 2cm */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1mm;
        }
        
        .barcode-svg {
            max-width: 100%;
            max-height: 100%;
        }
        
        /* Área vazia (metade direita) */
        .area-vazia {
            position: absolute;
            right: 0;
            top: 0;
            width: 40mm; /* 4cm */
            height: 20mm; /* 2cm */
        }
        
        /* Botões de controle (ocultar na impressão) */
        .controles-impressao {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 20px;
            border: 2px solid #007bff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        
        .btn {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        /* Estilos para impressão */
        @media print {
            /* Ocultar tudo que não seja o container das etiquetas */
            body * {
                visibility: hidden;
            }
            
            /* Tornar visível apenas o container das etiquetas e seus filhos */
            .etiquetas-container,
            .etiquetas-container * {
                visibility: visible;
            }
            
            /* Posicionar o container no topo da página */
            .etiquetas-container {
                position: absolute;
                left: 0;
                top: 0;
                padding: 0;
                width: 100%;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
            
            .etiqueta {
                border: none;
                margin: 0;
                page-break-inside: avoid;
            }
            
            .controles-impressao {
                display: none !important;
            }
            
            /* Garantir que as dimensões sejam respeitadas */
            @page {
                size: auto;
                margin: 5mm;
            }
        }
    </style>
</head>
<body>
    <!-- Controles de impressão -->
    <div class="controles-impressao">
        <h4 style="margin-bottom: 15px;">Controles de Impressão</h4>
        <div>
            <button onclick="window.print()" class="btn btn-success">
                🖨️ Imprimir
            </button>
        </div>
        <div>
            <button onclick="window.close()" class="btn btn-primary">
                ← Fechar
            </button>
        </div>
        <div style="margin-top: 15px; font-size: 12px; color: #666;">
            <strong><?= count($produtos) ?></strong> etiqueta(s)<br>
            <small>Configure a impressora para modo paisagem</small>
        </div>
    </div>

    <!-- Container das etiquetas -->
    <div class="etiquetas-container">
        <?php foreach ($produtos as $produto): ?>
            <?php
            $ean13 = $controller->gerarEAN13($produto['id']);
            ?>
            <div class="etiqueta">
                <!-- Área de impressão (4cm) -->
                <div class="area-impressao">
                    <!-- Texto do produto (2cm) -->
                    <div class="area-texto">
                        <?= htmlspecialchars(resumirTextoEtiqueta($produto['descricao_etiqueta'])) ?>
                    </div>
                    
                    <!-- Código de barras (2cm) -->
                    <div class="area-barcode">
                        <svg class="barcode-svg" id="barcode-<?= $produto['id'] ?>"></svg>
                    </div>
                </div>
                
                <!-- Área vazia (4cm) -->
                <div class="area-vazia"></div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Biblioteca para gerar código de barras -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <script>
        // Gerar códigos de barras para cada produto
        <?php foreach ($produtos as $produto): ?>
            <?php $ean13 = $controller->gerarEAN13($produto['id']); ?>
            try {
                JsBarcode("#barcode-<?= $produto['id'] ?>", "<?= $ean13 ?>", {
                    format: "EAN13",
                    width: 1,
                    height: 35,
                    displayValue: true,
                    fontSize: 8,
                    margin: 0,
                    marginTop: 2,
                    marginBottom: 2
                });
            } catch (e) {
                console.error('Erro ao gerar código de barras:', e);
            }
        <?php endforeach; ?>

        // Auto-impressão (opcional, remova o comentário se desejar)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 1000);
        // };
    </script>
</body>
</html>
