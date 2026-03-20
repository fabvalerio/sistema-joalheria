<?php

use App\Models\MaterialPagamento\Controller;

$id = $link['3'];

$controller = new Controller();
$material = $controller->ver($id);

if (!$material) {
    echo notify('danger', "Material não encontrado.");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'tipo_material' => $_POST['tipo_material'],
        'valor_por_grama' => $_POST['valor_por_grama']
    ];

    $return = $controller->editar($id, $dados);

    if ($return) {
        echo notify('success', "Material atualizado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao atualizar o material.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Editar Material (Pagamento)</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$id}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-lg-6">
                    <label for="tipo_material" class="form-label">Tipo do Material</label>
                    <input type="text" class="form-control" id="tipo_material" name="tipo_material" value="<?= htmlspecialchars($material['tipo_material']) ?>" required>
                </div>
                <div class="col-lg-6">
                    <label for="valor_por_grama" class="form-label">Valor por Grama (R$)</label>
                    <input type="number" step="0.01" min="0" class="form-control" id="valor_por_grama" name="valor_por_grama" value="<?= htmlspecialchars($material['valor_por_grama']) ?>" required>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar Alterações</button>
                </div>
            </div>
        </form>
    </div>
</div>
