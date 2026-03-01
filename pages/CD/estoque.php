<?php

// CD/estoque usa tabela estoque (estoque principal do CD)

?>

<link href="<?= $url ?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="<?= $url ?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $url ?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

<div class="card">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">
            <i class="fas fa-warehouse me-2"></i>Estoque do Centro de Distribuição
        </h3>
        <div class="d-flex gap-2">
            <a href="<?= $url ?>!/CD/transferir" class="btn btn-warning btn-sm">
                <i class="fas fa-truck-loading me-1"></i>Transferir para Lojas
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="tabelaEstoqueCD" class="table table-striped table-hover">
                <thead>
                    <tr>
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
    var baseUrl = '<?= $url ?>pages/CD/estoque_json.php';

    $('#tabelaEstoqueCD').DataTable({
        ajax: {
            url: baseUrl,
            dataSrc: 'data'
        },
        columns: [
            { data: 'codigo_formatado', render: function(v) { return '<code>' + (v || '') + '</code>'; } },
            { data: 'nome_produto' },
            { data: 'quantidade_formatada', className: 'text-center', render: function(v) { return '<span class="badge bg-info">' + (v || '0') + '</span>'; } },
            { data: 'quantidade_minima_formatada', className: 'text-center' },
            { data: 'status', className: 'text-center', render: function(v) {
                return '<span class="badge bg-' + (v === 'Baixo' ? 'danger' : 'success') + '">' + (v || 'OK') + '</span>';
            }}
        ],
        order: [[0, 'asc']],
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
