
<?php

use App\Models\Feriados\ControllerFeriados;

$id = $link['3'];
$controller = new ControllerFeriados();
$return = $controller->ver($id);

if (!$return) {
    echo notify('danger', "Feriado não encontrado.");
    exit;
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Visualizar Feriado</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-4">
                <label for="" class="form-label fw-bold">Data</label>
                <!-- data dd/mm/aaa -->
                <?= date('d/m/Y', strtotime($return['data_feriado'])) ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label fw-bold">Descrição</label>
                <?= htmlspecialchars($return['descricao']) ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label fw-bold">Tipo</label>
                <?= htmlspecialchars($return['tipo']) ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label fw-bold">Facultativo</label>
                <span class="badge bg-<?= $return['facultativo'] == 'S' ? 'success' : 'danger' ?>"><?= $return['facultativo'] == 'S' ? 'Sim' : 'Não' ?></span>
            </div>
        </div>
    </div>
</div>
