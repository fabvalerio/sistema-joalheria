<?php

use App\Models\SubGrupoProdutos\Controller;

$id = $link['3']; // ID do subgrupo a ser visualizado

// Buscar os dados do subgrupo
$controller = new Controller();
$return = $controller->ver($id);

// Verificar se o subgrupo foi encontrado
if (!$return) {
    echo notify('danger', "Subgrupo não encontrado.");
    exit;
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Detalhes do Subgrupo</h3>
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
    </div>
</div>
