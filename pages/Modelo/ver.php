<?php

use App\Models\Modelo\Controller; // Altere para o nome "Modelo" para nome da Tabela do SQL

// ID para editar
$id = $link['3'];

//Exibir
$controller = new Controller();
$return = $controller->ver($id);

// Verificar se o registro foi encontrado
if (!$return) {
    echo notify('danger', "Agente não encontrado.");
    exit;
}

?>

<div class="card">

    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Visualizar Dados Usuários</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar"?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
                <div class="col-lg-4">
                    <label for="" class="form-label d-block fw-bold">Nome</label>
                    <?php echo $return['nome'] ?? ''; ?>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label d-block fw-bold">Celular</label>
                    <?php echo $return['telefone'] ?? ''; ?>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label d-block fw-bold">E-mail</label>
                    <?php echo $return['email'] ?? ''; ?>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label d-block fw-bold">Comissão (%)</label>
                    <?php echo $return['taxa_comissao'] ?? ''; ?>
                </div>
        </div>

    </div>

</div>