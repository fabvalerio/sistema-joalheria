<?php

use App\Models\Pedidos\Controller;
use App\Models\GrupoProdutos\Controller as GrupoController;
use App\Models\Caixa\Controller as CaixaController;

// Regra: só pode fazer venda se tiver caixa aberto do dia
$caixaController = new CaixaController();
$loja_id = $_COOKIE['loja_id'] ?? 1;
$data_hoje = date('Y-m-d');
$sessoes_abertas = $caixaController->listarSessoesAbertasPorLojaData($loja_id, $data_hoje);
if (empty($sessoes_abertas)) {
    echo notify('warning', 'É necessário ter um caixa aberto para o dia de hoje antes de realizar vendas. Redirecionando para o Caixa...');
    $urlCaixa = ($url ?? '') . '!/Caixa/lista/' . $data_hoje . '/' . $data_hoje;
    echo '<meta http-equiv="refresh" content="2; url=' . htmlspecialchars($urlCaixa) . '">';
    exit;
}

$controller = new Controller();
$clientes = $controller->listarClientes(); // Obter lista de clientes
$cartaos = $controller->listarCartoes(); // Obter lista de cartões

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pagamentosDecoded = json_decode((string)($_POST['pagamentos'] ?? ''), true);
    $dados = [
        'cliente_id' => $_POST['cliente_id'] ?? null,
        'data_pedido' => $_POST['data_pedido'] ?? null,
        'acrescimo' => $_POST['acrescimo'] ?? 0,
        'desconto' => $_POST['desconto'] ?? 0,
        'observacoes' => $_POST['observacoes'] ?? null,
        'total' => $_POST['total'] ?? 0,
        'valor_pago' => $_POST['valor_pago'] ?? 0,
        'cod_vendedor' => $_POST['cod_vendedor'] ?? null,
        'status_pedido' => $_POST['status_pedido'] ?? 'Pendente',
        'data_entrega' => $_POST['data_entrega'] ?? null,
        'fabrica' => $_POST['fabrica'] ?? false,
        'loja_id' => $_COOKIE['loja_id'] ?? null,
        'itens' => [],
        'pagamentos' => is_array($pagamentosDecoded) ? $pagamentosDecoded : [],
    ];

    // Dados de cheque (quando há linha de pagamento Cheque)
    $dados['cheque_config_id'] = !empty($_POST['cheque_config_id']) ? (int)$_POST['cheque_config_id'] : null;
    $dados['numero_parcelas'] = !empty($_POST['numero_parcelas']) ? (int)$_POST['numero_parcelas'] : null;
    $dados['numero_cheque'] = [];
    if (!empty($_POST['numero_cheque']) && is_array($_POST['numero_cheque'])) {
        foreach ($_POST['numero_cheque'] as $i => $nc) {
            if (!empty(trim((string)$nc))) {
                $dados['numero_cheque'][(int)$i + 1] = trim((string)$nc);
            }
        }
    }

    // Dados de material (linhas Material: campos hidden gerados em pedido-pagamento.js)
    $dados['materiais'] = [];
    if (!empty($_POST['materiais']) && is_array($_POST['materiais'])) {
        foreach ($_POST['materiais'] as $m) {
            if (!empty($m['material_id']) && isset($m['gramas']) && (float)($m['gramas'] ?? 0) > 0) {
                $dados['materiais'][] = [
                    'material_id' => (int)$m['material_id'],
                    'gramas' => (float)$m['gramas'],
                ];
            }
        }
    }

    // Capturar os produtos enviados via POST
    if (!empty($_POST['produtos'])) {
        foreach ($_POST['produtos'] as $produto) {
            if (!empty($produto['id']) && !empty($produto['quantidade']) && !empty($produto['preco'])) {
                $dados['itens'][] = [
                    'descricao_produto' => $produto['descricao_produto'],
                    'produto_id' => (int)$produto['id'], // ID do produto
                    'quantidade' => (float)$produto['quantidade'], // Quantidade
                    'valor_unitario' => (float)$produto['preco'], // Valor unitário
                    'desconto_percentual' => (float)($produto['desconto_percentual'] ?? 0), // Desconto percentual
                    'estoque_antes' => (int)$produto['estoque_atual'],
                    'tipo_movimentacao' => 'Pedido',
                    //data de hoje
                    'data_movimentacao' => date('Y-m-d'),
                    'motivo' => 'Pedido',
                    //estoque atualizado estoque_antes - quantidade
                    'estoque_atualizado' => (int)$produto['estoque_atual'] - (int)$produto['quantidade']
                ];
            }
        }
    }
    // Debug para verificar o conteúdo do array
    // echo '<pre>';
    // print_r($dados['itens']); // Acessa a chave correta
    // echo '</pre>';
    // exit;


    $return = $controller->cadastro($dados);

    if ($return) {
        $data_pedido = $dados['data_pedido'] ?? date('Y-m-d');
        $ehPagoComValor = ($dados['status_pedido'] ?? '') === 'Pago' && (float)($dados['valor_pago'] ?? 0) > 0;
        $temDinheiro = false;
        foreach ($dados['pagamentos'] as $pg) {
            if (($pg['forma'] ?? '') === 'Dinheiro' && (float)($pg['valor'] ?? 0) > 0) {
                $temDinheiro = true;
                break;
            }
        }
        $ehDinheiroPago = $ehPagoComValor && $temDinheiro;
        $emitirNota = !empty($_POST['emitir_nota_fiscal']);
        if ($ehDinheiroPago) {
            // Venda em dinheiro pago: ir para Caixa (modal) antes da impressão
            $caixaUrl = $url . '!/Caixa/lista/' . $data_pedido . '/' . $data_pedido
                . '&pedido_lancar=' . (int)$return
                . '&data_pedido=' . urlencode($data_pedido)
                . '&redirect_print=1'
                . '&redirect_emitir_nota=' . ($emitirNota ? '1' : '0');
            echo notify('success', "Pedido cadastrado com sucesso! Conclua o pagamento no caixa para imprimir.");
            echo '<meta http-equiv="refresh" content="0; url=' . htmlspecialchars($caixaUrl) . '">';
        } else {
            $printUrl = $emitirNota
                ? ($url . '!/Notas/emitir-nota/' . $return . '&vias=2')
                : ($url . 'pages/Pedidos/imprimir.php?id=' . $return);
            if ($ehPagoComValor) {
                $printUrl .= (strpos($printUrl, '?') !== false ? '&' : '&') . 'pedido_lancar=' . (int)$return . '&data_pedido=' . urlencode($data_pedido);
            }
            echo notify('success', "Pedido cadastrado com sucesso! Redirecionando para impressão...");
            echo '<meta http-equiv="refresh" content="0; url=' . htmlspecialchars($printUrl) . '">';
        }
    } else {
        $msgErro = $controller->cadastroErro ?: 'Erro ao cadastrar o pedido.';
        echo notify('danger', $msgErro);
    }
}

