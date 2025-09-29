<?php

use App\Models\Categoria\Controller;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'nome' => $_POST['nome']
    ];

    $controller = new Controller();
    $return = $controller->cadastro($dados);

    if ($return) {
        echo notify('success', "Categoria cadastrado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao cadastrar a Categoria.");
    }
    
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro de Categoria</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-lg-4">
                    <label for="nome" class="form-label">Categoria</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
