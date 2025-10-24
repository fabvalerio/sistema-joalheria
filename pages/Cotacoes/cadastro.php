<?php

use App\Models\Cotacoes\Controller;

$controller = new Controller();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'nome' => $_POST['nome'],
        'valor' => $_POST['valor']
    ];

    $return = $controller->cadastro($dados);

    if ($return) {
        echo notify('success', "Cotação cadastrada com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao cadastrar a cotação.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title">Cadastro de Cotação</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="" class="needs-validation" novalidate>
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" class="form-control" name="nome" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Valor (R$)</label>
                <input type="number" step="0.01" class="form-control" name="valor" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
</div>
