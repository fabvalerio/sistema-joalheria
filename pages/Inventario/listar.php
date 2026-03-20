<?php

use App\Models\Inventario\Controller;

$controller = new Controller();
$devolucoes = $controller->listar(100);
?>

<link href="<?= $url ?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="<?= $url ?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $url ?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="fas fa-undo me-2"></i>Devoluções</h3>
        <div>
            <?php if (isset($podeManipular) && $podeManipular($link[1])): ?>
                <a href="<?= $url ?>!/Inventario/cadastro" class="btn btn-warning text-primary me-2">Novo Registro</a>
            <?php endif; ?>
            <a href="<?= $url ?>!/Inventario/relatorio" class="btn btn-outline-light btn-sm">Relatório por Período</a>
        </div>
    </div>

    <div class="card-body">
        <p class="text-muted">Últimos registros de inventário (entradas e saídas). Use o <strong>Relatório por Período</strong> para filtrar por data.</p>

        <table id="example1" class="table table-striped table-hover">
            <thead class="bg-light">
                <tr>
                    <th>Tipo</th>
                    <th>Data/Hora</th>
                    <th>Produto</th>
                    <th class="text-center">Quantidade</th>
                    <th>Motivo</th>
                    <th>Nº Venda</th>
                    <th>Estoque</th>
                    <th>Responsável</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($devolucoes)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Nenhum registro de inventário.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($devolucoes as $d): ?>
                        <tr>
                            <td><span class="badge bg-<?= ($d['tipo'] ?? 'Entrada') === 'Entrada' ? 'success' : 'danger' ?>"><?= htmlspecialchars($d['tipo'] ?? 'Entrada') ?></span></td>
                            <td><?= date('d/m/Y', strtotime($d['data_devolucao'])) ?> <?= date('H:i', strtotime($d['hora_devolucao'])) ?></td>
                            <td>
                                <strong><?= str_pad($d['produto_id'], 6, '0', STR_PAD_LEFT) ?></strong> - <?= htmlspecialchars($d['nome_produto'] ?? '-') ?>
                            </td>
                            <td class="text-center"><span class="badge bg-success"><?= number_format((float)$d['quantidade'], 0, ',', '.') ?></span></td>
                            <td><?= htmlspecialchars($d['motivo']) ?></td>
                            <td><?= !empty($d['pedido_id']) ? htmlspecialchars($d['pedido_id']) : '-' ?></td>
                            <td><span class="badge bg-<?= ($d['loja_tipo'] ?? '') === 'CD' ? 'secondary' : 'primary' ?>"><?= htmlspecialchars($d['loja_nome'] ?? '-') ?></span></td>
                            <td><?= htmlspecialchars($d['responsavel_nome'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($devolucoes)): ?>
<script>
$(document).ready(function() {
    $('#example1').DataTable({
        order: [[1, 'desc']],
        language: {
            sEmptyTable: "Nenhum dado disponível na tabela",
            sInfo: "Mostrando de _START_ até _END_ de _TOTAL_ entradas",
            sInfoEmpty: "Mostrando 0 até 0 de 0 entradas",
            sInfoFiltered: "(filtrado de _MAX_ entradas totais)",
            sLengthMenu: "Mostrar _MENU_ entradas",
            sLoadingRecords: "Carregando...",
            sProcessing: "Processando...",
            sSearch: "Pesquisar:",
            sZeroRecords: "Nenhum registro encontrado",
            oPaginate: {
                sFirst: "Primeiro",
                sPrevious: "Anterior",
                sNext: "Próximo",
                sLast: "Último"
            }
        }
    });
});
</script>
<?php endif; ?>
