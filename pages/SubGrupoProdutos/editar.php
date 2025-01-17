<?php

use App\Models\SubGrupoProdutos\Controller;

$id = $link['3'];

$controller = new Controller();
$return = $controller->ver($id);
$grupos = $controller->listarGrupos();

if (!$return) {
    echo notify('danger', "Subgrupo não encontrado.");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'nome_subgrupo' => $_POST['nome_subgrupo'],
        'grupo_id' => $_POST['grupo_id']
    ];

    $returnUpdate = $controller->editar($id, $dados);

    if ($returnUpdate) {
        echo notify('success', "Subgrupo atualizado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao atualizar o subgrupo.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Editar Subgrupo</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$id}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-lg-6">
                    <label for="" class="form-label">Nome do Subgrupo</label>
                    <input type="text" class="form-control" name="nome_subgrupo" value="<?= htmlspecialchars($return['nome_subgrupo']) ?>" required>
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Grupo</label>
                    <select class="form-select" name="grupo_id" required>
                        <option value="">Selecione o Grupo</option>
                        <?php foreach ($grupos as $grupo): ?>
                            <option value="<?= $grupo['id'] ?>" <?= $return['grupo_id'] == $grupo['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($grupo['nome_grupo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar Alterações</button>
                </div>
            </div>
        </form>
    </div>
</div>
