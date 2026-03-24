<?php

use App\Models\Pedidos\Controller;

$controller = new Controller();
$id = $link[3] ?? null;

if (!$id) {
    echo notify('danger', 'ID do pedido não informado.');
    echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    exit;
}

$dados = $controller->ver($id);
$pedido = $dados['pedido'];
$itens = $dados['itens'];
$pagamentos = $dados['pagamentos'] ?? [];

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Detalhes do Pedido</h3>
        <div>
        <a href="<?php echo "{$url}!/Notas/emitir-nota/{$link[3]}"; ?>" class="btn btn-success">Imprimir Nf-e</a>
        <a href="<?php echo "{$url}!/{$link[1]}/listar"; ?>" class="btn btn-warning text-primary">Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <h4 class="card-title">Dados do Pedido</h4>
        <div class="row g-3">
            <div class="col-lg-6">
                <strong>Cliente:</strong> 
                <?= htmlspecialchars(
                    !empty($pedido['nome_pf']) 
                    ? $pedido['nome_pf'] 
                    : ($pedido['nome_fantasia_pj'] ?? 'Não informado')
                ) ?>
            </div>
            <div class="col-lg-6">
                <strong>Data do Pedido:</strong> 
                <?= htmlspecialchars(date('d/m/Y', strtotime($pedido['data_pedido']))) ?>
            </div>
            <div class="col-lg-6">
                <strong>Data de Entrega:</strong> 
                <?= !empty($pedido['data_entrega']) 
                    ? htmlspecialchars(date('d/m/Y', strtotime($pedido['data_entrega']))) 
                    : 'Não informado'; ?>
            </div>
            <div class="col-lg-6">
                <strong>Forma de Pagamento:</strong> 
                <?= htmlspecialchars($pedido['forma_pagamento'] ?? 'Não informado') ?>
            </div>
            <?php if (!empty($pagamentos)): ?>
            <div class="col-12">
                <strong>Composição do pagamento:</strong>
                <table class="table table-sm table-bordered mt-2 mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Forma</th>
                            <th class="text-end">Valor</th>
                            <th>Parcelas</th>
                            <th>Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagamentos as $pg): ?>
                            <tr>
                                <td><?= htmlspecialchars($pg['forma'] ?? '') ?></td>
                                <td class="text-end">R$ <?= number_format((float)($pg['valor'] ?? 0), 2, ',', '.') ?></td>
                                <td><?= (int)($pg['parcelas'] ?? 1) ?></td>
                                <td><?= htmlspecialchars($pg['observacao'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            <div class="col-lg-6">
                <strong>Status:</strong> 
                <span class="badge bg-<?= $pedido['status_pedido'] == 'Pendente' ? 'warning' : 'success' ?>"><?= htmlspecialchars($pedido['status_pedido'] ?? 'Pendente') ?> </span>
            </div>
            <div class="col-lg-6">
                <strong>Valor Total:</strong> 
                R$<?= isset($pedido['total']) 
                    ? number_format($pedido['total'], 2, ',', '.') 
                    : '0,00'; ?>
            </div>
            <div class="col-lg-6">
                <strong>Valor Pago:</strong> 
                R$<?= isset($pedido['valor_pago']) 
                    ? number_format($pedido['valor_pago'], 2, ',', '.') 
                    : '0,00'; ?>
            </div>
            <div class="col-lg-6">
                <strong>Acréscimo:</strong> 
                <?= isset($pedido['acrescimo']) 
                    ? number_format($pedido['acrescimo'], 2, ',', '.') . '%' 
                    : '0,00%'; ?>
            </div>
            <div class="col-lg-6">
                <strong>Desconto:</strong> 
                <?= isset($pedido['desconto']) 
                    ? number_format($pedido['desconto'], 2, ',', '.') . '%' 
                    : '0,00%'; ?>
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
                    <th>Desconto (%)</th>
                    <th>Subtotal (R$)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome_produto'] ?? 'Produto não encontrado') ?></td>
                        <td><?= htmlspecialchars($item['quantidade']) ?></td>
                        <td>
                            R$<?= isset($item['valor_unitario']) 
                                ? number_format($item['valor_unitario'], 2, ',', '.') 
                                : '0,00'; ?>
                        </td>
                        <td>
                            <?= isset($item['desconto_percentual']) 
                                ? number_format($item['desconto_percentual'], 2, ',', '.') . '%' 
                                : '0,00%'; ?>
                        </td>
                        <td>
                            R$<?= number_format(
                                ($item['quantidade'] * $item['valor_unitario']) * (1 - ($item['desconto_percentual'] / 100)), 
                                2, ',', '.'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="col-12">
                <strong>Observações:</strong>
                <p><?= htmlspecialchars($pedido['observacoes'] ?? 'Não informado') ?></p>
            </div>
    </div>
</div>
