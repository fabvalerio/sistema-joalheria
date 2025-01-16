<?php

use App\Models\GrupoProdutos\Controller;

$id = $link['3'];

$controller = new Controller();
$return = $controller->ver($id);

if (!$return) {
    echo notify('danger', "Grupo de produtos não encontrado.");
    exit;
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Detalhes do Grupo de Produtos</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-12">
                <label for="" class="form-label d-block fw-bold">Nome do Grupo</label>
                <?php echo htmlspecialchars($return['nome_grupo']); ?>
            </div>
        </div>
    </div>
</div>
