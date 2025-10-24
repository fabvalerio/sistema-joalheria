<?php

use App\Models\GrupoClientes\Controller;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'nome_grupo' => $_POST['nome_grupo'],
        'comissao_vendedores' => $_POST['comissao_vendedores']
    ];

    $controller = new Controller();
    $return = $controller->cadastro($dados);

    if ($return) {
        echo notify('success', "Grupo de clientes cadastrado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao cadastrar o grupo de clientes.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro de Grupo de Clientes</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-lg-6">
                    <label for="" class="form-label">Nome do Grupo</label>
                    <input type="text" class="form-control" name="nome_grupo" required>
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Desconto (%)</label>
                    <input type="number" step="0.01" class="form-control" name="comissao_vendedores" required>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
