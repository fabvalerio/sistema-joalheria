<?php

use App\Models\Relatorios\Controller;

$tipo = $_GET['tipo'] ?? null;
$inicio = $_GET['data_inicio'] ?? null;
$fim = $_GET['data_final'] ?? null;
$pagina = $_GET['pagina'] ?? 1;

// Instanciar o Controller
$controller = new Controller();

// Listar contas com paginação
$contas = $controller->listaPages($tipo, $inicio, $fim, $pagina, 10, $url_completa);
$r = $controller->soma($inicio, $fim);

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Contas Financeiras - Relatórios</h3>
    </div>

    <div class="m-3">
        <div class="row g-3">
            <div class="col-12">
                <div class="card card-body">
                    <h6 class="card-title">Filtros</h6>
                    <form id="filtroForm">
                        <div class="row g-3 d-flex align-items-end">
                            <div class="col-lg-4">
                                <label class="form-label fw-bold">Tipo</label>
                                <select name="tipo" id="tipo" class="form-select">
                                    <option value="" <?php echo ($tipo == '') ? 'selected' : ''; ?>>Todos</option>
                                    <option value="Pendente" <?php echo ($tipo == 'Pendente') ? 'selected' : ''; ?>>Pendente</option>
                                    <option value="Pago" <?php echo ($tipo == 'Pago') ? 'selected' : ''; ?>>Pago</option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label fw-bold">Período Inicial</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?php echo $inicio; ?>">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label fw-bold">Período Final</label>
                                <input type="date" name="data_final" id="data_final" class="form-control" value="<?php echo $fim; ?>">
                            </div>
                            <div class="col-lg-2">
                            <a class="btn btn-success submit">FILTRAR</a>
                                <a class="btn btn-danger" href="<?php echo "{$url}!/Relatorios/financeiros"; ?>">LIMPAR</a>
                            </div>
                        </div>
                    </form>
                <script>
                        $(document).ready(function() {
                            $(".submit").click(function(event) {
                                event.preventDefault(); // Evita que o link redirecione

                                let tipo = $("#tipo").val();
                                let dataInicio = $("#data_inicio").val();
                                let dataFinal = $("#data_final").val();

                                // Monta a URL com os parâmetros
                                let url = "/!/Relatorios/financeiros/&tipo=" + encodeURIComponent(tipo) + 
                                        "&data_inicio=" + encodeURIComponent(dataInicio) + 
                                        "&data_final=" + encodeURIComponent(dataFinal);

                                // Redireciona para a nova URL
                                window.location.href = url;
                            });
                        });
                </script>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-body border-success">
                    <h6 class="card-title">Entrada R$</h6>
                    <?php echo number_format($r[0]['total_receber'] ?? 0, 2, ',', '.'); ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-body border-danger">
                    <h6 class="card-title">Saída R$</h6>
                    <?php echo number_format($r[0]['total_pagar'] ?? 0, 2, ',', '.'); ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-body border-info">
                    <h6 class="card-title">Total Líquido R$</h6>
                    <?php echo number_format(($r[0]['total_receber'] ?? 0) - ($r[0]['total_pagar'] ?? 0), 2, ',', '.'); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Data Vencimento</th>
                    <th>Valor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contas['registros'])): ?>
                    <?php foreach ($contas['registros'] as $conta): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($conta['id'] ?? 'N/A'); ?></td>
                            <td>
                                <?php
                                echo ($conta['tipo'] ?? '') === 'P'
                                    ? '<span class="badge bg-danger">Contas a Pagar</span>'
                                    : '<span class="badge bg-success">Contas a Receber</span>';
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($conta['data_vencimento'] ?? 'N/A'); ?></td>
                            <td>R$ <?php echo number_format((float) ($conta['valor'] ?? 0), 2, ',', '.'); ?></td>
                            <td>
                                <span class="badge bg-<?php echo ($conta['status'] ?? 'Pendente') == 'Pago' ? 'success' : 'warning'; ?>">
                                    <?php echo htmlspecialchars($conta['status'] ?? 'Pendente'); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Nenhuma conta encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Paginação -->
<?php echo $contas['navegacaoHtml']; ?>