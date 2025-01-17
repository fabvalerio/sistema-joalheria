<?php

use App\Models\CategoriaDespesa\Controller;

$id = $link['3']; // ID da categoria a ser editada

$controller = new Controller();
$categoria = $controller->ver($id);

if (!$categoria) {
    echo notify('danger', "Categoria não encontrada.");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'descricao' => $_POST['descricao'],
    ];

    $return = $controller->editar($id, $dados);

    if ($return) {
        echo notify('success', "Categoria atualizada com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao atualizar a categoria.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Editar Categoria de Despesa</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$id}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-lg-12">
                    <label class="form-label">Descrição</label>
                    <input type="text" class="form-control" name="descricao" value="<?= htmlspecialchars($categoria['descricao']) ?>" required>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar Alterações</button>
                </div>
            </div>
        </form>
    </div>
</div>
