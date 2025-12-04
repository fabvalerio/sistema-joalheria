<?php

use App\Models\Relatorios\Controller;

$status = $_GET['status'] ?? null;
$inicio = $_GET['data_inicio'] ?? null;
$fim = $_GET['data_final'] ?? null;

// Instanciar o Controller
$controller = new Controller();

// Buscar dados
$consignacoes = $controller->consignacoes($status, $inicio, $fim);
$totais = $controller->somaConsignacoes($status, $inicio, $fim);

?>

<!-- DataTables CSS -->
<link href="<?php echo $url?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

<!-- DataTables JS -->
<script src="<?php echo $url?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo $url?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Relatório de Consignações</h3>
        <button class="btn btn-white text-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir
        </button>
    </div>

    <div class="m-3">
        <div class="row g-3">
            <!-- Card de Filtros -->
            <div class="col-12">
                <div class="card card-body">
                    <h6 class="card-title">Filtros</h6>
                    <form id="filtroForm">
                        <div class="row g-3 d-flex align-items-end">
                            <div class="col-lg-3">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="" <?php echo ($status == '') ? 'selected' : ''; ?>>Todos</option>
                                    <option value="Aberta" <?php echo ($status == 'Aberta') ? 'selected' : ''; ?>>Aberta</option>
                                    <option value="Finalizada" <?php echo ($status == 'Finalizada') ? 'selected' : ''; ?>>Finalizada</option>
                                    <option value="Canceleda" <?php echo ($status == 'Canceleda') ? 'selected' : ''; ?>>Cancelada</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label fw-bold">Data Inicial</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?php echo $inicio; ?>">
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label fw-bold">Data Final</label>
                                <input type="date" name="data_final" id="data_final" class="form-control" value="<?php echo $fim; ?>">
                            </div>
                            <div class="col-lg-3">
                                <a class="btn btn-success submit">FILTRAR</a>
                                <a class="btn btn-danger" href="<?php echo "{$url}!/Relatorios/consignacao"; ?>">LIMPAR</a>
                            </div>
                        </div>
                    </form>
                    <script>
                        $(document).ready(function() {
                            $(".submit").click(function(event) {
                                event.preventDefault();

                                let status = $("#status").val();
                                let dataInicio = $("#data_inicio").val();
                                let dataFinal = $("#data_final").val();

                                // Monta a URL com os parâmetros
                                let url = "<?php echo $url; ?>!/Relatorios/consignacao/&status=" + encodeURIComponent(status) + 
                                        "&data_inicio=" + encodeURIComponent(dataInicio) + 
                                        "&data_final=" + encodeURIComponent(dataFinal);

                                // Redireciona para a nova URL
                                window.location.href = url;
                            });
                        });
                    </script>
                </div>
            </div>

            <!-- Cards de Resumo -->
            <div class="col-md-3">
                <div class="card card-body border-primary">
                    <h6 class="card-title text-primary">Total de Consignações</h6>
                    <h3 class="mb-0"><?php echo $totais['total_consignacoes'] ?? 0; ?></h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-body border-success">
                    <h6 class="card-title text-success">Valor Total (com desconto)</h6>
                    <h3 class="mb-0">R$ <?php echo number_format($totais['valor_total'] ?? 0, 2, ',', '.'); ?></h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-body border-warning">
                    <h6 class="card-title text-warning">Total de Descontos Aplicados</h6>
                    <h3 class="mb-0">R$ <?php echo number_format($totais['desconto_total'] ?? 0, 2, ',', '.'); ?></h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-body border-info">
                    <h6 class="card-title text-info">Desconto Médio</h6>
                    <h3 class="mb-0"><?php echo number_format($totais['desconto_medio'] ?? 0, 2, ',', '.'); ?>%</h3>
                </div>
            </div>

            <!-- Cards de Status -->
            <div class="col-md-4">
                <div class="card card-body bg-success text-white">
                    <h6 class="card-title">Abertas</h6>
                    <h3 class="mb-0"><?php echo $totais['total_abertas'] ?? 0; ?></h3>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-body bg-primary text-white">
                    <h6 class="card-title">Finalizadas</h6>
                    <h3 class="mb-0"><?php echo $totais['total_finalizadas'] ?? 0; ?></h3>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-body bg-danger text-white">
                    <h6 class="card-title">Canceladas</h6>
                    <h3 class="mb-0"><?php echo $totais['total_canceladas'] ?? 0; ?></h3>
                </div>
            </div>

            <!-- Tabela de Consignações -->
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Detalhes das Consignações</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabelaConsignacoes" class="table table-striped table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Data</th>
                                        <th>Status</th>
                                        <th>Itens</th>
                                        <th>Desconto (%)</th>
                                        <th>Valor Desconto</th>
                                        <th>Valor Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($consignacoes as $consignacao): ?>
                                        <?php
                                        $desconto_percentual = $consignacao['desconto_percentual'] ?? 0;
                                        $valor_total = $consignacao['valor'] ?? 0;
                                        
                                        // Calcular valor do desconto
                                        if ($desconto_percentual > 0 && $valor_total > 0) {
                                            $subtotal = $valor_total / (1 - ($desconto_percentual / 100));
                                            $valor_desconto = $subtotal - $valor_total;
                                        } else {
                                            $valor_desconto = 0;
                                        }

                                        // Classe do status
                                        $status_class = '';
                                        switch ($consignacao['status']) {
                                            case 'Aberta':
                                                $status_class = 'badge badge-success';
                                                break;
                                            case 'Finalizada':
                                                $status_class = 'badge badge-primary';
                                                break;
                                            case 'Canceleda':
                                                $status_class = 'badge badge-danger';
                                                break;
                                            default:
                                                $status_class = 'badge badge-secondary';
                                        }
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($consignacao['id']) ?></td>
                                            <td><?= htmlspecialchars($consignacao['nome_pf'] ?? $consignacao['nome_fantasia_pj'] ?? 'Não informado') ?></td>
                                            <td><?= date('d/m/Y', strtotime($consignacao['data_consignacao'])) ?></td>
                                            <td><span class="<?= $status_class ?>"><?= htmlspecialchars($consignacao['status']) ?></span></td>
                                            <td><?= $consignacao['total_itens'] ?? 0 ?> itens</td>
                                            <td><?= number_format($desconto_percentual, 2, ',', '.') ?>%</td>
                                            <td>R$ <?= number_format($valor_desconto, 2, ',', '.') ?></td>
                                            <td class="fw-bold text-success">R$ <?= number_format($valor_total, 2, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tabelaConsignacoes').DataTable({
            "language": {
                "sEmptyTable": "Nenhum dado disponível na tabela",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ entradas",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 entradas",
                "sInfoFiltered": "(filtrado de _MAX_ entradas totais)",
                "sInfoPostFix": "",
                "sLengthMenu": "Mostrar _MENU_ entradas",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sSearch": "Pesquisar:",
                "sZeroRecords": "Nenhum registro encontrado",
                "oPaginate": {
                    "sFirst": "Primeiro",
                    "sPrevious": "Anterior",
                    "sNext": "Próximo",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": ativar para ordenar a coluna de forma ascendente",
                    "sSortDescending": ": ativar para ordenar a coluna de forma descendente"
                }
            },
            "order": [[0, 'desc']] // Ordenar por ID decrescente
        });
    });
</script>

<style>
    @media print {
        .btn, .card-body form, .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate {
            display: none !important;
        }
    }
</style>

