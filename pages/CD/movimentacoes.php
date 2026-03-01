<?php

use App\Models\Relatorios\Controller;

$controller = new Controller();

// Busca IDs dos CDs
$db = new db();
$db->query("SELECT id FROM loja WHERE status = 1 AND tipo = 'CD'");
$cds = $db->resultSet();
$cd_ids = array_column($cds, 'id');

$tipo = $_GET['tipo'] ?? null;
$inicio = $_GET['data_inicio'] ?? null;
$fim = $_GET['data_final'] ?? null;
$loja_id = $_GET['loja_id'] ?? ($cd_ids[0] ?? null); // Filtra por CD por padrão
$pagina = $_GET['pagina'] ?? 1;

$lojasCD = $controller->listarLojas();
$lojasCD = array_filter($lojasCD, fn($l) => ($l['tipo'] ?? '') === 'CD');

$movimentacoes = $controller->movimentos(
    $tipo,
    $inicio,
    $fim,
    $pagina,
    15,
    $url . "!/CD/movimentacoes&tipo=" . $tipo . "&data_inicio=" . $inicio . "&data_final=" . $fim . "&loja_id=" . $loja_id,
    $loja_id
);

?>

<div class="card">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">
            <i class="fas fa-exchange-alt me-2"></i>Movimentações do CD
        </h3>
        <a href="<?= $url ?>!/CD/estoque" class="btn btn-outline-light btn-sm">
            <i class="fas fa-warehouse me-1"></i>Estoque do CD
        </a>
    </div>

    <div class="m-3">
        <div class="card card-body">
            <h6 class="card-title">Filtros</h6>
            <form method="GET" action="<?= $url ?>!/CD/movimentacoes" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="!/CD/movimentacoes">
                <div class="col-lg-2">
                    <label class="form-label fw-bold">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="" <?= $tipo === '' ? 'selected' : '' ?>>Todos</option>
                        <option value="Entrada" <?= $tipo === 'Entrada' ? 'selected' : '' ?>>Entrada</option>
                        <option value="Saida" <?= $tipo === 'Saida' ? 'selected' : '' ?>>Saída</option>
                    </select>
                </div>
                <?php if (count($lojasCD) > 1): ?>
                <div class="col-lg-3">
                    <label class="form-label fw-bold">CD</label>
                    <select name="loja_id" class="form-select">
                        <?php foreach ($lojasCD as $cd): ?>
                            <option value="<?= $cd['id'] ?>" <?= $loja_id == $cd['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cd['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-lg-2">
                    <label class="form-label fw-bold">Data Inicial</label>
                    <input type="date" name="data_inicio" class="form-control" value="<?= $inicio ?>">
                </div>
                <div class="col-lg-2">
                    <label class="form-label fw-bold">Data Final</label>
                    <input type="date" name="data_final" class="form-control" value="<?= $fim ?>">
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-success">FILTRAR</button>
                    <a href="<?= $url ?>!/CD/movimentacoes" class="btn btn-danger">LIMPAR</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Loja</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Estoque Atual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimentacoes['registros'] ?? [] as $m): ?>
                        <tr>
                            <td><strong><?= $m['produto_id'] ?></strong> - <?= htmlspecialchars($m['descricao_produto'] ?? '') ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($m['loja_nome'] ?? '-') ?></span></td>
                            <td><span class="badge bg-<?= ($m['tipo_movimentacao'] ?? '') === 'Entrada' ? 'success' : 'danger' ?>">
                                    <?= $m['tipo_movimentacao'] ?? '-' ?>
                                </span></td>
                            <td><span class="badge bg-info"><?= $m['quantidade'] ?? 0 ?></span></td>
                            <td><?= $m['atual'] ?? '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (empty($movimentacoes['registros'] ?? [])): ?>
            <div class="alert alert-info">Nenhuma movimentação encontrada para os filtros selecionados.</div>
        <?php endif; ?>
        <?= $movimentacoes['navegacaoHtml'] ?? '' ?>
    </div>
</div>
