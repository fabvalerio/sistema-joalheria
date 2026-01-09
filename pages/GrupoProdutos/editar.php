<?php

use App\Models\GrupoProdutos\Controller;

$id = $link['3']; 

$controller = new Controller();
$return = $controller->ver($id);

if (!$return) {
    echo notify('danger', "Grupo de produtos não encontrado.");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'nome_grupo' => $_POST['nome_grupo'],
        'tempo' => $_POST['tempo']
    ];

    $returnUpdate = $controller->editar($id, $dados);

    if ($returnUpdate) {
        echo notify('success', "Grupo de produtos atualizado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao atualizar o grupo de produtos.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Editar Grupo de Produtos</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$id}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-lg-12">
                    <label for="" class="form-label">Nome do Grupo</label>
                    <input type="text" class="form-control" name="nome_grupo" value="<?= $return['nome_grupo'] ?>" required>
                </div>
                <div class="col-lg-12">
                    <label for="" class="form-label">Dias de Confecção</label>
                    <input type="number" class="form-control" name="tempo" value="<?= $return['tempo'] ?? '' ?>" min="0" placeholder="Número de dias">
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar Alterações</button>
                </div>
            </div>
        </form>
    </div>
</div>
