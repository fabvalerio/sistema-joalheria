<?php

use App\Models\Caixa\Controller;

$controller = new Controller();

// Loja atual (definida no cookie por outras telas)
$loja_id = $_COOKIE['loja_id'] ?? 1;

// Obter parâmetros de data
$data_inicio = $link[3] ?? date('Y-m-d');
$data_fim = $link[4] ?? $data_inicio;

// Mantém a variável usada no modal legado (evita warning, mesmo que o modal fique desativado)
$config_troco = $controller->obterConfiguracaoTroco($data_inicio);

// Gaveta selecionada (opcional via GET)
$caixas = $controller->listarCaixasPorLoja($loja_id);
$caixa_drawer_id = isset($_GET['caixa_drawer_id']) && $_GET['caixa_drawer_id'] !== '' ? (int)$_GET['caixa_drawer_id'] : null;
if (!$caixa_drawer_id && !empty($caixas)) {
    $caixa_drawer_id = (int)$caixas[0]['id'];
}

// Parâmetros para lançar pedido vindo do cadastro (Pedidos/cadastro)
$pedido_lancar_id = isset($_GET['pedido_lancar']) ? (int)$_GET['pedido_lancar'] : null;
$pedido_lancar_data = isset($_GET['data_pedido']) ? $_GET['data_pedido'] : $data_inicio;

// Carrega sessão e movimentações apenas para a data de operação (data_inicio)
$sessao_aberta = ($caixa_drawer_id ? $controller->obterSessaoAberta($loja_id, $caixa_drawer_id, $data_inicio) : null);
$totais_gaveta = ($caixa_drawer_id ? $controller->obterTotaisPorGavetaData($loja_id, $caixa_drawer_id, $data_inicio) : null);
$movimentacoes_caixa = ($caixa_drawer_id ? $controller->listarMovimentacaoCaixa($data_inicio, $data_inicio, $loja_id, $caixa_drawer_id) : []);

$mostrar_modal_pedido_lancar = false;
$pedido_lancar_info = null;
$sessoes_abertas_para_lancar = [];
$pedido_lancar_eh_dinheiro = false;

