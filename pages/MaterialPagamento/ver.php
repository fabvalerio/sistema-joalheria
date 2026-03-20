<?php

use App\Models\MaterialPagamento\Controller;

$id = $link['3'];

$controller = new Controller();
$material = $controller->ver($id);

if (!$material) {
    echo notify('danger', "Material não encontrado.");
    exit;
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Detalhes do Material (Pagamento)</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-6">
                <label class="form-label d-block fw-bold">Tipo do Material</label>
                <?php echo htmlspecialchars($material['tipo_material'] ?? 'Não informado'); ?>
            </div>
            <div class="col-lg-6">
                <label class="form-label d-block fw-bold">Valor por Grama (R$)</label>
                R$ <?= number_format((float)($material['valor_por_grama'] ?? 0), 2, ',', '.'); ?>
            </div>
        </div>
    </div>
</div>
