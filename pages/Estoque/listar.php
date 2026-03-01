<?php

use App\Models\Estoque\Controller;

$controller = new Controller();

$isAdmin = isset($_COOKIE['nivel_acesso']) && $_COOKIE['nivel_acesso'] === 'Administrador';
$usuario_loja_id = $_COOKIE['loja_id'] ?? null;

$lojas = $controller->listarLojas($isAdmin ? null : $usuario_loja_id);

$loja_id_filtro = $_GET['loja_id'] ?? null;
if (!$isAdmin && $usuario_loja_id) {
    $loja_id_filtro = $usuario_loja_id;
}
$mostrarColunaLoja = ($loja_id_filtro === null || $loja_id_filtro === '');
?>

<link href="<?= $url ?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="<?= $url ?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $url ?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <i class="fas fa-boxes me-2"></i>Estoque por Loja
        </h3>
        <?php if ($isAdmin && count($lojas) > 1): ?>
        <div class="d-flex align-items-center gap-2">
            <label class="text-white mb-0">Filtrar loja:</label>
            <select id="filtroLoja" class="form-select form-select-sm" style="width:auto">
                <option value="">Todas as lojas</option>
                <?php foreach ($lojas as $l): ?>
                    <option value="<?= $l['id'] ?>" <?= (string)$loja_id_filtro === (string)$l['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($l['nome']) ?> (<?= $l['tipo'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <?php if (!$isAdmin): ?>
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-1"></i>
            Você está visualizando o estoque da sua loja: <strong><?= htmlspecialchars($_COOKIE['loja_nome'] ?? 'Sua Loja') ?></strong>
        </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table id="tabelaEstoque" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Loja</th>
                        <th>Código</th>
                        <th>Produto</th>
                        <th class="text-center">Quantidade</th>
                        <th class="text-center">Qtd Mínima</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var baseUrl = '<?= $url ?>pages/Estoque/json.php';
    var lojaIdInicial = <?= json_encode($loja_id_filtro) ?>;

    var table = $('#tabelaEstoque').DataTable({
        ajax: {
            url: baseUrl,
            dataSrc: 'data',
            data: function(d) {
                d.loja_id = $('#filtroLoja').length ? ($('#filtroLoja').val() || null) : lojaIdInicial;
            }
        },
        columns: [
            { data: 'loja_nome' },
            { data: 'codigo_formatado', render: function(v) { return '<code>' + (v || '') + '</code>'; } },
            { data: 'nome_produto' },
            { data: 'quantidade_formatada', className: 'text-center', render: function(v) { return '<span class="badge bg-info">' + (v || '0') + '</span>'; } },
            { data: 'quantidade_minima_formatada', className: 'text-center' },
            { data: 'status', className: 'text-center', render: function(v) {
                return '<span class="badge bg-' + (v === 'Baixo' ? 'danger' : 'success') + '">' + (v || 'OK') + '</span>';
            }}
        ],
        columnDefs: [{ targets: 0, visible: <?= json_encode($mostrarColunaLoja) ?> }],
        order: [[1, 'asc']],
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

    $('#filtroLoja').on('change', function() {
        var val = $(this).val();
        table.column(0).visible(!val);
        table.ajax.reload();
        var params = new URLSearchParams(window.location.search);
        if (val) params.set('loja_id', val); else params.delete('loja_id');
        params.set('page', '!/Estoque/listar');
        if (history.replaceState) {
            history.replaceState(null, '', window.location.pathname + '?' + params.toString());
        }
    });
});
</script>
