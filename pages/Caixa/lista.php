<?php

use App\Models\Caixa\Controller;

$controller = new Controller();

// Obter parâmetros de data
$data_inicio =$link[3] ?? date('Y-m-d');
$data_fim = $link[4] ?? date('Y-m-d');

// Obter dados do fluxo de caixa
$movimentacoes = $controller->listarMovimentacao($data_inicio, $data_fim);
$totais = $controller->obterTotais($data_inicio, $data_fim);
$config_troco = $controller->obterConfiguracaoTroco($data_fim);
$receb_parcelas = $controller->obterRecebimentoParcelas($data_inicio, $data_fim);

// Processar POST para salvar configuração de troco
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['salvar_troco'])) {
    $controller->salvarConfiguracaoTroco(
        $_POST['data_troco'],
        $_POST['troco_abertura'],
        $_POST['troco_fechamento']
    );
    // Recarregar a página para mostrar os dados atualizados
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Movimento de Caixa</h3>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-white text-primary" onclick="imprimirDivPrint()">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="card-body">
        <!-- Filtros de Data -->
        <div class="row mb-3">
            <div class="col-md-6">
                <form id="filtroDataForm" class="d-flex gap-2" onsubmit="return redirecionarFiltroData();">
                    <input type="date" id="data_inicio" name="data_inicio" value="<?= $data_inicio ?>" class="form-control">
                    <input type="date" id="data_fim" name="data_fim" value="<?= $data_fim ?>" class="form-control">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </form>
                <script>
                    function redirecionarFiltroData() {
                        const dataInicio = document.getElementById('data_inicio').value;
                        const dataFim = document.getElementById('data_fim').value;
                        let urlBase = "<?= $url ?? '' ?>";
                        if (!urlBase.endsWith('/')) urlBase += '/';
                        window.location.href = urlBase + "!/Caixa/lista/" + dataInicio + "/" + dataFim;
                        return false;
                    }
                </script>
            </div>
        </div>

        <div id="print">

            <div class="row">
                    
                <div class="col-md-12 text-end">
                    <small class="text-muted">
                        Relatório gerado em: <?= date('d/m/Y H:i:s') ?>
                    </small>
                </div>
            </div>                    

            <!-- Tabela de Movimentação -->
            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th>Data</th>
                            <!-- <th>Hr</th> -->
                            <th>Cliente</th>
                            <th>Ped.</th>
                            <th>Total</th>
                            <!-- <th>Sinal</th> -->
                            <!-- <th>Dinheiro</th> -->
                            <!-- <th>Cheque</th> -->
                            <!-- <th>Parc. Cartão</th> -->
                            <!-- <th>Parcela</th> -->
                            <!-- <th>Carnet</th> -->
                            <!-- <th>Parc.</th> -->
                            <!-- <th>Val.Vend. Ouro</th> -->
                            <!-- <th>Dp.Banc</th> -->
                            <!-- <th>Venda<th> -->
                            <!-- <th>Lib.</th> -->
                            <!-- <th>Rec</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimentacoes as $mov): ?>
                            <tr>
                                <td><?= ($mov['data_pedido']) ?></td>
                                <!-- <td><?= date('H:i', strtotime($mov['hora_pedido'])) ?></td> -->
                                <td>
                                    <?= htmlspecialchars($mov['nome_pf'] ?: $mov['nome_fantasia_pj'] ?: 'Cliente não informado') ?>
                                </td>
                                <td><?= $mov['pedido_id'] ?></td>
                                <td>R$ <?= number_format($mov['total'] ?: 0, 2, ',', '.') ?></td>
                                <!-- <td>
                                    <?= ($mov['valor_pago'] ?: 0) < ($mov['total'] ?: 0) ? 'R$ ' . number_format(($mov['total'] ?: 0) - ($mov['valor_pago'] ?: 0), 2, ',', '.') : '' ?>
                                </td> 
                                <td>
                                    <?= ($mov['dinheiro'] ?: 0) > 0 ? 'R$ ' . number_format($mov['dinheiro'], 2, ',', '.') : '' ?>
                                </td>
                                <td>
                                    <?= ($mov['cheque'] ?: 0) > 0 ? 'R$ ' . number_format($mov['cheque'], 2, ',', '.') : '' ?>
                                </td> 
                                <td>
                                    <?= ($mov['parc_cartao'] ?: 0) > 0 ? 'R$ ' . number_format($mov['parc_cartao'], 2, ',', '.') : '' ?>
                                </td>
                                <td>
                                    <?= ($mov['parcela'] ?: 1) > 1 ? number_format($mov['parcela'], 0, ',', '.') : '' ?>
                                </td>
                                <td>
                                    <?= ($mov['carnet'] ?: 0) > 0 ? 'R$ ' . number_format($mov['carnet'], 2, ',', '.') : '' ?>
                                </td>
                                <td>
                                    <?= ($mov['parcela'] ?: 1) > 1 ? number_format($mov['parcela'], 0, ',', '.') : '' ?>
                                </td>
                                <td>
                                    <?= ($mov['val_vend_ouro'] ?: 0) > 0 ? 'R$ ' . number_format($mov['val_vend_ouro'], 2, ',', '.') : '' ?>
                                </td>
                                <td><?= $mov['dp_banc'] ?: 0 ?></td> -->
                                <!-- <td><?= $mov['vend'] ?: 0 ?></td> -->
                                <!-- <td><?= $mov['lib'] ?: 0 ?></td>
                                <td><?= $mov['rec'] ?: 0 ?></td> -->
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Totais -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Resumo do Dia</h6>
                            <div class="row">
                                <div class="col-6">
                                    <p><strong>Total de Pedidos:</strong><br>
                                    R$ <?= number_format($totais['total_pedidos_valor'] ?: 0, 2, ',', '.') ?></p>
                                    
                                    <p><strong>Total Líquido:</strong><br>
                                    R$ <?= number_format($totais['total_liquido'] ?: 0, 2, ',', '.') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Recebimento de Parcelas</h6>
                            <div class="text-center">
                                <h4 class="text-primary">R$ <?= number_format($receb_parcelas ?: 0, 2, ',', '.') ?></h4>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>

        </div>
    </div>
