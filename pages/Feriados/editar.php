
<?php

use App\Models\Feriados\ControllerFeriados;

$id = $link['3'];
$controller = new ControllerFeriados();
$return = $controller->ver($id);

if (!$return) {
    echo notify('danger', "Feriado não encontrado.");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'data_feriado' => $_POST['data_feriado'],
        'descricao' => $_POST['descricao'],
        'tipo' => $_POST['tipo'],
        'facultativo' => $_POST['facultativo']
    ];

    $returnUpdate = $controller->editar($id, $dados);

    if ($returnUpdate) {
        echo notify('success', "Feriado atualizado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao atualizar o feriado.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Editar Feriado</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$id}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-lg-4">
                    <label for="" class="form-label">Data do Feriado</label>
                    <input type="date" class="form-control" name="data_feriado" value="<?= $return['data_feriado'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Descrição</label>
                    <input type="text" class="form-control" name="descricao" value="<?= $return['descricao'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Tipo</label>
                    <select class="form-select" name="tipo" required>
                        <option value="Nacional" <?= $return['tipo'] == 'Nacional' ? 'selected' : '' ?>>Nacional</option>
                        <option value="Municipal" <?= $return['tipo'] == 'Municipal' ? 'selected' : '' ?>>Municipal</option>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Facultativo</label>
                    <select class="form-select" name="facultativo" required>
                        <option value="S" <?= $return['facultativo'] == 'S' ? 'selected' : '' ?>>Sim</option>
                        <option value="N" <?= $return['facultativo'] == 'N' ? 'selected' : '' ?>>Não</option>
                    </select>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar Alterações</button>
                </div>
            </div>
        </form>
    </div>
</div>
