<?php

use App\Models\EntradaMercadorias\Controller;

$id = $link['3']; // ID da entrada de mercadoria
$controller = new Controller();
$return = $controller->ver($id); // Busca os dados da entrada de mercadoria pelo ID

if (!$return) {
    echo notify('danger', "Entrada de mercadoria não encontrada.");
    exit;
}

// Verifica se a ação de deletar foi confirmada
if (isset($link['4']) && $link['4'] == 'deletar') {
    $returnDelete = $controller->deletar($id);
    if ($returnDelete) {
        echo notify('success', "Entrada de mercadoria excluída com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao excluir a entrada de mercadoria.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Entrada de Mercadoria</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <p>Tem certeza de que deseja excluir a entrada de mercadoria <strong>Nota Fiscal: <?= htmlspecialchars($return['nf_fiscal']) ?></strong>?</p>
        <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar" ?>">Deletar</a>
    </div>
</div>