</div>

<!-- Modal para Configurar Troco -->
<div class="modal fade" id="modalTroco" tabindex="-1" aria-labelledby="modalTrocoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTrocoLabel">Configurar Troco</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="data_troco" class="form-label">Data:</label>
                        <input type="date" class="form-control" id="data_troco" name="data_troco" value="<?= $data_fim ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="troco_abertura" class="form-label">Troco de Abertura (R$):</label>
                        <input type="number" step="0.01" class="form-control" id="troco_abertura" name="troco_abertura" 
                               value="<?= $config_troco['troco_abertura'] ?: 0 ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="troco_fechamento" class="form-label">Troco de Fechamento (R$):</label>
                        <input type="number" step="0.01" class="form-control" id="troco_fechamento" name="troco_fechamento" 
                               value="<?= $config_troco['troco_fechamento'] ?: 0 ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="salvar_troco" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@media print {
    .card-header .btn,
    .modal,
    .d-flex.gap-2 {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 12px;
    }
    
    .table th,
    .table td {
        padding: 4px !important;
    }
}
</style>

<script>
function imprimirDivPrint() {
    // Obter o conteúdo da div com id "print"
    const conteudoPrint = document.getElementById('print').innerHTML;
    
    // Criar uma nova janela para impressão
    const janelaImpressao = window.open('', '_blank', 'width=800,height=600');
    
    // Escrever o conteúdo na nova janela
    janelaImpressao.document.write(`
        <html>
        <head>
            <title>Movimento de Caixa - Impressão</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    font-size: 12px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
                .card {
                    border: 1px solid #ddd;
                    padding: 15px;
                    margin-bottom: 20px;
                }
                .card-title {
                    font-size: 16px;
                    font-weight: bold;
                    margin-bottom: 10px;
                }
                .text-end {
                    text-align: right;
                }
                .text-muted {
                    color: #6c757d;
                }
                .bg-light {
                    background-color: #f8f9fa;
                    padding: 10px;
                }
                .mt-4 {
                    margin-top: 1.5rem;
                }
                .row {
                    display: flex;
                    flex-wrap: wrap;
                }
                .col-md-12 {
                    flex: 0 0 100%;
                    max-width: 100%;
                }
                .col-6 {
                    flex: 0 0 50%;
                    max-width: 50%;
                }
                p {
                    margin: 5px 0;
                }
                strong {
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <h2>Movimento de Caixa - Loja 1</h2>
            ${conteudoPrint}
        </body>
        </html>
    `);
    
    // Fechar o documento e abrir a impressão
    janelaImpressao.document.close();
    janelaImpressao.focus();
    janelaImpressao.print();
    janelaImpressao.close();
}
</script>
