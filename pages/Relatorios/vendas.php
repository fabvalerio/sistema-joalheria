<?php

use App\Models\Relatorios\Controller;

$tipo = $_GET['tipo'] ?? null;
$inicio = $_GET['data_inicio'] ?? date('Y-m-d');
$fim = $_GET['data_final'] ?? date('Y-m-d');
$vendedor_id = $_GET['vendedor_id'] ?? null;
$pagina = $_GET['pagina'] ?? 1;
$imprimir = $_GET['imprimir'] ?? null;

$controller = new Controller();

if ($imprimir == '1') {
    $contas = $controller->vendasParaImprimir($tipo, $inicio, $fim, $vendedor_id);
    $r = $controller->somaVendas($inicio, $fim);
    include 'vendas_imprimir.php';
    exit;
}

$contas = $controller->vendas($tipo, $inicio, $fim, $pagina, 10, $url."/!/Relatorios/vendas/&tipo=".$tipo."&data_inicio=".$inicio."&data_final=".$fim."&vendedor_id=".$vendedor_id, $vendedor_id);
$r = $controller->somaVendas($inicio, $fim);
$pagamentos = $controller->somaVendasPorPagamento($inicio, $fim);
$vendedores = $controller->listarVendedores();

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Vendas - Relatórios</h3>
    </div>

    <div class="m-3">
        <div class="row g-3">
            <div class="col-12">
                <div class="card card-body">
                    <h6 class="card-title">Filtros</h6>
                    <form id="filtroForm">
                        <div class="row g-3 d-flex align-items-end">
                            <div class="col-lg-3">
                                <label class="form-label fw-bold">Tipo</label>
                                <select name="tipo" id="tipo" class="form-select">
                                    <option value="" <?php echo ($tipo == '') ? 'selected' : ''; ?>>Todos</option>
                                    <option value="Crédito" <?php echo ($tipo == 'Crédito') ? 'selected' : ''; ?>>Cartão de Crédito</option>
                                    <option value="Débito" <?php echo ($tipo == 'Débito') ? 'selected' : ''; ?>>Cartão de Débito</option>
                                    <option value="Dinheiro" <?php echo ($tipo == 'Dinheiro') ? 'selected' : ''; ?>>Dinheiro</option>
                                    <option value="Cheque" <?php echo ($tipo == 'Cheque') ? 'selected' : ''; ?>>Cheque</option>
                                    <option value="Carnê" <?php echo ($tipo == 'Carnê') ? 'selected' : ''; ?>>Carnê</option>
                                    <option value="Pix" <?php echo ($tipo == 'Pix') ? 'selected' : ''; ?>>Pix</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label fw-bold">Vendedora</label>
                                <select name="vendedor_id" id="vendedor_id" class="form-select">
                                    <option value="">Todas</option>
                                    <?php foreach ($vendedores as $v): ?>
                                        <option value="<?php echo $v['id']; ?>" <?php echo ($vendedor_id == $v['id']) ? 'selected' : ''; ?>>
                                            <?php echo $v['nome_completo']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label fw-bold">Data Inicial</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?php echo $inicio; ?>">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label fw-bold">Data Final</label>
                                <input type="date" name="data_final" id="data_final" class="form-control" value="<?php echo $fim; ?>">
                            </div>
                            <div class="col-lg-2 d-flex gap-2 align-items-end">
                                <a class="btn btn-success submit">FILTRAR</a>
                                <a class="btn btn-danger" href="<?php echo "{$url}!/Relatorios/vendas"; ?>">LIMPAR</a>
                                <button type="button" class="btn btn-primary" onclick="imprimirVendas()">🖨️</button>
                            </div>
                        </div>
                    </form>
                <script>
                        $(document).ready(function() {
                            $(".submit").click(function(event) {
                                event.preventDefault();
                                let tipo = $("#tipo").val();
                                let vendedorId = $("#vendedor_id").val();
                                let dataInicio = $("#data_inicio").val();
                                let dataFinal = $("#data_final").val();

                                let url = "/sistema-joias/!/Relatorios/vendas/&tipo=" + encodeURIComponent(tipo) + 
                                        "&vendedor_id=" + encodeURIComponent(vendedorId) +
                                        "&data_inicio=" + encodeURIComponent(dataInicio) + 
                                        "&data_final=" + encodeURIComponent(dataFinal);
                                window.location.href = url;
                            });
                        });

                        function imprimirVendas() {
                            let tipo = $("#tipo").val();
                            let vendedorId = $("#vendedor_id").val();
                            let dataInicio = $("#data_inicio").val();
                            let dataFinal = $("#data_final").val();

                            let url = "<?php echo $url; ?>pages/Relatorios/vendas_imprimir.php?tipo=" + encodeURIComponent(tipo) + 
                                    "&vendedor_id=" + encodeURIComponent(vendedorId) +
                                    "&data_inicio=" + encodeURIComponent(dataInicio) + 
                                    "&data_final=" + encodeURIComponent(dataFinal) +
                                    "&imprimir=1";
                            window.open(url, '_blank');
                        }
                </script>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-body border-success">
                    <h6 class="card-title">Pago R$</h6>
                    <span class="fs-5 fw-bold text-success"><?php echo number_format($r[0]['Pago'] ?? 0, 2, ',', '.'); ?></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body border-warning">
                    <h6 class="card-title">Pendente R$</h6>
                    <span class="fs-5 fw-bold text-warning"><?php echo number_format($r[0]['Pendente'] ?? 0, 2, ',', '.'); ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-body border-primary">
                    <h6 class="card-title">Vendas por Forma de Pagamento</h6>
                    <div class="row g-2">
                        <div class="col-4">
                            <small class="text-muted">Dinheiro</small>
                            <div class="fw-bold">R$ <?php echo number_format($pagamentos['dinheiro'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Cartão</small>
                            <div class="fw-bold">R$ <?php echo number_format($pagamentos['cartao'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Cheque</small>
                            <div class="fw-bold">R$ <?php echo number_format($pagamentos['cheque'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Carnê</small>
                            <div class="fw-bold">R$ <?php echo number_format($pagamentos['carne'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Pix</small>
                            <div class="fw-bold">R$ <?php echo number_format($pagamentos['deposito'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Total Geral</small>
                            <div class="fw-bold text-primary">R$ <?php echo number_format($pagamentos['total_geral'] ?? 0, 2, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <table id="vendas" class="table table-striped">
            <thead>
                <tr>
                    <th width="80">Venda</th>
                    <th>Data</th>
                    <th>Vendedora</th>
                    <th>Cliente</th>
                    <th>Forma Pagamento</th>
                    <th>Valor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contas['registros'])): ?>
                    <?php foreach ($contas['registros'] as $conta): ?>
                        <tr>
                            <td><?php echo str_pad($conta['id'], 6, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo (dia($conta['data_pedido']) ?? 'N/A'); ?></td>
                            <td><?php echo ($conta['vendedor_nome'] ?? '-'); ?></td>
                            <td><?php echo ($conta['nome_pf'] ?? $conta['nome_fantasia_pj']); ?></td>
                            <td><?php echo ($conta['forma_pagamento'] ?? '-'); ?></td>
                            <td>R$ <?php echo number_format((float) ($conta['total'] ?? 0), 2, ',', '.'); ?></td>
                            <td>
                                <?php
                                echo ($conta['status_pedido'] ?? '') === 'Pendente'
                                    ? '<span class="badge bg-warning">Pendente</span>'
                                    : '<span class="badge bg-success">Pago</span>';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Nenhuma venda encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Paginação -->
<?php echo $contas['navegacaoHtml']; ?>