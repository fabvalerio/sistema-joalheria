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
    </style>


<h2>Código de Barras para o Produto: <?php echo htmlspecialchars($idProduto); ?></h2>

<div class="codigo-container">
    <img src="gerar_codigo.php?id=<?php echo urlencode($idProduto); ?>" alt="Código de Barras">
</div>

<button class="btn-imprimir btn-primary" onclick="window.print();">Imprimir</button>