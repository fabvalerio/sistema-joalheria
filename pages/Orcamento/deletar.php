<?php

use App\Models\Orcamento\Controller;

$id = $link['3']; // ID do pedido a ser deletado

// Buscar os dados do pedido para exibição
$controller = new Controller();
$return = $controller->ver($id);

// Verificar se o pedido foi encontrado
if (!$return) {
    echo notify('danger', "Pedido não encontrado.");
    exit;
}

// Deletar o registro se o comando for confirmado
if (isset($link['4']) && $link['4'] == 'deletar') {
    $deletar = new Controller();
    $result = $deletar->deletar($id);

    if ($result) {
        echo notify('success', "Pedido deletado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao deletar o pedido.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Pedido</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar"; ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <h4 class="card-title">Dados do Pedido</h4>
        <div class="row g-3">
            <div class="col-lg-6">
                <label class="form-label d-block fw-bold">Cliente:</label>
                <?= htmlspecialchars(
                    !empty($return['pedido']['nome_pf'])
                        ? $return['pedido']['nome_pf']
                        : ($return['pedido']['nome_fantasia_pj'] ?? 'Não informado')
                ) ?>
            </div>
            <div class="col-lg-6">
                <label class="form-label d-block fw-bold">Data do Pedido:</label>
                <?= htmlspecialchars(date('d/m/Y', strtotime($return['pedido']['data_pedido']))) ?>
            </div>
            <div class="col-lg-6">
                <label class="form-label d-block fw-bold">Valor Total:</label>
                R$<?= number_format($return['pedido']['total'], 2, ',', '.') ?>
            </div>
            <div class="col-lg-6">
                <label class="form-label d-block fw-bold">Status:</label>
                <?= htmlspecialchars($return['pedido']['status_pedido']) ?>
            </div>
        </div>

        <hr>
        <h4 class="card-title">Itens do Pedido</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Valor Unitário (R$)</th>
                    <th>Subtotal (R$)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($return['itens'] as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome_produto'] ?? 'Produto não encontrado') ?></td>
                        <td><?= htmlspecialchars($item['quantidade'] ?? '0') ?></td>
                        <td>R$<?= number_format($item['valor_unitario'], 2, ',', '.') ?></td>
                        <td>R$<?= number_format(($item['quantidade'] ?? 0) * ($item['valor_unitario'] ?? 0), 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-3">
            <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar"; ?>">Deletar</a>
        </div>
    </div>
</div>
