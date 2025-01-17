<?php

use App\Models\GrupoClientes\Controller;

$id = $link['3']; // ID do grupo de clientes a ser editado

// Buscar os dados do grupo para preencher o formulário
$controller = new Controller();
$return = $controller->ver($id);

// Verificar se o grupo foi encontrado
if (!$return) {
    echo notify('danger', "Grupo de clientes não encontrado.");
    exit;
}

// Atualizar o grupo de clientes se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'nome_grupo' => $_POST['nome_grupo'],
        'comissao_vendedores' => $_POST['comissao_vendedores']
    ];

    $returnUpdate = $controller->editar($id, $dados);

    if ($returnUpdate) {
        echo notify('success', "Grupo de clientes atualizado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao atualizar o grupo de clientes.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Editar Grupo de Clientes</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$id}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-lg-6">
                    <label for="" class="form-label">Nome do Grupo</label>
                    <input type="text" class="form-control" name="nome_grupo" value="<?= htmlspecialchars($return['nome_grupo']) ?>" required>
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Comissão dos Vendedores (%)</label>
                    <input type="number" step="0.01" class="form-control" name="comissao_vendedores" value="<?= htmlspecialchars($return['comissao_vendedores']) ?>" required>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar Alterações</button>
                </div>
            </div>
        </form>
    </div>
</div>
