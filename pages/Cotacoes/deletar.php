<?php

use App\Models\Cotacoes\Controller;

$controller = new Controller();
$id = $link['3'];

$cotacao = $controller->ver($id);

if (!$cotacao) {
    echo notify('danger', "Cotação não encontrada.");
    exit;
}

if (isset($link['4']) && $link['4'] == 'deletar') {
    $return = $controller->deletar($id);

    if ($return) {
        echo notify('success', "Cotação deletada com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
        exit;
    } else {
        echo notify('danger', "Erro ao deletar a cotação.");
    }
}

?>

<div class="card">
    <div class="card-header bg-danger text-white">
        <h3 class="card-title">Excluir Cotação</h3>
    </div>
    <div class="card-body">
        <p class="text-danger">Tem certeza de que deseja excluir a cotação abaixo?</p>
        <p><strong>ID:</strong> <?= $cotacao['id'] ?></p>
        <p><strong>Nome:</strong> <?= htmlspecialchars($cotacao['nome']) ?></p>
        <p><strong>Valor:</strong> <?= number_format($cotacao['valor'], 2, ',', '.') ?></p>
        <a href="<?= "{$url}!/{$link[1]}/deletar/{$id}/deletar" ?>" class="btn btn-danger">Excluir</a>
        <a href="<?= "{$url}!/{$link[1]}/listar" ?>" class="btn btn-secondary">Cancelar</a>
    </div>
</div>
