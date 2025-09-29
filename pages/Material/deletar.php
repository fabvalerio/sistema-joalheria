<?php

use App\Models\Material\Controller;

$id = $link['3'];

$controller = new Controller();
$return = $controller->ver($id);

if (!$return) {
    echo notify('danger', "Material não encontrado.");
    exit;
}

if (isset($link['4']) && $link['4'] == 'deletar') {
    $returnDelete = $controller->deletar($id);

    if ($returnDelete) {
        echo notify('success', "Material excluído com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao excluir o Material.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Material</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <p>Tem certeza de que deseja excluir o Material <strong><?= htmlspecialchars($return['nome']) ?></strong>?</p>
        <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$id}/deletar" ?>">Deletar</a>
    </div>
</div>
