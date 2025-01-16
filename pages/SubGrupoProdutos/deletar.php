<?php

use App\Models\SubGrupoProdutos\Controller;

$id = $link['3']; // ID do subgrupo a ser deletado

// Buscar os dados do subgrupo para exibição
$controller = new Controller();
$return = $controller->ver($id);

// Verificar se o subgrupo foi encontrado
if (!$return) {
    echo notify('danger', "Subgrupo não encontrado.");
    exit;
}

// Deletar o registro se o comando for confirmado
if (isset($link['4']) && $link['4'] == 'deletar') {
    $deletar = new Controller();
    $return = $deletar->deletar($id);

    if ($return) {
        echo notify('success', "Subgrupo deletado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
        exit;
    } else {
        echo notify('danger', "Erro ao deletar o subgrupo.");
        exit;
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Subgrupo</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Nome do Subgrupo</label>
                <?php echo htmlspecialchars($return['nome_subgrupo']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Grupo</label>
                <?php echo htmlspecialchars($return['nome_grupo']); ?>
            </div>
        </div>
        <div class="mt-3">
            <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar"; ?>">Deletar</a>
        </div>
    </div>
</div>
