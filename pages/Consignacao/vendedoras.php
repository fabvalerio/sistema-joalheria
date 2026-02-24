<?php

use App\Models\Consignacao\Controller;

$controller = new Controller();
$cliente_id = $_GET['cliente_id'] ?? null;
$itens = $controller->produtosPorVendedora($cliente_id);

$vendedoras = [];
foreach ($itens as $item) {
    $vid = $item['cliente_id'];
    if (!isset($vendedoras[$vid])) {
        $vendedoras[$vid] = [
            'nome' => !empty($item['nome_pf']) ? $item['nome_pf'] : ($item['nome_fantasia_pj'] ?? 'N/A'),
            'telefone' => $item['telefone'] ?? '',
            'whatsapp' => $item['whatsapp'] ?? '',
            'itens' => [],
            'total_pecas' => 0,
            'total_valor' => 0
        ];
    }
    $vendedoras[$vid]['itens'][] = $item;
    $vendedoras[$vid]['total_pecas'] += (float)$item['em_maos'];
    $vendedoras[$vid]['total_valor'] += (float)$item['em_maos'] * (float)$item['valor'];
}

$clientes = $controller->listarClientes();

?>

<link href="<?php echo $url?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="<?php echo $url?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo $url?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Produtos em Mãos por Vendedora</h3>
        <a href="<?php echo "{$url}!/Consignacao/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="m-3">
        <div class="card card-body">
            <h6 class="card-title">Filtro por Vendedora</h6>
            <form id="filtroForm" class="row g-3 align-items-end">
                <div class="col-lg-8">
                    <select name="cliente_id" id="cliente_id" class="form-select">
                        <option value="">Todas as Vendedoras</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $cliente_id == $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars(!empty($c['nome_pf']) ? $c['nome_pf'] : ($c['nome_fantasia_pj'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-4">
                    <a class="btn btn-success submit">FILTRAR</a>
                    <a class="btn btn-danger" href="<?= "{$url}!/Consignacao/vendedoras" ?>">LIMPAR</a>
                </div>
            </form>
            <script>
                $(document).ready(function() {
                    $(".submit").click(function(event) {
                        event.preventDefault();
                        let clienteId = $("#cliente_id").val();
                        let url = "<?= $url ?>!/Consignacao/vendedoras/&cliente_id=" + encodeURIComponent(clienteId);
                        window.location.href = url;
                    });
                });
            </script>
        </div>
    </div>

    <div class="card-body">
        <?php if (empty($vendedoras)): ?>
            <div class="alert alert-info">Nenhuma consignação aberta com produtos em mãos.</div>
        <?php else: ?>
            <?php foreach ($vendedoras as $vid => $vendedora): ?>
                <div class="card mb-4 border-left-primary">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">
                                    <i class="fas fa-user"></i> <?= htmlspecialchars($vendedora['nome']) ?>
                                </h5>
                                <small class="text-muted">
                                    <?php if ($vendedora['whatsapp']): ?>
                                        <i class="fab fa-whatsapp text-success"></i> <?= $vendedora['whatsapp'] ?>
                                    <?php endif; ?>
                                    <?php if ($vendedora['telefone']): ?>
                                        | <i class="fas fa-phone"></i> <?= $vendedora['telefone'] ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary fs-6"><?= $vendedora['total_pecas'] ?> peças</span>
                                <span class="badge bg-success fs-6">R$ <?= number_format($vendedora['total_valor'], 2, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Consignação</th>
                                    <th>Data</th>
                                    <th>Código</th>
                                    <th>Produto</th>
                                    <th class="text-center">Enviado</th>
                                    <th class="text-center">Devolvido</th>
                                    <th class="text-center">Em Mãos</th>
                                    <th class="text-end">Valor Un.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vendedora['itens'] as $item): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= $url ?>!/Consignacao/ver/<?= $item['consignacao_id'] ?>" class="text-primary">
                                                #<?= $item['consignacao_id'] ?>
                                            </a>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($item['data_consignacao'])) ?></td>
                                        <td><?= str_pad($item['produto_id'], 6, '0', STR_PAD_LEFT) ?></td>
                                        <td><?= htmlspecialchars($item['nome_produto'] ?? '') ?></td>
                                        <td class="text-center"><span class="badge bg-info"><?= $item['quantidade'] ?></span></td>
                                        <td class="text-center"><span class="badge bg-warning text-dark"><?= $item['qtd_devolvido'] ?></span></td>
                                        <td class="text-center"><span class="badge bg-danger"><?= $item['em_maos'] ?></span></td>
                                        <td class="text-end">R$ <?= number_format($item['valor'], 2, ',', '.') ?></td>
                                        <td class="text-end">
                                            <strong>R$ <?= number_format($item['em_maos'] * $item['valor'], 2, ',', '.') ?></strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
