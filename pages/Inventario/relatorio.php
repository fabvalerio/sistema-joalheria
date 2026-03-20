<?php

use App\Models\Inventario\Controller;
use App\Models\Estoque\Controller as EstoqueController;

$controller = new Controller();
$estoqueCtrl = new EstoqueController();

$inicio = $_GET['data_inicio'] ?? null;
$fim = $_GET['data_final'] ?? null;
$motivo = trim($_GET['motivo'] ?? '');
$tipo = $_GET['tipo'] ?? null;
$loja_id = $_GET['loja_id'] ?? null;
$pagina = (int)($_GET['pagina'] ?? 1);

$lojas = $estoqueCtrl->listarLojas();
$urlBase = $url . '!/Inventario/relatorio/?data_inicio=' . urlencode($inicio ?? '') . '&data_final=' . urlencode($fim ?? '') . '&motivo=' . urlencode($motivo) . '&tipo=' . urlencode($tipo ?? '') . '&loja_id=' . urlencode($loja_id ?? '');
$resultado = $controller->listarDevolucoes($inicio, $fim, $motivo ?: null, $loja_id ?: null, $tipo ?: null, $pagina, 15, $urlBase);
$registros = $resultado['registros'];
$navegacaoHtml = $resultado['navegacaoHtml'];
$total = $resultado['total'];
?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="fas fa-chart-bar me-2"></i>Relatório de Inventário por Período</h3>
        <a href="<?= $url ?>!/Inventario/listar" class="btn btn-outline-light btn-sm">Voltar</a>
    </div>

    <div class="m-3">
        <div class="card card-body">
            <h6 class="card-title">Filtros</h6>
            <form id="filtroForm">
                <div class="row g-3 d-flex align-items-end">
                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Data Início</label>
                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?= htmlspecialchars($inicio ?? '') ?>">
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Data Fim</label>
                        <input type="date" name="data_final" id="data_final" class="form-control" value="<?= htmlspecialchars($fim ?? '') ?>">
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Tipo</label>
                        <select name="tipo" id="tipo_filtro" class="form-select">
                            <option value="">Todos</option>
                            <option value="Entrada" <?= ($tipo ?? '') === 'Entrada' ? 'selected' : '' ?>>Entrada</option>
                            <option value="Saida" <?= ($tipo ?? '') === 'Saida' ? 'selected' : '' ?>>Saída</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Motivo</label>
                        <input type="text" name="motivo" id="motivo" class="form-control" placeholder="Filtrar motivo..." value="<?= htmlspecialchars($motivo) ?>">
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Estoque</label>
                        <select name="loja_id" id="loja_id_filtro" class="form-select">
                            <option value="">Todas</option>
                            <?php foreach ($lojas as $loja): ?>
                                <option value="<?= $loja['id'] ?>" <?= ($loja_id ?? '') == $loja['id'] ? 'selected' : '' ?>><?= htmlspecialchars($loja['nome']) ?> (<?= $loja['tipo'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <button type="button" class="btn btn-success" id="btnFiltrar">FILTRAR</button>
                        <a class="btn btn-danger" href="<?= $url ?>!/Inventario/relatorio">LIMPAR</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card-body">
        <p class="text-muted">
            <?php if ($inicio && $fim): ?>
                Exibindo registros de <?= date('d/m/Y', strtotime($inicio)) ?> a <?= date('d/m/Y', strtotime($fim)) ?>.
            <?php else: ?>
                Informe o período para filtrar os registros de inventário.
            <?php endif; ?>
            Total: <strong><?= $total ?></strong> registro(s).
        </p>

        <table class="table table-striped table-hover">
            <thead>
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
                <?php if (empty($registros)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Nenhum registro encontrado para os filtros selecionados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($registros as $d): ?>
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

        <?= $navegacaoHtml ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#btnFiltrar').on('click', function(e) {
        e.preventDefault();
        const dataInicio = $('#data_inicio').val();
        const dataFinal = $('#data_final').val();
        const motivo = $('#motivo').val();
        const tipo = $('#tipo_filtro').val();
        const lojaId = $('#loja_id_filtro').val();
        let url = "<?= $url ?>!/Inventario/relatorio/?data_inicio=" + encodeURIComponent(dataInicio) +
            "&data_final=" + encodeURIComponent(dataFinal) +
            "&motivo=" + encodeURIComponent(motivo) +
            "&tipo=" + encodeURIComponent(tipo) +
            "&loja_id=" + encodeURIComponent(lojaId);
        window.location.href = url;
    });
});
</script>