// Processar POST para operações do Caixa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $operacao = $_POST['operacao'] ?? null;
    $data_caixa = $_POST['data_caixa'] ?? $data_inicio;
    $caixa_drawer_id_post = isset($_POST['caixa_drawer_id']) ? (int)$_POST['caixa_drawer_id'] : $caixa_drawer_id;
    $operador_id = $_COOKIE['id'] ?? null;
    $observacoes = $_POST['observacoes'] ?? null;

    $redirectBase = ($url ?? '') . '!/Caixa/lista/' . $data_inicio . '/' . $data_fim;
    if ($caixa_drawer_id_post) {
        $redirectBase .= '?caixa_drawer_id=' . $caixa_drawer_id_post;
    }

    if ($operacao === 'configurar_caixas') {
        $quantidade_caixas = (int)($_POST['quantidade_caixas'] ?? 0);
        if ($quantidade_caixas <= 0) {
            echo notify('danger', 'Informe a quantidade de gavetas para cadastrar.');
            echo '<meta http-equiv="refresh" content="1; url=' . $redirectBase . '">';
            exit;
        }

        $ok = $controller->garantirCaixasPorLoja($loja_id, $quantidade_caixas);
        if (!$ok) {
            echo notify('danger', 'Não foi possível cadastrar as gavetas.');
            echo '<meta http-equiv="refresh" content="1; url=' . $redirectBase . '">';
            exit;
        }

        echo notify('success', 'Gavetas cadastradas com sucesso.');
        // Recarrega sem forçar caixa_drawer_id; a tela vai selecionar a primeira gaveta disponível.
        $redirectSemDrawer = ($url ?? '') . '!/Caixa/lista/' . $data_inicio . '/' . $data_fim;
        echo '<meta http-equiv="refresh" content="1; url=' . $redirectSemDrawer . '">';
        exit;
    }

    if ($operacao === 'abrir_sessao') {
        $troco_abertura = (float)($_POST['troco_abertura'] ?? 0);
        if (!$caixa_drawer_id_post) {
            echo notify('danger', 'Selecione uma gaveta para abrir o Caixa.');
            echo '<meta http-equiv="refresh" content="1; url=' . $redirectBase . '">';
            exit;
        }

        $controller->abrirSessao($loja_id, $caixa_drawer_id_post, $data_caixa, $troco_abertura, $operador_id, $observacoes);
        echo notify('success', 'Sessão de caixa aberta com sucesso.');
        echo '<meta http-equiv="refresh" content="1; url=' . $redirectBase . '">';
        exit;
    }

    if ($operacao === 'sangria' || $operacao === 'reforco') {
        $sessao = $controller->obterSessaoAberta($loja_id, $caixa_drawer_id_post, $data_caixa);
        if (!$sessao) {
            echo notify('danger', 'Não existe sessão aberta para esta gaveta e data.');
            echo '<meta http-equiv="refresh" content="1; url=' . $redirectBase . '">';
            exit;
        }

        $valor = abs((float)($_POST['valor'] ?? 0));
        if ($valor <= 0) {
            echo notify('danger', 'Informe um valor válido para a operação.');
            echo '<meta http-equiv="refresh" content="1; url=' . $redirectBase . '">';
            exit;
        }

        $tipo = $operacao === 'sangria' ? 'Sangria' : 'Reforco';
        $valor_mov = $operacao === 'sangria' ? (-1 * $valor) : $valor;

        $controller->registrarMovimento(
            (int)$sessao['id'],
            $loja_id,
            (int)$caixa_drawer_id_post,
            $tipo,
            $valor_mov,
            'Manual',
            null,
            $observacoes
        );

        echo notify('success', ($operacao === 'sangria' ? 'Sangria' : 'Reforço') . ' registrado com sucesso.');
        echo '<meta http-equiv="refresh" content="1; url=' . $redirectBase . '">';
        exit;
    }

    if ($operacao === 'fechar_sessao') {
        $saldo_fisico_informado = (float)($_POST['saldo_fisico_informado'] ?? 0);
        $resultado = $controller->fecharSessao($loja_id, $caixa_drawer_id_post, $data_caixa, $saldo_fisico_informado, $operador_id, $observacoes);

        if ($resultado === false) {
            echo notify('danger', 'Não foi possível fechar a sessão. Verifique se ela está aberta.');
            echo '<meta http-equiv="refresh" content="1; url=' . $redirectBase . '">';
            exit;
        }

        echo notify('success', 'Caixa fechado com sucesso.');
        echo '<meta http-equiv="refresh" content="1; url=' . $redirectBase . '">';
        exit;
    }

    if ($operacao === 'lancar_pedido') {
        $pedido_id = isset($_POST['pedido_id']) ? (int)$_POST['pedido_id'] : 0;
        $caixa_drawer_id_lancar = isset($_POST['caixa_drawer_id']) ? (int)$_POST['caixa_drawer_id'] : 0;
        if (!$pedido_id || !$caixa_drawer_id_lancar) {
            echo notify('danger', 'Dados insuficientes para lançar o pedido no caixa.');
            echo '<meta http-equiv="refresh" content="2; url=' . $redirectBase . '">';
            exit;
        }
        $pedido = $controller->obterPedidoParaCaixa($pedido_id);
        if (!$pedido || ($pedido['status_pedido'] ?? '') !== 'Pago') {
            echo notify('danger', 'Pedido não encontrado ou não está pago.');
            echo '<meta http-equiv="refresh" content="2; url=' . $redirectBase . '">';
            exit;
        }
        $loja_id_pedido = (int)($pedido['loja_id'] ?? $loja_id);
        $data_pedido_val = $pedido['data_pedido'];
        $sessao = $controller->obterSessaoAberta($loja_id_pedido, $caixa_drawer_id_lancar, $data_pedido_val);
        if (!$sessao) {
            echo notify('danger', 'Não existe sessão aberta para a gaveta e data do pedido.');
            echo '<meta http-equiv="refresh" content="2; url=' . $redirectBase . '">';
            exit;
        }
        $valor = (float)($pedido['valor_pago'] ?? 0);
        if ($valor <= 0) {
            $valor = (float)($pedido['total'] ?? 0);
        }
        if ($valor <= 0) {
            echo notify('danger', 'Valor do pedido inválido para lançar no caixa.');
            echo '<meta http-equiv="refresh" content="2; url=' . $redirectBase . '">';
            exit;
        }
        $tipo = $controller->mapearTipoPedidoParaCaixa($pedido['forma_pagamento']);
        $obs = null;
        if (stripos((string)($pedido['forma_pagamento'] ?? ''), 'dinheiro') !== false) {
            $valor_recebido = (float)($_POST['valor_recebido'] ?? 0);
            if ($valor_recebido > 0) {
                $troco = $valor_recebido - $valor;
                $obs = 'Valor recebido: R$ ' . number_format($valor_recebido, 2, ',', '.') . ' | Troco: R$ ' . number_format($troco, 2, ',', '.');
            }
        }
        $controller->registrarMovimento(
            (int)$sessao['id'],
            $loja_id_pedido,
            $caixa_drawer_id_lancar,
            $tipo,
            $valor,
            'Pedido',
            (int)$pedido_id,
            $obs
        );
        echo notify('success', 'Pedido #' . $pedido_id . ' lançado no caixa.');
        $redir = ($url ?? '') . '!/Caixa/lista/' . $data_inicio . '/' . $data_fim . '?caixa_drawer_id=' . $caixa_drawer_id_lancar;
        echo '<meta http-equiv="refresh" content="1; url=' . $redir . '">';
        exit;
    }
}

