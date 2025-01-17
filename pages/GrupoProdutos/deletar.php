<?php

use App\Models\GrupoProdutos\Controller;

$id = $link['3'];

// Buscar os dados do grupo de produtos para exibição
$controller = new Controller();
$return = $controller->ver($id);

// Verificar se o grupo foi encontrado
if (!$return) {
    echo notify('danger', "Grupo de produtos não encontrado.");
    exit;
}

// Deletar o registro se o comando for confirmado
if (isset($link['4']) && $link['4'] == 'deletar') {
    $deletar = new Controller();
    $return = $deletar->deletar($id);

    if ($return) {
        echo notify('success', "Grupo de produtos deletado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao deletar o grupo de produtos.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Grupo de Produtos</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-12">
                <label for="" class="form-label d-block fw-bold">Nome do Grupo</label>
                <?php echo isset($return['nome_grupo']) ? htmlspecialchars($return['nome_grupo']) : "Grupo não encontrado."; ?>
            </div>
        </div>
        <div class="mt-3">
            <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar"; ?>">Deletar</a>
        </div>
    </div>
</div>
