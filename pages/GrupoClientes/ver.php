<?php

use App\Models\GrupoClientes\Controller;

$id = $link['3']; // ID do grupo de clientes a ser visualizado

// Buscar os dados do grupo de clientes
$controller = new Controller();
$return = $controller->ver($id);

// Verificar se o grupo foi encontrado
if (!$return) {
    echo notify('danger', "Grupo de clientes não encontrado.");
    exit;
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Detalhes do Grupo de Clientes</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Nome do Grupo</label>
                <?php echo htmlspecialchars($return['nome_grupo']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Comissão dos Vendedores (%)</label>
                <?php echo htmlspecialchars($return['comissao_vendedores']); ?>
            </div>
        </div>
    </div>
</div>
