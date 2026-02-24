<?php

use App\Models\Relatorios\Controller;

$tipo = $_GET['tipo'] ?? null;
$inicio = $_GET['data_inicio'] ?? null;
$fim = $_GET['data_final'] ?? null;
$loja_id = $_GET['loja_id'] ?? null;
$pagina = $_GET['pagina'] ?? 1;
$aba = $_GET['aba'] ?? 'movimentos';

$controller = new Controller();
$lojas = $controller->listarLojas();
$movimentacoes = $controller->movimentos($tipo, $inicio, $fim, $pagina, 10, $url."/!/Relatorios/estoque/&tipo=".$tipo."&data_inicio=".$inicio."&data_final=".$fim."&loja_id=".$loja_id."&aba=".$aba, $loja_id);
$estoqueLoja = $controller->estoquePorLoja($loja_id);

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Relatório de Estoque</h3>
    </div>

    <div class="m-3">
        <ul class="nav nav-tabs" id="abaEstoque">
            <li class="nav-item">
                <a class="nav-link <?= $aba == 'movimentos' ? 'active' : '' ?>" href="<?= $url ?>!/Relatorios/estoque/&aba=movimentos">Movimentações</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $aba == 'por_loja' ? 'active' : '' ?>" href="<?= $url ?>!/Relatorios/estoque/&aba=por_loja">Estoque por Loja</a>
            </li>
        </ul>
    </div>

