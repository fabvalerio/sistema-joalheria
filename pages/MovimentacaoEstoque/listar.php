<?php

use App\Models\MovimentacaoEstoque\Controller;

$controller = new Controller();
$movimentacoes = $controller->listar();

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Movimentações de Estoque</h3>
    </div>

    <div class="card-body">
        <table id="example1" class="table table-striped">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Tipo</th>
                    <th>Quantidade</th>
                    <th>Documento</th>
                    <th>Data</th>
                    <th>Motivo</th>
                    <th>Estoque Antes</th>
                    <th>Estoque Atualizado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimentacoes as $movimentacao): ?>
                    <tr>
                        <td><?= htmlspecialchars($movimentacao['descricao_produto']) ?></td>
                        <td><span class="badge bg-<?= $movimentacao['tipo_movimentacao'] == 'Entrada' ? 'success' : 'danger' ?>"><?= htmlspecialchars($movimentacao['tipo_movimentacao']) ?></span></td>
                        <td><?= number_format($movimentacao['quantidade'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars($movimentacao['documento'] ?? '-') ?></td>
                        <td><?= date("d/m/Y", strtotime($movimentacao['data_movimentacao'])) ?></td>
                        <td><?= htmlspecialchars($movimentacao['motivo']) ?></td>
                        <td><span class="badge bg-<?= $movimentacao['estoque_antes'] > $movimentacao['estoque_atualizado'] ? 'danger' : 'success' ?>"><?= number_format($movimentacao['estoque_antes'], 2, ',', '.') ?> </span></td>
                        <td><span class="badge bg-<?= $movimentacao['estoque_antes'] > $movimentacao['estoque_atualizado'] ? 'danger' : 'success' ?>"><?= number_format($movimentacao['estoque_atualizado'], 2, ',', '.') ?> </span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
