<?php

use App\Models\Usuarios\Controller; // Altere para o nome "Usuario" para o nome da Tabela do SQL

// ID para deletar
$id = $link['3'];

// Exibir os detalhes do registro para confirmar a exclusão
$controller = new Controller();
$return = $controller->ver($id);

// Verificar se o registro foi encontrado
if (!$return) {
    echo notify('danger', "Usuário não encontrado.");
    exit;
}

// Verificar se o comando para deletar foi enviado
if (isset($link['4']) && $link['4'] == 'deletar') {
    // Deletar o registro com o valor de $id
    $deletar = new Controller();
    $return = $deletar->deletar($id);

    // Verificar se a exclusão foi realizada
    if ($return) {
        echo notify('success', "Usuário deletado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao deletar usuário.");
    }
}


?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Usuário</h3>
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
                <?php echo $return['cargo'] ?? ''; ?>
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
                <label for="" class="form-label d-block fw-bold">CPF</label>
                <?php echo $return['cpf'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Data de Nascimento</label>
                <?php echo $return['data_nascimento'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Endereço</label>
                <?php echo $return['endereco'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Cidade</label>
                <?php echo $return['cidade'] ?? ''; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Estado</label>
                <?php echo $return['estado'] ?? ''; ?>
            </div>
        </div>
        <div class="mt-3">
            <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar"; ?>">Deletar</a>
        </div>
    </div>
</div>