// Tratar pedido_lancar vindo do cadastro (GET): 0 sessões = alerta; 1 sessão e não-dinheiro = lançar e redirecionar; dinheiro ou 2+ = exibir modal (troco + caixa)
if ($pedido_lancar_id && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $pedido_lancar_info = $controller->obterPedidoParaCaixa($pedido_lancar_id);
    if ($pedido_lancar_info && ($pedido_lancar_info['status_pedido'] ?? '') === 'Pago' && (float)($pedido_lancar_info['valor_pago'] ?? 0) > 0) {
        $loja_id_pedido = (int)($pedido_lancar_info['loja_id'] ?? $loja_id);
        $sessoes_abertas_para_lancar = $controller->listarSessoesAbertasPorLojaData($loja_id_pedido, $pedido_lancar_data) ?: [];
        $n = count($sessoes_abertas_para_lancar);
        $pedido_eh_dinheiro = (stripos((string)($pedido_lancar_info['forma_pagamento'] ?? ''), 'dinheiro') !== false);

        if ($n === 0) {
            $data_fmt = date('d/m/Y', strtotime($pedido_lancar_data));
            echo notify('warning', 'Nenhum caixa aberto para a data do pedido (' . $data_fmt . '). Abra um caixa para lançar a venda.');
            echo '<meta http-equiv="refresh" content="3; url=' . ($url ?? '') . '!/Caixa/lista/' . $data_inicio . '/' . $data_fim . '">';
            exit;
        }
        // Só faz auto-lançamento quando há 1 sessão e NÃO é dinheiro (dinheiro precisa do modal para troco)
        if ($n === 1 && !$pedido_eh_dinheiro) {
            $sessao_unica = $sessoes_abertas_para_lancar[0];
            $valor = (float)($pedido_lancar_info['valor_pago'] ?? 0);
            if ($valor <= 0) {
                $valor = (float)($pedido_lancar_info['total'] ?? 0);
            }
            $tipo = $controller->mapearTipoPedidoParaCaixa($pedido_lancar_info['forma_pagamento']);
            $controller->registrarMovimento(
                (int)$sessao_unica['id'],
                $loja_id_pedido,
                (int)$sessao_unica['caixa_drawer_id'],
                $tipo,
                $valor,
                'Pedido',
                (int)$pedido_lancar_id,
                null
            );
            $redir = ($url ?? '') . '!/Caixa/lista/' . $data_inicio . '/' . $data_fim . '?caixa_drawer_id=' . (int)$sessao_unica['caixa_drawer_id'];
            header('Location: ' . $redir);
            exit;
        }
        $mostrar_modal_pedido_lancar = true;
        $pedido_lancar_eh_dinheiro = $pedido_eh_dinheiro;
    }
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
                        const caixaDrawer = "<?= (int)($caixa_drawer_id ?? 0) ?>";
                        window.location.href = urlBase + "!/Caixa/lista/" + dataInicio + "/" + dataFim + "?caixa_drawer_id=" + caixaDrawer;
                        return false;
                    }
                </script>
            </div>
        </div>

        <!-- Operações do Caixa (Abertura, Sangria/Reforço e Fechamento) -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Gaveta (Caixa #)</label>
                <select class="form-select" onchange="window.location.href='<?= $url ?? '' ?>!/Caixa/lista/<?= $data_inicio ?>/<?= $data_fim ?>?caixa_drawer_id='+this.value">
                    <?php if (!empty($caixas)): ?>
                        <?php foreach ($caixas as $caixa): ?>
                            <option value="<?= (int)$caixa['id'] ?>" <?= ((int)$caixa['id'] === (int)($caixa_drawer_id ?? 0)) ? 'selected' : '' ?>>
                                Caixa #<?= htmlspecialchars($caixa['numero']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">Nenhuma gaveta cadastrada</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-8">
                <?php if (!$caixa_drawer_id || empty($caixas)): ?>
                    <?= notify('warning', 'Sem gavetas cadastradas para esta loja.'); ?>
                <?php else: ?>
                    <?php if ($sessao_aberta): ?>
                        <div class="alert alert-success mb-0">
                            Sessão <strong>Aberta</strong> (Caixa #<?= htmlspecialchars($caixas[0]['numero'] ?? '') ?>) em
                            <strong><?= htmlspecialchars(date('d/m/Y', strtotime($data_inicio))) ?></strong>.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary mb-0">
                            Nenhuma sessão aberta em <strong><?= htmlspecialchars(date('d/m/Y', strtotime($data_inicio))) ?></strong>.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="row mb-3">
            <?php if (empty($caixas) || !$caixa_drawer_id): ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Configurar Gavetas (Quantidade)</h6>
                            <div class="alert alert-warning mb-3">
                                Você precisa cadastrar a quantidade de caixas (gavetas) para liberar abertura e movimentações.
                            </div>
                            <form method="POST" action="">
                                <input type="hidden" name="operacao" value="configurar_caixas">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label">Quantidade de gavetas</label>
                                        <input type="number" name="quantidade_caixas" class="form-control" min="1" step="1" required>
                                    </div>
                                    <div class="col-md-8">
                                        <button type="submit" class="btn btn-primary w-100">Salvar e liberar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php elseif ($sessao_aberta): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Sangria</h6>
                            <form method="POST" action="">
                                <input type="hidden" name="operacao" value="sangria">
                                <input type="hidden" name="data_caixa" value="<?= htmlspecialchars($data_inicio) ?>">
                                <input type="hidden" name="caixa_drawer_id" value="<?= (int)$caixa_drawer_id ?>">
                                <label class="form-label">Valor (retira)</label>
                                <input type="number" step="0.01" name="valor" class="form-control" required>
                                <label class="form-label mt-2">Observação</label>
                                <input type="text" name="observacoes" class="form-control">
                                <button type="submit" class="btn btn-danger mt-3 w-100">Registrar Sangria</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Reforço</h6>
                            <form method="POST" action="">
                                <input type="hidden" name="operacao" value="reforco">
                                <input type="hidden" name="data_caixa" value="<?= htmlspecialchars($data_inicio) ?>">
                                <input type="hidden" name="caixa_drawer_id" value="<?= (int)$caixa_drawer_id ?>">
                                <label class="form-label">Valor (entra)</label>
                                <input type="number" step="0.01" name="valor" class="form-control" required>
                                <label class="form-label mt-2">Observação</label>
                                <input type="text" name="observacoes" class="form-control">
                                <button type="submit" class="btn btn-success mt-3 w-100">Registrar Reforço</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Fechamento</h6>
                            <form method="POST" action="">
                                <input type="hidden" name="operacao" value="fechar_sessao">
                                <input type="hidden" name="data_caixa" value="<?= htmlspecialchars($data_inicio) ?>">
                                <input type="hidden" name="caixa_drawer_id" value="<?= (int)$caixa_drawer_id ?>">
                                <label class="form-label">Saldo físico informado</label>
                                <input type="number" step="0.01" name="saldo_fisico_informado" class="form-control" required>
                                <label class="form-label mt-2">Observação</label>
                                <input type="text" name="observacoes" class="form-control">
                                <button type="submit" class="btn btn-primary mt-3 w-100">Fechar Caixa</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Abertura de Caixa</h6>
                            <form method="POST" action="">
                                <input type="hidden" name="operacao" value="abrir_sessao">
                                <input type="hidden" name="data_caixa" value="<?= htmlspecialchars($data_inicio) ?>">
                                <input type="hidden" name="caixa_drawer_id" value="<?= (int)$caixa_drawer_id ?>">
                                <label class="form-label">Troco de abertura (R$)</label>
                                <input type="number" step="0.01" name="troco_abertura" class="form-control" required value="<?= htmlspecialchars($config_troco['troco_abertura'] ?? 0) ?>">
                                <label class="form-label mt-2">Observação</label>
                                <input type="text" name="observacoes" class="form-control">
                                <button type="submit" class="btn btn-primary mt-3 w-100">Abrir Caixa</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
                            <th>Data/Hora</th>
                            <th>Tipo</th>
                            <th>Origem</th>
                            <th class="text-end">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimentacoes_caixa as $mov): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($mov['data_hora']))) ?></td>
                                <td><?= htmlspecialchars($mov['tipo'] ?? '') ?></td>
                                <td>
                                    <?php
                                        $origemTipo = $mov['origem_tipo'] ?? '';
                                        $origemId = $mov['origem_id'] ?? null;
                                        if ($origemTipo === 'Pedido') {
                                            echo 'Pedido #' . (int)$origemId;
                                        } elseif ($origemTipo === 'Conta') {
                                            echo 'Conta #' . (int)$origemId;
                                        } else {
                                            echo 'Manual';
                                        }
                                    ?>
                                </td>
                                <td class="text-end">
                                    <?php
                                        $valor = (float)($mov['valor'] ?? 0);
                                        $valorAbs = abs($valor);
                                        $sinal = $valor < 0 ? '-' : '';
                                    ?>
                                    <?= $sinal . 'R$ ' . number_format($valorAbs, 2, ',', '.') ?>
                                </td>
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
                            <h6 class="card-title">Resumo do Caixa</h6>
                            <?php
                                $troco_abertura = (float)($totais_gaveta['troco_abertura'] ?? 0);
                                $saldo_esperado = (float)($totais_gaveta['saldo_esperado'] ?? 0);
                                $saldo_fisico_informado = $totais_gaveta['saldo_fisico_informado'] ?? null;
                                $diferenca = $totais_gaveta['diferenca'] ?? null;
                                $status = $totais_gaveta['status'] ?? ($sessao_aberta ? 'Aberta' : 'Sem sessão');
                            ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <p><strong>Troco de Abertura:</strong><br>
                                    R$ <?= number_format($troco_abertura, 2, ',', '.') ?></p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Saldo Esperado:</strong><br>
                                    R$ <?= number_format($saldo_esperado, 2, ',', '.') ?></p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Status:</strong><br>
                                    <?= htmlspecialchars($status) ?></p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Diferença:</strong><br>
                                    <?php if ($diferenca === null) { echo '—'; } else { echo 'R$ ' . number_format((float)$diferenca, 2, ',', '.'); } ?></p>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <p><strong>Saldo físico informado:</strong><br>
                                    <?php if ($saldo_fisico_informado === null) { echo '—'; } else { echo 'R$ ' . number_format((float)$saldo_fisico_informado, 2, ',', '.'); } ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php if ($mostrar_modal_pedido_lancar && $pedido_lancar_info && !empty($sessoes_abertas_para_lancar)): ?>
