
<?php

use App\Models\Feriados\ControllerFeriados;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'data_feriado' => $_POST['data_feriado'],
        'descricao' => $_POST['descricao'],
        'tipo' => $_POST['tipo'],
        'facultativo' => $_POST['facultativo']
    ];

    $controller = new ControllerFeriados();
    $return = $controller->cadastro($dados);

    if ($return) {
        echo notify('success', "Feriado cadastrado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao cadastrar o feriado.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro de Feriado</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-lg-4">
                    <label for="" class="form-label">Data do Feriado</label>
                    <input type="date" class="form-control" name="data_feriado" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Descrição</label>
                    <input type="text" class="form-control" name="descricao" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Tipo</label>
                    <select class="form-select" name="tipo" required>
                        <option value="">Selecione o Tipo</option>
                        <option value="Nacional">Nacional</option>
                        <option value="Municipal">Municipal</option>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Facultativo</label>
                    <select class="form-select" name="facultativo" required>
                        <option value="">Selecione</option>
                        <option value="S">Sim</option>
                        <option value="N">Não</option>
                    </select>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
