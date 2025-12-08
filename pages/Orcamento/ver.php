<?php

use App\Models\Orcamento\Controller;

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

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Detalhes do Pedido</h3>
        <div>
        <a href="<?php echo "{$url}!/{$link[1]}/listar"; ?>" class="btn btn-warning text-primary">Voltar</a>
        <a href="<?php echo "{$url}pages/Orcamento/imprimir.php?id={$id}"; ?>" class="btn btn-info text-primary print" target="_blank">Imprimir</a>
        <a href="<?php echo "{$url}!/Fabrica/aberto/{$id}"; ?>" class="btn btn-info text-primary">Fábrica</a>
        </div>
    </div>

    <div class="card-body print-tela">
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
                    <th>Fábrica</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome_produto'] ?? $item['descricao_produto']) ?></td>
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
                        <td><?= $item['fabrica'] == 1 ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' ?></td>
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
