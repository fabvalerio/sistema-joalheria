<?php

//erro de php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use App\Models\Produtos\Controller;

// Capturar o ID do produto a ser deletado
$id = $link[3];

// Instanciar o Controller de produtos
$controller = new Controller();

// Buscar os dados do produto para exibição
$produto = $controller->ver($id);

// Verificar se o produto foi encontrado
if (!$produto) {
    echo notify('danger', "Produto não encontrado.");
    exit;
}

// Deletar o registro se o comando for confirmado
if (isset($link[4]) && $link[4] == 'deletar') {
    try {
        $retorno = $controller->deletar($id);

        if ($retorno) {
            echo notify('success', "Produto deletado com sucesso!");
            echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
        } else {
            echo notify('danger', "Erro ao deletar o produto.");
        }
    } catch (Exception $e) {
        echo notify('danger', "Erro ao deletar o produto: " . $e->getMessage());
    }
    exit;
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Produto</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar"; ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-12">
                <label for="" class="form-label d-block fw-bold">Descrição do Produto</label>
                <!-- Ajuste aqui caso deseje exibir outro campo, como nome, modelo etc. -->
                <?php 
                    echo isset($produto['descricao_etiqueta']) 
                        ? htmlspecialchars($produto['descricao_etiqueta']) 
                        : "Produto não encontrado."; 
                ?>
            </div>
        </div>

        <div class="mt-3">
            <!-- Ao clicar neste botão, chamamos a URL com "deletar" no final,
                 confirmando a exclusão -->
            <a 
               class="btn btn-danger" 
               href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar"; ?>"
            >
               Deletar
            </a>
        </div>
    </div>
</div>