//lista de grupo de produtos
$grupoController = new GrupoController();
$grupos = $grupoController->listarSelectTempo();

?>

<div class="card">


    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro de Pedido</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate id="formPedido">
            <div class="row g-3">
                <div class="col-12">

                    <h6 class="card-title">Tipo de Grupo</h6>
                    <select class="form-select mb-3" id="grupo_produto_id" name="grupo_produto_id">
                        <option value="0" selected>Nenhum</option>
                        <?php foreach ($grupos as $grupo): ?>
                            <option value="<?php echo $grupo['tempo']; ?>"><?php echo htmlspecialchars($grupo['nome_grupo']) . ' - ' . htmlspecialchars($grupo['tempo'] ?? '0') . ' dias'; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const grupoSelect = document.getElementById('grupo_produto_id');
                            const dataPedidoInput = document.getElementById('data_pedido');
                            const dataEntregaInput = document.getElementById('data_entrega');

                            // Função para adicionar dias úteis
                            function adicionarDiasUteis(dataInicial, diasUteis) {
                                let data = new Date(dataInicial + 'T00:00:00');
                                let diasAdicionados = 0;

                                while (diasAdicionados < diasUteis) {
                                    data.setDate(data.getDate() + 1);
                                    const diaSemana = data.getDay();
                                    // Se não for sábado (6) ou domingo (0)
                                    if (diaSemana !== 0 && diaSemana !== 6) {
                                        diasAdicionados++;
                                    }
                                }

                                return data.toISOString().split('T')[0];
                            }

                            // Função para atualizar data de entrega
                            function atualizarDataEntrega() {
                                const diasUteis = parseInt(grupoSelect.value) || 0;
                                const dataPedido = dataPedidoInput.value;

                                if (dataPedido && diasUteis > 0) {
                                    const novaDataEntrega = adicionarDiasUteis(dataPedido, diasUteis);
                                    dataEntregaInput.value = novaDataEntrega;
                                } else if (dataPedido) {
                                    // Se não houver dias úteis, usar a data do pedido
                                    dataEntregaInput.value = dataPedido;
                                }
                            }

                            // Eventos
                            grupoSelect.addEventListener('change', atualizarDataEntrega);
                            dataPedidoInput.addEventListener('change', atualizarDataEntrega);

                            // Calcular ao carregar a página
                            atualizarDataEntrega();
                        });
                    </script>

                    <hr>
                    <h4 class="card-title">Dados do Pedido</h4>
                </div>
                <!-- Dados principais -->
                <div class="col-lg-6">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select class="form-select" id="cliente_id" name="cliente_id" required>
                        <option value="" disabled selected>Selecione um cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <?php if (isset($cliente['nome_pf']) && !empty($cliente['nome_pf'])) { ?>
                                <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nome_pf']; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nome_fantasia_pj']; ?></option>
                            <?php } ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label for="data_pedido" class="form-label">Data do Pedido</label>
                    <input type="date" class="form-control" id="data_pedido" name="data_pedido" value="<?php echo $data_atual ?>" required>
                </div>
                <div class="col-lg-2">
                    <label for="data_entrega" class="form-label">Data de Entrega</label>
                    <input type="date" class="form-control" id="data_entrega" name="data_entrega" value="<?php echo adicionarDiasUteis($data_atual, 0); ?>" required>
                </div>

                <div class="col-lg-6" style="display: none;">
                    <label for="cod_vendedor" class="form-label">Código do Vendedor</label>
                    <input type="text" class="form-control" id="cod_vendedor" name="cod_vendedor" value="<?php echo $_COOKIE['id']; ?>">
                </div>

                <div class="col-12">
                    <hr>
                    <h4 class="card-title">Produtos do Pedido</h4>
                </div>

                <!-- Seção de Produtos -->

                <div class="col-lg-12">
                    <div id="product-list">
                        <!-- Campo inicial para produtos -->
                        <div class="row g-3 align-items-end product-item mb-2">
                            <div class="col-lg-1">
                                <img name="produtos[0][capa]" src="<?= $url . '/assets/img_padrao.webp'; ?>" alt="Capa do Produto"
                                    style="height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 5px;"
                                    class="capa img-fluid img-capa-produto image-capa">
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">Produto</label>
                                <input type="text" class="form-control product-input" name="produtos[0][descricao_produto]" placeholder="Clique para selecionar um produto" readonly data-index="0">
                                <input type="hidden" name="produtos[0][id]" class="product-id">
                                <input type="hidden" name="produtos[0][valor_unitario]" class="product-price">
                                <input type="hidden" name="produtos[0][estoque_atual]" class="estoque_atual">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label">Preço</label>
                                <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">R$</span>
                                <input type="text" class="form-control product-price-display" name="produtos[0][preco]" placeholder="Preço" readonly oninput="let v = this.value.replace(/\D/g,''); if(v.length < 3) { v = v.padStart(3, '0'); } this.value = (parseInt(v,10)/100).toFixed(2).replace('.', ',');" inputmode="decimal">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label">Quantidade</label>
                                <input type="number" class="form-control" name="produtos[0][quantidade]" placeholder="Quantidade" required>
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label">Desconto (%)</label>
                                <input type="number" step="0.01" class="form-control" name="produtos[0][desconto_percentual]" placeholder="Desconto (%)">
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-success btn-add">Adicionar +</button>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="col-12">
                    <hr>
                    <h4 class="card-title">Complementos</h4>
                </div>
                <div class="col-lg-4">
                    <label for="acrescimo" class="form-label">Acréscimo Adicional</label>
                    <input type="number" step="0.01" class="form-control" id="acrescimo" name="acrescimo" placeholder="Acrescimo (%)">
                </div>
                <div class="col-lg-4">
                    <label for="desconto" class="form-label">Desconto Adicional</label>
                    <input type="number" step="0.01" class="form-control" id="desconto" name="desconto" placeholder="Desconto (%)">
                    <input type="hidden" name="juros_aplicado" id="juros_aplicado" value="0">
                </div>
                <div class="col-lg-4">
                    <label>Enviar para Fábrica</label>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="fabrica" id="inlineRadio1" value="true">
                        <label class="form-check-label" for="inlineRadio1">Sim</label>
                    </div>
                </div>
                <div class="col-12">
                    <hr>
                    <h4 class="card-title">Pagamento</h4>
                </div>

                <div class="col-12">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h6 class="mb-0 text-primary">Formas de pagamento</h6>
                            <button type="button" class="btn btn-success btn-sm" id="btnAdicionarPagamento">
                                <i class="fas fa-plus"></i> Adicionar forma
                            </button>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-2">A soma dos valores deve ser igual ao total do pedido.</p>
                            <div id="listaPagamentos"></div>
                            <div class="row mt-3">
                                <div class="col-md-6 ms-md-auto">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td class="text-end fw-bold">Total do pedido:</td>
                                            <td class="text-end" id="exibeTotalPedido">R$ 0,00</td>
                                        </tr>
                                        <tr>
                                            <td class="text-end fw-bold">Total pago:</td>
                                            <td class="text-end" id="exibeTotalPago">R$ 0,00</td>
                                        </tr>
                                        <tr id="trDiferencaPagamento">
                                            <td class="text-end fw-bold text-danger">Diferença:</td>
                                            <td class="text-end fw-bold text-danger" id="exibeDiferencaPagamento">R$ 0,00</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div id="alertaPagamento" class="alert alert-danger mt-2 d-none" role="alert">
                                <span id="msgAlertaPagamento"></span>
                            </div>
                            <input type="hidden" name="pagamentos" id="inputPagamentos" value="[]">
                        </div>
                    </div>
                </div>

                <template id="templateLinhaPagamento">
                    <div class="linha-pagamento row g-2 align-items-end mb-2 border rounded p-2 bg-light">
                        <div class="col-md-2">
                            <label class="form-label small mb-0">Forma <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm pp-forma" required>
                                <option value="">Selecione...</option>
                                <option value="Dinheiro">Dinheiro</option>
                                <option value="Pix">Pix</option>
                                <option value="Cartão de Crédito">Cartão de Crédito</option>
                                <option value="Cartão de Débito">Cartão de Débito</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Material">Material</option>
                            </select>
                        </div>
                        <div class="col-md-2 pp-material-select-wrap" style="display:none">
                            <label class="form-label small mb-0">Material</label>
                            <select class="form-select form-select-sm pp-material">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-md-2 pp-gramas-wrap" style="display:none">
                            <label class="form-label small mb-0">Gramas (g)</label>
                            <input type="number" step="0.001" min="0" class="form-control form-control-sm pp-gramas" placeholder="0">
                        </div>
                        <div class="col-md-2 pp-cartao-wrap" style="display:none">
                            <label class="form-label small mb-0">Cartão</label>
                            <select class="form-select form-select-sm pp-cartao">
                                <option value="">...</option>
                            </select>
                        </div>
                        <div class="col-md-2 pp-parcelas-wrap" style="display:none">
                            <label class="form-label small mb-0">Parcelas</label>
                            <select class="form-select form-select-sm pp-parcelas"></select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-0">Valor (R$) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm pp-valor" required placeholder="0,00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-0">Observação</label>
                            <input type="text" class="form-control form-control-sm pp-obs" maxlength="255" placeholder="Opcional">
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm pp-remove" title="Remover"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </template>

                <!-- Container Cheque -->
                <div class="col-12" id="cheque_container" style="display: none;">
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <label for="cheque_config_id" class="form-label">Configuração do Cheque</label>
                            <select class="form-select" id="cheque_config_id" name="cheque_config_id">
                                <option value="" disabled selected>Selecione uma configuração</option>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label for="numero_parcelas_cheque" class="form-label">Número de Parcelas</label>
                            <select class="form-select" id="numero_parcelas_cheque" name="numero_parcelas">
                                <option value="" disabled selected>Selecione as parcelas</option>
                            </select>
                        </div>
                    </div>
                    <div id="cheque_numero_container" class="row g-3 mt-2"></div>
                </div>

                <div class="col-12">
                    <hr>
                </div>

                <div class="col-lg-12">
                    <label for="total" class="form-label">Total do Pedido</label>
                    
                    <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">R$</span>
                    <input type="number" step="0.01" class="form-control text-white" id="total" name="total" style="background-color: #198754;" readonly>
                    </div>
                </div>

                
                <div class="col-lg-3">
                    <label for="status_pedido" class="form-label">Status do Pedido</label>
                    <select class="form-select" id="status_pedido" name="status_pedido" required>
                        <option value="Pendente">Pendente</option>
                        <option value="Pago">Pago</option>

                    </select>
                </div>
                <div class="col-lg-8">
                    <label for="valor_pago" class="form-label">Valor Pago</label>
                    <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">R$</span>
                    <input type="number" step="0.01" class="form-control" id="valor_pago" name="valor_pago">
                    </div>
                </div>
                <div class="col-lg-1 d-flex align-items-bottom justify-content-bottom">
                    <button class="btn btn-primary w-100" id="totalCopy"><i class="fa fa-calculator"></i></button>
                </div>
                <div class="col-lg-12">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                </div>


                <?php include dirname(__DIR__, 2) . '/assets/components/lightbox_capa.php'; ?>

                <!-- Modal para Seleção de Produtos -->
                <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="productModalLabel">Selecione um Produto</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Pesquisar Produto..." id="productSearch" autocomplete="off">
                                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                                </div>
                                <div id="productSearchStatus" class="text-muted small mb-2" style="display:none;"></div>
                                <table id="produtoTable" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Capa</th>
                                            <th>Nome</th>
                                            <th>Preço</th>
                                            <th>Estoque</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalProductList">
                                        <tr><td colspan="6" class="text-center text-muted">Digite para pesquisar ou aguarde o carregamento...</td></tr>
                                    </tbody>
                                </table>
                                <script>
                                (function () {
                                    const imgPadrao = '<?= $url . '/assets/img_padrao.webp'; ?>';
                                    const endpointUrl = '<?= $url; ?>pages/Pedidos/listar_produtos.php';
                                    const lojaId = '<?= addslashes($_COOKIE['loja_id'] ?? '') ?>';
                                    let searchTimeout = null;

                                    function renderProdutos(produtos) {
                                        const tbody = document.getElementById('modalProductList');
                                        const status = document.getElementById('productSearchStatus');

                                        if (!produtos || produtos.length === 0) {
                                            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nenhum produto encontrado.</td></tr>';
                                            status.style.display = 'none';
                                            return;
                                        }

                                        status.textContent = produtos.length >= 50 ? 'Exibindo os 50 primeiros resultados. Refine a busca para encontrar mais.' : `${produtos.length} produto(s) encontrado(s).`;
                                        status.style.display = 'block';

                                        tbody.innerHTML = produtos.map(p => {
                                            const capa = p.capa ? p.capa : imgPadrao;
                                            const preco = parseFloat(p.preco || 0).toFixed(2);
                                            return `<tr>
                                                <td>${p.id}</td>
                                                <td><img src="${capa}" alt="Capa" class="image-capa" width="65" style="height:65px;object-fit:cover;border:1px solid #ddd;border-radius:5px;cursor:pointer;"></td>
                                                <td>${p.nome_produto}</td>
                                                <td>R$ ${preco}</td>
                                                <td>${p.estoque ?? 0}</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-select-product"
                                                        data-id="${p.id}"
                                                        data-name="${p.nome_produto}"
                                                        data-price="${preco}"
                                                        data-estoque="${p.estoque ?? 0}"
                                                        data-capa="${capa}"
                                                        data-bs-dismiss="modal">
                                                        Selecionar
                                                    </button>
                                                </td>
                                            </tr>`;
                                        }).join('');
                                    }

                                    async function buscarProdutos(termo) {
                                        const tbody = document.getElementById('modalProductList');
                                        tbody.innerHTML = '<tr><td colspan="6" class="text-center"><span class="spinner-border spinner-border-sm me-2"></span>Carregando...</td></tr>';
                                        try {
                                            let url = `${endpointUrl}?busca=${encodeURIComponent(termo)}`;
                                            if (lojaId) url += `&loja_id=${encodeURIComponent(lojaId)}`;
                                            const resp = await fetch(url);
                                            const dados = await resp.json();
                                            renderProdutos(dados);
                                        } catch (err) {
                                            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erro ao carregar produtos.</td></tr>';
                                        }
                                    }

                                    document.addEventListener('DOMContentLoaded', function () {
                                        const productSearch = document.getElementById('productSearch');
                                        const modalEl = document.getElementById('productModal');

                                        // Carrega ao abrir o modal (se lista vazia)
                                        modalEl.addEventListener('show.bs.modal', function () {
                                            const tbody = document.getElementById('modalProductList');
                                            if (tbody.querySelectorAll('tr[data-loaded]').length === 0) {
                                                buscarProdutos('');
                                            }
                                        });

                                        // Busca com debounce ao digitar
                                        productSearch.addEventListener('input', function () {
                                            clearTimeout(searchTimeout);
                                            searchTimeout = setTimeout(() => {
                                                buscarProdutos(this.value.trim());
                                            }, 400);
                                        });
                                    });
                                })();
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="col-12 mt-4 mb-2">
                    <hr>
                    <div class="form-check form-check-lg border rounded p-3 bg-light">
                        <input class="form-check-input" type="checkbox" name="emitir_nota_fiscal" id="emitir_nota_fiscal" value="1">
                        <label class="form-check-label fw-bold" for="emitir_nota_fiscal">Emitir nota fiscal (NFC-e)?</label>
                        <div class="small text-muted mt-1">Se marcado: emite NFC-e e imprime 2 vias da nota. Se não marcado: imprime apenas o pedido de venda.</div>
                    </div>
                </div>

            <!-- Botão de Cadastro -->
            <div class="col-lg-12 mt-3">
                <button type="submit" class="btn btn-primary float-end">Cadastrar Pedido</button>
                <!-- <button type="button" class="btn btn-secondary" id="altera_cartao">Alterar Cartão</button> -->
            </div>
        </form>


        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const productList = document.getElementById('product-list');
                const modalElement = document.getElementById('productModal');
                const modal = new bootstrap.Modal(modalElement); // Certifique-se de que o Bootstrap está carregado
                const totalField = document.getElementById('total');
                let activeIndex = null; // Índice do produto sendo editado no momento
                let productIndex = 0;

                // Função para calcular o total
                function calculateTotal() {
                    let total = 0;
                    const productItems = document.querySelectorAll('.product-item');
                    productItems.forEach(item => {
                        const price = parseFloat(item.querySelector('.product-price').value) || 0;
                        const quantity = parseFloat(item.querySelector('input[name*="[quantidade]"]').value) || 0;
                        const discount = parseFloat(item.querySelector('input[name*="[desconto_percentual]"]').value) || 0;

                        const subtotal = price * quantity * (1 - discount / 100);
                        total += subtotal;
                    });
                    totalField.value = total.toFixed(2);

                    // Atualiza o baseTotal e aplica juros
                    if (typeof window.pedidoPagamentoRecalc === 'function') {
                        window.pedidoPagamentoRecalc();
                    }
                }

                // Abrir o modal ao clicar no input de produto
                document.addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('product-input')) {
                        activeIndex = e.target.dataset.index; // Armazena o índice do produto selecionado
                        modal.show(); // Abre o modal
                    }
                });

                // Evento para quando o modal é fechado (por qualquer meio)
                modalElement.addEventListener('hidden.bs.modal', function () {
                    const productSearch = document.getElementById('productSearch');
                    if (productSearch) {
                        productSearch.value = '';
                    }
                    activeIndex = null;
                });

                // Selecionar produto no modal
                document.addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('btn-select-product')) {
                        const productId = e.target.getAttribute('data-id');
                        const productName = e.target.getAttribute('data-name');
                        const productPrice = e.target.getAttribute('data-price');
                        const activeInput = document.querySelector(`.product-input[data-index="${activeIndex}"]`);
                        const estoque_atual = e.target.getAttribute('data-estoque');
                        const capa = e.target.getAttribute('data-capa');

                        if (activeInput) {
                            activeInput.value = productName;
                            const parentItem = activeInput.closest('.product-item');
                            parentItem.querySelector('.product-id').value = productId;
                            parentItem.querySelector('.product-price').value = productPrice;
                            parentItem.querySelector('.estoque_atual').value = estoque_atual;
                            parentItem.querySelector('.product-price-display').value = productPrice;
                            //troca o src da capa
                            parentItem.querySelector('.capa').src = capa;
                            
                            // Recalcula o total após selecionar o produto
                            calculateTotal();
                        }

                        // Fecha o modal
                        modal.hide();
                        
                        const productSearch = document.getElementById('productSearch');
                        if (productSearch) {
                            productSearch.value = '';
                        }
                        
                        activeIndex = null;
                    }
                });

                // Adicionar novo campo de produto
                document.addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('btn-add')) {
                        e.preventDefault();
                        productIndex++;
                        const productItem = document.createElement('div');
                        productItem.classList.add('row', 'g-3', 'align-items-end', 'product-item', 'mb-2');
                        productItem.innerHTML = `
                        <div class="col-lg-1">
                                <img name="produtos[${productIndex}][capa]" src="<?= $url . '/assets/img_padrao.webp'; ?>" alt="Capa do Produto"
                                    style="height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 5px;"
                                    class="capa img-fluid img-capa-produto image-capa">
                            </div>
                            <div class="col-lg-3">
                    <input type="text" class="form-control product-input" placeholder="Clique para selecionar um produto" name="produtos[${productIndex}][descricao_produto]" readonly data-index="${productIndex}">
                    <input type="hidden" name="produtos[${productIndex}][id]" class="product-id">
                    <input type="hidden" name="produtos[${productIndex}][valor_unitario]" class="product-price">
                    <input type="hidden" name="produtos[${productIndex}][estoque_atual]" class="estoque_atual">
                </div>
                <div class="col-lg-2">
                                <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">R$</span>
                    <input type="number" step="0.01" class="form-control product-price-display" name="produtos[${productIndex}][preco]" placeholder="Preço" readonly>
                                </div>
                </div>
                <div class="col-lg-2">
                    <input type="number" class="form-control" name="produtos[${productIndex}][quantidade]" placeholder="Quantidade" required>
                </div>
                <div class="col-lg-2">
                    <input type="number" step="0.01" class="form-control" name="produtos[${productIndex}][desconto_percentual]" placeholder="Desconto (%)">
                </div>
                <div class="col-lg-2">
                    <button type="button" class="btn btn-danger btn-remove">-</button>
                </div>`;
                        productList.appendChild(productItem);
                    }
                });

                // Recalcular total ao alterar quantidade ou desconto
                document.addEventListener('input', function(e) {
                    if (e.target && (e.target.matches('input[name*="[quantidade]"]') || e.target.matches('input[name*="[desconto_percentual]"]'))) {
                        calculateTotal();
                    }
                });

                // Remover um campo de produto
                document.addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('btn-remove')) {
                        e.preventDefault();
                        e.target.closest('.product-item').remove();
                        calculateTotal();
                    }
                });
            });

            document.addEventListener('DOMContentLoaded', () => {
                const totalField = document.getElementById('total'); // Campo de total
                const acrescimoField = document.getElementById('acrescimo'); // Campo de acréscimo geral
                const descontoField = document.getElementById('desconto'); // Campo de desconto geral
                const productList = document.getElementById('product-list'); // Lista de produtos

                // Função para calcular o total base dos produtos
                function calculateBaseTotal() {
                    let baseTotal = 0;
                    const productItems = document.querySelectorAll('.product-item');
                    productItems.forEach(item => {
                        const price = parseFloat(item.querySelector('.product-price').value) || 0;
                        const quantity = parseFloat(item.querySelector('input[name*="[quantidade]"]').value) || 0;
                        const discount = parseFloat(item.querySelector('input[name*="[desconto_percentual]"]').value) || 0;

                        const subtotal = price * quantity * (1 - discount / 100);
                        baseTotal += subtotal;
                    });
                    return baseTotal;
                }

                // Função para aplicar acréscimo e desconto ao total
                function applyAcrescimoDesconto() {
                    let baseTotal = calculateBaseTotal();

                    const acrescimo = parseFloat(acrescimoField.value) || 0;
                    const desconto = parseFloat(descontoField.value) || 0;

                    // Calcula o total com acréscimo e desconto
                    const total = baseTotal * (1 + acrescimo / 100) * (1 - desconto / 100);
                    totalField.value = total.toFixed(2);

                    // Atualiza o baseTotal e aplica juros
                    if (typeof window.pedidoPagamentoRecalc === 'function') {
                        window.pedidoPagamentoRecalc();
                    }
                }

                // Eventos para recalcular o total ao alterar produtos, acréscimo ou desconto
                document.addEventListener('input', function(e) {
                    if (
                        e.target.matches('input[name*="[quantidade]"]') ||
                        e.target.matches('input[name*="[desconto_percentual]"]') ||
                        e.target.id === 'acrescimo' ||
                        e.target.id === 'desconto'
                    ) {
                        applyAcrescimoDesconto();
                    }
                });

                // Recalcula o total inicial ao carregar a página
                applyAcrescimoDesconto();
            });


            // Botão para copiar o total para o valor pago
            document.addEventListener('DOMContentLoaded', () => {
                const totalCopyButton = document.getElementById('totalCopy');
                const totalField = document.getElementById('total');
                const valorPagoField = document.getElementById('valor_pago');
                
                totalCopyButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    const totalValue = totalField.value;
                    if (totalValue && totalValue !== '0.00') {
                        valorPagoField.value = totalValue;
                    } else {
                        alert('O total do pedido deve ser calculado primeiro.');
                    }
                });
            });

        </script>
        <script>
            window.__pedidoPagamentoConfig = { baseUrl: <?= json_encode($url ?? '') ?> };
        </script>
        <script src="<?= htmlspecialchars($url ?? '') ?>assets/js/pedido-pagamento.js"></script>