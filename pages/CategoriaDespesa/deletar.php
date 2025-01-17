<?php

use App\Models\CategoriaDespesa\Controller;

$id = $link['3']; // ID da categoria a ser deletada

$controller = new Controller();
$categoria = $controller->ver($id);

if (!$categoria) {
    echo notify('danger', "Categoria não encontrada.");
    exit;
}

if (isset($link['4']) && $link['4'] == 'deletar') {
    $return = $controller->deletar($id);

    if ($return) {
        echo notify('success', "Categoria deletada com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao deletar a categoria.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Categoria de Despesa</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-12">
                <label class="form-label d-block fw-bold">Descrição</label>
                <?php echo htmlspecialchars($categoria['descricao']); ?>
            </div>
        </div>
        <div class="mt-3">
            <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar"; ?>">Deletar</a>
        </div>
    </div>
</div>
