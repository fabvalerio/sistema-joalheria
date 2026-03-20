<?php

use App\Models\Cargos\Controller;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'cargo' => $_POST['cargo'],
        'fabrica' => isset($_POST['fabrica']) ? 1 : 0
    ];

    $controller = new Controller();
    $return = $controller->cadastro($dados);

    if ($return) {
        echo notify('success', "Cargo cadastrado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao cadastrar o cargo.");
    }
    
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro de Cargo</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-lg-4">
                    <label for="cargo" class="form-label">Cargo</label>
                    <input type="text" class="form-control" id="cargo" name="cargo" required>
                </div>
                <div class="col-lg-4 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="fabrica" id="fabrica" value="1">
                        <label class="form-check-label" for="fabrica">Cargo da fábrica</label>
                    </div>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
