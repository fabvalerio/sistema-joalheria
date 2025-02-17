<?php

use App\Models\Usuarios\Controller; // Altere para o nome correto

// ID para exibir os detalhes
$id = $link['3'];

// Exibir os detalhes
$controller = new Controller();
$return = $controller->ver($id);

// Obter todos os cargos para criar um mapa
$cargos = $controller->cargos();
$cargoMap = [];
foreach ($cargos as $cargo) {
    $cargoMap[$cargo['id']] = $cargo['cargo'];
}

// Verificar se o registro foi encontrado
if (!$return) {
    echo notify('danger', "Usuário não encontrado.");
    exit;
}

// Obter o nome do cargo a partir do ID
$nomeCargo = $cargoMap[$return['cargo']] ?? 'Cargo não encontrado';

?>

<div class="card">

    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Visualizar Dados do Usuário</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Nome Completo</label>
                <?php echo $return['nome_completo'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">E-mail</label>
                <?php echo $return['email'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Cargo</label>
                <?php echo htmlspecialchars($nomeCargo); ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Telefone</label>
                <?php echo $return['telefone'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">RG</label>
                <?php echo $return['rg'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Data de Emissão do RG</label>
                <?php echo $return['emissao_rg'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">CPF</label>
                <?php echo $return['cpf'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Data de Nascimento</label>
                <?php echo $return['data_nascimento'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">CEP</label>
                <?php echo $return['cep'] ?? ''; ?>
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
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Login</label>
                <?php echo $return['login'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Nível de Acesso</label>
                <?php echo $return['nivel_acesso'] ?? ''; ?>
            </div>
            <!-- status -->
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Status</label>
                <span class="badge bg-<?= $return['status'] == 'Ativo' ? 'success' : 'danger' ?>"><?= $return['status'] == 'Ativo' ? 'Ativo' : 'Inativo' ?></span>
            </div>
        </div>
    </div>

</div>