<?php
$total_pedido_modal = (float)($pedido_lancar_info['valor_pago'] ?? $pedido_lancar_info['total'] ?? 0);
$caixas_abertos_numeros = array_map(function($s) { return 'Caixa #' . $s['numero']; }, (array)$sessoes_abertas_para_lancar);
?>
<!-- Modal: lançar pedido no caixa (troco quando dinheiro + escolher gaveta) -->
<div class="modal fade" id="modalPedidoLancar" tabindex="-1" aria-labelledby="modalPedidoLancarLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" id="formPedidoLancar">
                <input type="hidden" name="operacao" value="lancar_pedido">
                <input type="hidden" name="pedido_id" value="<?= (int)$pedido_lancar_id ?>">
                <input type="hidden" name="data_caixa" value="<?= htmlspecialchars($data_inicio) ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPedidoLancarLabel">Lançar pedido no caixa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">
                        Pedido <strong>#<?= (int)$pedido_lancar_id ?></strong> — Total: <strong>R$ <?= number_format($total_pedido_modal, 2, ',', '.') ?></strong>
                    </p>
                    <p class="text-muted small mb-3">
                        <strong>Caixa(s) aberto(s) na data do pedido:</strong> <?= implode(', ', $caixas_abertos_numeros) ?>
                    </p>
                    <?php if ($pedido_lancar_eh_dinheiro): ?>
                    <div class="alert alert-info py-2 mb-3">
                        <label for="valor_recebido_modal" class="form-label mb-1">Valor recebido do cliente (R$)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="valor_recebido_modal" name="valor_recebido" placeholder="0,00" value="<?= number_format($total_pedido_modal, 2, '.', '') ?>">
                        <div class="mt-2">
                            <strong>Troco a devolver:</strong> <span id="troco_devolver" class="fs-5">R$ 0,00</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="mb-0">
                        <label for="caixa_drawer_id_lancar" class="form-label">Lançar no caixa</label>
                        <select class="form-select" id="caixa_drawer_id_lancar" name="caixa_drawer_id" required>
                            <?php if (count((array)$sessoes_abertas_para_lancar) > 1): ?>
                                <option value="">Selecione o caixa...</option>
                            <?php endif; ?>
                            <?php foreach ((array)$sessoes_abertas_para_lancar as $s): ?>
                                <option value="<?= (int)$s['caixa_drawer_id'] ?>" <?= count((array)$sessoes_abertas_para_lancar) === 1 ? 'selected' : '' ?>>Caixa #<?= htmlspecialchars($s['numero']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Lançar no caixa</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('modalPedidoLancar');
    if (el && typeof bootstrap !== 'undefined') { new bootstrap.Modal(el).show(); }
    var totalPedido = <?= json_encode($total_pedido_modal) ?>;
    var inputValor = document.getElementById('valor_recebido_modal');
    var spanTroco = document.getElementById('troco_devolver');
    if (inputValor && spanTroco) {
        function atualizarTroco() {
            var recebido = parseFloat(String(inputValor.value).replace(',', '.')) || 0;
            var troco = Math.max(0, recebido - totalPedido);
            spanTroco.textContent = 'R$ ' + troco.toFixed(2).replace('.', ',');
        }
        inputValor.addEventListener('input', atualizarTroco);
        inputValor.addEventListener('change', atualizarTroco);
        atualizarTroco();
    }
});
</script>
<?php endif; ?>

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
            <h2>Movimento de Caixa - Loja <?= (int)$loja_id ?> / Gaveta #<?= (int)($caixa_drawer_id ?? 0) ?></h2>
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
