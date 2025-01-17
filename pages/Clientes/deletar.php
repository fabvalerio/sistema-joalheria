<?php

use App\Models\Clientes\Controller;

$id = $link['3']; // ID do cliente a ser deletado

// Buscar os dados do cliente para exibição
$controller = new Controller();
$cliente = $controller->ver($id);

// Verificar se o cliente foi encontrado
if (!$cliente) {
    echo notify('danger', "Cliente não encontrado.");
    exit;
}

// Deletar o cliente se o comando for confirmado
if (isset($link['4']) && $link['4'] == 'deletar') {
    $return = $controller->deletar($id);

    if ($return) {
        echo notify('success', "Cliente deletado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao deletar o cliente.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Cliente</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Tipo de Cliente</label>
                <?php echo $cliente['tipo_cliente'] == 'PF' ? 'Pessoa Física' : 'Pessoa Jurídica'; ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Nome</label>
                <?php 
                echo $cliente['tipo_cliente'] == 'PF' 
                    ? htmlspecialchars($cliente['nome_pf']) 
                    : htmlspecialchars($cliente['razao_social_pj']); 
                ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Telefone</label>
                <?php echo htmlspecialchars($cliente['telefone']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">E-mail</label>
                <?php echo htmlspecialchars($cliente['email']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Grupo</label>
                <?php 
                $grupos = $controller->listarGrupos();
                foreach ($grupos as $grupo) {
                    if ($grupo['id'] == $cliente['grupo']) {
                        echo htmlspecialchars($grupo['nome_grupo']);
                        break;
                    }
                }
                ?>
            </div>
        </div>
        <div class="mt-3">
            <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar"; ?>">Deletar</a>
        </div>
    </div>
</div>
