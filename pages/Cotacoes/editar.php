<?php

use App\Models\Cotacoes\Controller;

$controller = new Controller();
$id = $link['3'];

if (!$id) {
    echo notify('danger', "ID não especificado.");
    exit;
}

$cotacao = $controller->ver($id);

if (!$cotacao) {
    echo notify('danger', "Cotação não encontrada.");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'nome' => $_POST['nome'],
        'valor' => $_POST['valor']
    ];

    $return = $controller->editar($id, $dados);

    if ($return) {
        echo notify('success', "Cotação atualizada com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
        exit;
    } else {
        echo notify('danger', "Erro ao atualizar a cotação.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title">Editar Cotação</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="" class="needs-validation" novalidate>
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($cotacao['nome']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Valor</label>
                <input type="number" step="0.01" class="form-control" name="valor" value="<?= $cotacao['valor'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
</div>