<?php if ($aba == 'movimentos'): ?>
    <div class="m-3">
        <div class="card card-body">
            <h6 class="card-title">Filtros</h6>
            <form id="filtroForm">
                <div class="row g-3 d-flex align-items-end">
                    <div class="col-lg-3">
                        <label class="form-label fw-bold">Tipo</label>
                        <select name="tipo" id="tipo" class="form-select">
                            <option value="" <?= ($tipo == '') ? 'selected' : '' ?>>Todos</option>
                            <option value="Entrada" <?= ($tipo == 'Entrada') ? 'selected' : '' ?>>Entrada</option>
                            <option value="Saida" <?= ($tipo == 'Saida') ? 'selected' : '' ?>>Saida</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label fw-bold">Loja</label>
                        <select name="loja_id" id="loja_id_filtro" class="form-select">
                            <option value="">Todas</option>
                            <?php foreach ($lojas as $loja): ?>
                                <option value="<?= $loja['id'] ?>" <?= $loja_id == $loja['id'] ? 'selected' : '' ?>><?= htmlspecialchars($loja['nome']) ?> (<?= $loja['tipo'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Período Inicial</label>
                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?= $inicio ?>">
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Período Final</label>
                        <input type="date" name="data_final" id="data_final" class="form-control" value="<?= $fim ?>">
                    </div>
                    <div class="col-lg-2">
                        <a class="btn btn-success submit">FILTRAR</a>
                        <a class="btn btn-danger" href="<?= "{$url}!/Relatorios/estoque" ?>">LIMPAR</a>
                    </div>
                </div>
            </form>
            <script>
                $(document).ready(function() {
                    $(".submit").click(function(event) {
                        event.preventDefault();
                        let tipo = $("#tipo").val();
                        let dataInicio = $("#data_inicio").val();
                        let dataFinal = $("#data_final").val();
                        let lojaId = $("#loja_id_filtro").val();
                        let url = "<?= $url ?>!/Relatorios/estoque/&tipo=" + encodeURIComponent(tipo) +
                            "&data_inicio=" + encodeURIComponent(dataInicio) +
                            "&data_final=" + encodeURIComponent(dataFinal) +
                            "&loja_id=" + encodeURIComponent(lojaId) +
                            "&aba=movimentos";
                        window.location.href = url;
                    });
                });
            </script>
        </div>
    </div>

    <div class="card-body">
        <table id="example1" class="table table-striped">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Loja</th>
                    <th>Tipo</th>
                    <th>Movimentação</th>
                    <th>Estoque Atual</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimentacoes['registros'] as $m): ?>
                    <tr>
                        <td><strong><?= $m['produto_id'] ?></strong> - <?= $m['descricao_produto'] ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($m['loja_nome'] ?? 'Global') ?></span></td>
                        <td><span class="badge bg-<?= $m['tipo_movimentacao'] == 'Entrada' ? 'success' : 'danger' ?>">
                                <?= $m['tipo_movimentacao'] ?>
                            </span>
                        </td>
                        <td><span class="badge bg-success"><?= $m['quantidade'] ?></span></td>
                        <td><span class="badge bg-success"><?= $m['atual'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= $movimentacoes['navegacaoHtml'] ?>

<?php elseif ($aba == 'por_loja'): ?>
    <div class="m-3">
        <div class="card card-body">
            <h6 class="card-title">Filtros</h6>
            <div class="row g-3 d-flex align-items-end">
                <div class="col-lg-6">
                    <label class="form-label fw-bold">Loja</label>
                    <select name="loja_id" id="loja_id_filtro2" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach ($lojas as $loja): ?>
                            <option value="<?= $loja['id'] ?>" <?= $loja_id == $loja['id'] ? 'selected' : '' ?>><?= htmlspecialchars($loja['nome']) ?> (<?= $loja['tipo'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-4">
                    <a class="btn btn-success" id="filtrarLoja">FILTRAR</a>
                    <a class="btn btn-danger" href="<?= "{$url}!/Relatorios/estoque/&aba=por_loja" ?>">LIMPAR</a>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    $("#filtrarLoja").click(function(event) {
                        event.preventDefault();
                        let lojaId = $("#loja_id_filtro2").val();
                        window.location.href = "<?= $url ?>!/Relatorios/estoque/&aba=por_loja&loja_id=" + encodeURIComponent(lojaId);
                    });
                });
            </script>
        </div>
    </div>

    <div class="card-body">
        <?php
        $estoqueAgrupado = [];
        foreach ($estoqueLoja as $item) {
            $lid = $item['loja_id'];
            if (!isset($estoqueAgrupado[$lid])) {
                $estoqueAgrupado[$lid] = [
                    'nome' => $item['loja_nome'],
                    'tipo' => $item['loja_tipo'],
                    'itens' => [],
                    'total' => 0
                ];
            }
            $estoqueAgrupado[$lid]['itens'][] = $item;
            $estoqueAgrupado[$lid]['total'] += (float)$item['quantidade'];
        }
        ?>

        <?php if (empty($estoqueAgrupado)): ?>
            <div class="alert alert-info">Nenhum estoque encontrado para os filtros selecionados.</div>
        <?php else: ?>
            <?php foreach ($estoqueAgrupado as $lid => $lojaData): ?>
                <div class="card mb-4 border-left-primary">
                    <div class="card-header bg-light d-flex justify-content-between">
                        <h5 class="mb-0">
                            <i class="fas fa-<?= $lojaData['tipo'] == 'CD' ? 'warehouse' : 'store' ?>"></i>
                            <?= htmlspecialchars($lojaData['nome']) ?>
                            <span class="badge bg-secondary"><?= $lojaData['tipo'] ?></span>
                        </h5>
                        <span class="badge bg-primary fs-6"><?= $lojaData['total'] ?> peças</span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Produto</th>
                                    <th class="text-center">Quantidade</th>
                                    <th class="text-center">Qtd Mínima</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lojaData['itens'] as $item): ?>
                                    <tr>
                                        <td><?= str_pad($item['produto_id'], 6, '0', STR_PAD_LEFT) ?></td>
                                        <td><?= htmlspecialchars($item['nome_produto'] ?? '') ?></td>
                                        <td class="text-center"><span class="badge bg-info"><?= $item['quantidade'] ?></span></td>
                                        <td class="text-center"><?= $item['quantidade_minima'] ?></td>
                                        <td class="text-center">
                                            <?php if ($item['quantidade'] <= $item['quantidade_minima'] && $item['quantidade_minima'] > 0): ?>
                                                <span class="badge bg-danger">Baixo</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">OK</span>
                                            <?php endif; ?>
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
<?php endif; ?>

</div>