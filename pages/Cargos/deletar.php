<?php

use App\Models\Cargos\Controller;

$id = $link['3'];

$controller = new Controller();
$return = $controller->ver($id);

if (!$return) {
    echo notify('danger', "Cargo não encontrado.");
    exit;
}

if ($link['4'] == 'deletar') {
    $returnDelete = $controller->deletar($id);

    if ($returnDelete) {
        echo notify('success', "Cargo excluído com sucesso!");
    } else {
        echo notify('danger', "Erro ao excluir o cargo.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Cargo</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <p>Tem certeza de que deseja excluir o cargo <strong><?= htmlspecialchars($return['cargo']) ?></strong>?</p>
        <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$id}/deletar" ?>">Deletar</a>
    </div>
</div>
