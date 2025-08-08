<?php

use App\Models\Loja\Controller; // Altere para o nome correto

// Exibir todos os erros do PHP (apenas para desenvolvimento, remova em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// ID para exibir os detalhes
$id = $link['3'];

// Exibir os detalhes
$controller = new Controller();
$return = $controller->ver($id);


// Verificar se o registro foi encontrado
if (!$return) {
    echo notify('danger', "Usuário não encontrado.");
    exit;
}


?>

<div class="card">

    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Visualizar Loja</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Loja</label>
                <?php echo $return['nome'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">CNPJ</label>
                <?php echo $return['cnpj'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Responsavel</label>
                <?php echo $return['responsavel'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">CPF</label>
                <?php echo $return['cpf'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Endereço</label>
                <?php echo $return['endereco'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Bairro</label>
                <?php echo $return['bairro'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Número</label>
                <?php echo $return['numero'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Cidade</label>
                <?php echo $return['cidade'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Estado</label>
                <?php echo $return['estado'] ?? ''; ?>
            </div>
            <!-- status -->
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Status</label>
                <span class="badge bg-<?= $return['status'] == 'Ativo' ? 'success' : 'danger' ?>"><?= $return['status'] == 'Ativo' ? 'Ativo' : 'Inativo' ?></span>
            </div>
        </div>
    </div>

</div>
