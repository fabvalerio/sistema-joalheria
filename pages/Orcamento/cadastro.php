<?php

use App\Models\Orcamento\Controller;

$controller = new Controller();
$clientes = $controller->listarClientes(); // Obter lista de clientes
$produtos = $controller->listarProdutos(); // Obter lista de produtos para o modal
$cartaos = $controller->listarCartoes(); // Obter lista de cartões

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'cliente_id' => $_POST['cliente_id'] ?? null,
        'data_pedido' => $_POST['data_pedido'] ?? null,
        'forma_pagamento' => $_POST['forma_pagamento'] ?? null,
        'acrescimo' => $_POST['acrescimo'] ?? 0,
        'desconto' => $_POST['desconto'] ?? 0,
        'observacoes' => $_POST['observacoes'] ?? null,
        'total' => $_POST['total'] ?? 0,
        'valor_pago' => $_POST['valor_pago'] ?? 0,
        'cod_vendedor' => $_POST['cod_vendedor'] ?? null,
        'status_pedido' => $_POST['status_pedido'] ?? 'Pendente',
        'data_entrega' => $_POST['data_entrega'] ?? null,
        // 'fabrica' => $_POST['fabrica'] ?? 0,
        'itens' => [] // Inicializa o array de itens do pedido
    ];

    // Capturar os produtos enviados via POST
    if (!empty($_POST['produtos'])) {
        foreach ($_POST['produtos'] as $produto) {
            if (!empty($produto['quantidade']) && !empty($produto['preco'])) {
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
                    'estoque_atualizado' => (int)$produto['estoque_atual'] - (int)$produto['quantidade'],
                    'fabrica' => (bool)($produto['fabrica'] ?? '0')
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
        echo notify('success', "Pedido cadastrado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao cadastrar o pedido.");
    }
}

?>
<link href="<?php echo $url?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro de Orçamento</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-12">
                    <hr>
                    <h4 class="card-title">Dados do Orçamento</h4>
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
                    <input type="date" class="form-control" id="data_pedido" name="data_pedido" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-lg-2">
                    <label for="data_entrega" class="form-label">Data de Entrega</label>
                    <input type="date" class="form-control" id="data_entrega" name="data_entrega">
                </div>

                <div class="col-lg-4" style="display: none;">
                    <label for="cod_vendedor" class="form-label">Código do Vendedor</label>
                    <input type="text" class="form-control" id="cod_vendedor" name="cod_vendedor" value="<?php echo $_COOKIE['id']; ?>">
                </div>
                <div class="col-lg-2">
                    <label for="status_pedido" class="form-label">Status do Pedido</label>
                    <select class="form-select" id="status_pedido" name="status_pedido" required>
                        <option value="Pendente">Pendente</option>
                        <option value="Pago">Pago</option>

                    </select>
                </div>

                <div class="col-12">
                    <hr>
                    <h4 class="card-title">Itens do Pedido</h4>
                </div>

                <!-- Seção de Produtos -->
                <div class="col-lg-12">
                    <div id="product-list">
                        <!-- Campo inicial para produtos -->
                        <div class="row g-3 align-items-end product-item mb-2">
                        <div class="col-lg-1">
                                <img name="produtos[0][capa]" src="<?= $url . '/assets/img_padrao.webp'; ?>" alt="Capa do Produto"
                                    width="100" style="height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 5px;" class="capa">
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">Item</label>
                                <input type="text" class="form-control product-input" name="produtos[0][descricao_produto]" placeholder="Clique para selecionar um produto" readonly data-index="0">
                                <input type="hidden" name="produtos[0][id]" class="product-id">
                                <input type="hidden" name="produtos[0][valor_unitario]" class="product-price">
                                <input type="hidden" name="produtos[0][estoque_atual]" class="estoque_atual">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label">Preço</label>
                                <input type="number" step="0.01" class="form-control product-price-display" name="produtos[0][preco]" placeholder="Preço" readonly>
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label">Quantidade</label>
                                <input type="number" class="form-control" name="produtos[0][quantidade]" placeholder="Quantidade" required>
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label">Desconto (%)</label>
                                <input type="number" step="0.01" class="form-control" name="produtos[0][desconto_percentual]" placeholder="Desconto (%)">
                            </div>
                            <div class="col-lg-1 text-start">
                                <label class="form-label">Produção</label>
                                <div style="padding: 8px 0px;">
                                <input type="checkbox" name="produtos[0][fabrica]" id="" value="1"> Sim
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <button type="button" class="btn btn-success btn-add">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
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
                <!-- <div class="col-lg-4">
                    <label>Enviar para Fábrica</label>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="fabrica" id="inlineRadio1" value="true">
                        <label class="form-check-label" for="inlineRadio1">Sim</label>
                    </div>
                </div> -->
                <div class="col-12">
                    <hr>
                    <h4 class="card-title">Pagamento</h4>
                </div>
                <div class="col-lg-4">
                    <label for="forma_pagamento" class="form-label">Forma de Pagamento</label>
                    <select class="form-select" id="forma_pagamento" name="forma_pagamento" required>
                        <option value="" selected>Selecione uma forma de pagamento</option>
                        <option value="Dinheiro">Dinheiro</option>
                        <option value="Cartão de Crédito">Cartão de Crédito</option>
                        <option value="Cartão de Débito">Cartão de Débito</option>
                        <option value="Pix">Pix</option>
                    </select>
                </div>

                <!-- Select de cartões (inicialmente oculto) -->
                <div class="col-lg-4" id="cartao_container" style="display: none;">
                    <label for="cartao_tipo" class="form-label">Selecione o Cartão</label>
                    <select class="form-select" id="cartao_tipo" name="cartao_tipo">
                        <option value="" disabled selected>Selecione um cartão</option>
                    </select>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const formaPagamento = document.getElementById('forma_pagamento');
                        const cartaoContainer = document.getElementById('cartao_container');
                        const cartaoTipo = document.getElementById('cartao_tipo');

                        formaPagamento.addEventListener('change', async () => {
                            const selectedValue = formaPagamento.value;

                            // Mostrar o select de cartões apenas para Crédito ou Débito
                            if (selectedValue === 'Cartão de Crédito' || selectedValue === 'Cartão de Débito') {
                                cartaoContainer.style.display = 'block';

                                // Fazer uma chamada AJAX para buscar os cartões
                                try {
                                    const response = await fetch(`<?php echo $url; ?>pages/Pedidos/listar_cartoes.php?tipo=${selectedValue === 'Cartão de Crédito' ? 'Crédito' : 'Débito'}`);
                                    const cartoes = await response.json();

                                    // Limpar as opções do select
                                    cartaoTipo.innerHTML = '<option value="" disabled selected>Selecione um cartão</option>';

                                    // Preencher o select com os cartões retornados
                                    cartoes.forEach(cartao => {
                                        const option = document.createElement('option');
                                        option.value = cartao.id; // Ajuste conforme o nome do campo ID na tabela
                                        option.textContent = cartao.bandeira; // Ajuste conforme o nome do campo nome na tabela
                                        cartaoTipo.appendChild(option);
                                    });
                                } catch (error) {
                                    console.error('Erro ao buscar cartões:', error);
                                }
                            } else {
                                cartaoContainer.style.display = 'none';
                                cartaoTipo.innerHTML = '<option value="" disabled selected>Selecione um cartão</option>'; // Resetar opções
                            }
                        });
                    });
                </script>
                <div class="col-lg-4" id="parcelas_container" style="display: none;">
                    <label for="numero_parcelas" class="form-label">Número de Parcelas</label>
                    <select class="form-select" id="numero_parcelas" name="numero_parcelas">
                        <option value="" disabled selected>Selecione o número de parcelas</option>
                    </select>
                </div>

                <div class="col-12">
                    <hr>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const formaPagamento = document.getElementById('forma_pagamento');
                        const cartaoContainer = document.getElementById('cartao_container');
                        const cartaoTipo = document.getElementById('cartao_tipo');
                        const parcelasContainer = document.getElementById('parcelas_container');
                        const numeroParcelas = document.getElementById('numero_parcelas');

                        // Evento para mostrar ou esconder os cartões
                        formaPagamento.addEventListener('change', async () => {
                            const selectedValue = formaPagamento.value;

                            // Mostrar o select de cartões apenas para Crédito ou Débito
                            if (selectedValue === 'Cartão de Crédito' || selectedValue === 'Cartão de Débito') {
                                cartaoContainer.style.display = 'block';

                                // Fazer uma chamada AJAX para buscar os cartões
                                try {
                                    const response = await fetch(`<?php echo $url; ?>pages/Pedidos/listar_cartoes.php?tipo=${selectedValue === 'Cartão de Crédito' ? 'Crédito' : 'Débito'}`);
                                    const cartoes = await response.json();

                                    // Limpar as opções do select
                                    cartaoTipo.innerHTML = '<option value="" disabled selected>Selecione um cartão</option>';

                                    // Preencher o select com os cartões retornados
                                    cartoes.forEach(cartao => {
                                        const option = document.createElement('option');
                                        option.value = cartao.id; // Ajuste conforme o nome do campo ID na tabela
                                        option.dataset.maxParcelas = cartao.max_parcelas; // Adiciona max_parcelas como atributo de dados
                                        option.textContent = cartao.bandeira; // Ajuste conforme o nome do campo nome na tabela
                                        cartaoTipo.appendChild(option);
                                        option.dataset.juros_parcela_1 = cartao.juros_parcela_1;
                                        option.dataset.juros_parcela_2 = cartao.juros_parcela_2;
                                        option.dataset.juros_parcela_3 = cartao.juros_parcela_3;
                                        option.dataset.juros_parcela_4 = cartao.juros_parcela_4;
                                        option.dataset.juros_parcela_5 = cartao.juros_parcela_5;
                                        option.dataset.juros_parcela_6 = cartao.juros_parcela_6;
                                        option.dataset.juros_parcela_7 = cartao.juros_parcela_7;
                                        option.dataset.juros_parcela_8 = cartao.juros_parcela_8;
                                        option.dataset.juros_parcela_9 = cartao.juros_parcela_9;
                                        option.dataset.juros_parcela_10 = cartao.juros_parcela_10;
                                        option.dataset.juros_parcela_11 = cartao.juros_parcela_11;
                                        option.dataset.juros_parcela_12 = cartao.juros_parcela_12;
                                    });
                                } catch (error) {
                                    console.error('Erro ao buscar cartões:', error);
                                }
                            } else {
                                cartaoContainer.style.display = 'none';
                                cartaoTipo.innerHTML = '<option value="" disabled selected>Selecione um cartão</option>'; // Resetar opções
                                parcelasContainer.style.display = 'none'; // Esconde o select de parcelas se o cartão for ocultado
                                numeroParcelas.innerHTML = '<option value="" disabled selected>Selecione o número de parcelas</option>';
                            }
                        });

                        // Evento para mostrar o número de parcelas ao selecionar um cartão
                        cartaoTipo.addEventListener('change', () => {
                            const selectedCardOption = cartaoTipo.options[cartaoTipo.selectedIndex];
                            const maxParcelas = selectedCardOption.dataset.maxParcelas;
                            const juros_parcela_1 = selectedCardOption.dataset.juros_parcela_1;
                            const juros_parcela_2 = selectedCardOption.dataset.juros_parcela_2;
                            const juros_parcela_3 = selectedCardOption.dataset.juros_parcela_3;
                            const juros_parcela_4 = selectedCardOption.dataset.juros_parcela_4;
                            const juros_parcela_5 = selectedCardOption.dataset.juros_parcela_5;
                            const juros_parcela_6 = selectedCardOption.dataset.juros_parcela_6;
                            const juros_parcela_7 = selectedCardOption.dataset.juros_parcela_7;
                            const juros_parcela_8 = selectedCardOption.dataset.juros_parcela_8;
                            const juros_parcela_9 = selectedCardOption.dataset.juros_parcela_9;
                            const juros_parcela_10 = selectedCardOption.dataset.juros_parcela_10;
                            const juros_parcela_11 = selectedCardOption.dataset.juros_parcela_11;
                            const juros_parcela_12 = selectedCardOption.dataset.juros_parcela_12;

                            if (maxParcelas) {
                                parcelasContainer.style.display = 'block';
                                numeroParcelas.innerHTML = '<option value="" disabled selected>Selecione o número de parcelas</option>';

                                // Preencher o select de parcelas
                                for (let i = 1; i <= maxParcelas; i++) {
                                    const option = document.createElement('option');
                                    option.value = i;
                                    option.textContent = i + (i > 1 ? ' Parcelas( Juros: ' + eval(`juros_parcela_${i}`) + '% )' : ' Parcela ( Juros: ' + eval(`juros_parcela_${i}`) + '% )');
                                    // criar data-juros no option
                                    option.dataset.juros_parcela_i = eval(`juros_parcela_${i}`);
                                    numeroParcelas.appendChild(option);
                                }
                            } else {
                                parcelasContainer.style.display = 'none';
                                numeroParcelas.innerHTML = '<option value="" disabled selected>Selecione o número de parcelas</option>';
                            }
                        });
                    });
                </script>

                <div class="col-lg-12">
                    <label for="total" class="form-label">Total do Pedido</label>
                    <input type="number" step="0.01" class="form-control text-white" id="total" name="total" style="background-color: #198754;" readonly>
                </div>
                <div class="col-lg-2">
                    <label for="valor_pago" class="form-label">Valor Pago</label>
                    <input type="number" step="0.01" class="form-control" id="valor_pago" name="valor_pago">
                </div>


                <!-- Modal para Seleção de Produtos -->
                <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="productModalLabel">Selecione um Produto</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                            <button type="button" class="btn btn-primary btn-select-product-branco"
                            data-id=""
                                                        data-name=""
                                                        data-price="0"
                                                        data-estoque="1000">
                                                        Campo em Branco
                                                    </button>
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Capa</th>
                                            <th>Nome</th>
                                            <th>Preço</th>
                                            <th>Estoque Atual</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalProductList">
                                        <?php foreach ($produtos as $produto): ?>
                                            <?php
                            //conta de valor dinamica com cotação
                            $produto['preco'] =  cotacao($produto['preco_ql'], $produto['peso_gr'], $produto['cotacao_valor'], $produto['margem']);
                            ?>
                                            <tr>
                                                <td><?php echo $produto['id']; ?></td>
                                                <td>
                                                    <img src="<?= isset($produto['capa']) && !empty($produto['capa']) ? htmlspecialchars($produto['capa']) : $url . '/assets/img_padrao.webp'; ?>" alt="Capa do Produto" width="65" style="height: 65px; object-fit: cover; border: 1px solid #ddd; border-radius: 5px;">
                                                </td>
                                                <td><?php echo $produto['nome_produto']; ?></td>
                                                <td>R$<?php echo $produto['preco']; ?></td>
                                                <td><?php echo $produto['estoque']; ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-select-product"
                                                        data-id="<?php echo $produto['id']; ?>"
                                                        data-name="<?php echo $produto['nome_produto']; ?>"
                                                        data-price="<?php echo $produto['preco']; ?>"
                                                        data-estoque="<?php echo $produto['estoque']; ?>"
                                                        data-capa="<?= isset($produto['capa']) && !empty($produto['capa']) ? htmlspecialchars($produto['capa']) : $url . '/assets/img_padrao.webp'; ?>">
                                                        Selecionar
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <!-- botao para adicionar um novo -->
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Botão de Cadastro -->
            <div class="col-lg-12 mt-4">
                <button type="submit" class="btn btn-primary float-end">Cadastrar Pedido</button>
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
                    if (typeof window.initializeBaseTotal === 'function') window.initializeBaseTotal();
                    if (typeof window.applyJuros === 'function') window.applyJuros();
                }

                // Abrir o modal ao clicar no input de produto
                document.addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('product-input')) {
                        activeIndex = e.target.dataset.index; // Armazena o índice do produto selecionado
                        modal.show(); // Abre o modal
                    }
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
                        }

                        modal.hide(); // Fecha o modal
                    }
                });
                // Selecionar produto no modal branco
                document.addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('btn-select-product-branco')) {
                        const productId = e.target.getAttribute('data-id');
                        const productName = e.target.getAttribute('data-name');
                        const productPrice = e.target.getAttribute('data-price');
                        const activeInput = document.querySelector(`.product-input[data-index="${activeIndex}"]`);
                        const estoque_atual = e.target.getAttribute('data-estoque');

                        if (activeInput) {
                            activeInput.value = productName;
                            const parentItem = activeInput.closest('.product-item');
                            parentItem.querySelector('.product-id').value = productId;
                            parentItem.querySelector('.product-price').value = productPrice;
                            parentItem.querySelector('.estoque_atual').value = estoque_atual;
                            parentItem.querySelector('.product-price-display').value = productPrice;
                            //tira o readonly do input .product-input[data-index="${activeIndex}"]
                            parentItem.querySelector('.product-input').removeAttribute('readonly');
                            //tira o read only do price
                            parentItem.querySelector('.product-price-display').removeAttribute('readonly');
                            //evita clique no .product-input[data-index="${activeIndex}"]
                            parentItem.querySelector('.product-input').setAttribute('onclick', 'event.stopPropagation();');
                        }

                        modal.hide(); // Fecha o modal
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
                                    width="100" style="height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                            <div class="col-lg-3">
                    <input type="text" class="form-control product-input" placeholder="Clique para selecionar um produto" name="produtos[${productIndex}][descricao_produto]" readonly data-index="${productIndex}">
                    <input type="hidden" name="produtos[${productIndex}][id]" class="product-id">
                    <input type="hidden" name="produtos[${productIndex}][valor_unitario]" class="product-price">
                    <input type="hidden" name="produtos[${productIndex}][estoque_atual]" class="estoque_atual">
                </div>
                <div class="col-lg-2">
                    <input type="number" step="0.01" class="form-control product-price-display" name="produtos[${productIndex}][preco]" placeholder="Preço" readonly>
                </div>
                <div class="col-lg-2">
                    <input type="number" class="form-control" name="produtos[${productIndex}][quantidade]" placeholder="Quantidade" required>
                </div>
                <div class="col-lg-2">
                    <input type="number" step="0.01" class="form-control" name="produtos[${productIndex}][desconto_percentual]" placeholder="Desconto (%)">
                </div>
                <div class="col-lg-1 text-start">
                    <label class="form-label">Produção</label>
                    <div style="padding: 8px 0px;">
                    <input type="checkbox" name="produtos[${productIndex}][fabrica]" id="" value="1"> Sim
                    </div>
                </div>
                <div class="col-lg-1">
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
                    if (typeof window.initializeBaseTotal === 'function') window.initializeBaseTotal();
                    if (typeof window.applyJuros === 'function') window.applyJuros();
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


            ///juros cartao

            document.addEventListener('DOMContentLoaded', () => {
                const numeroParcelas = document.getElementById('numero_parcelas'); // Select de parcelas
                const totalField = document.getElementById('total'); // Campo de total
                let timeout; // Variável para controlar o delay
                let previousTotalValue = totalField.value; // Armazena o valor anterior do total
                const juros_aplicado = document.getElementById('juros_aplicado'); // Campo hidden para armazenar o juros aplicado

                // Função para aplicar juros ao total
                function applyJuros() {
                    // Subtrai o valor do juros_aplicado do total
                    const totalSemJuros = parseFloat(totalField.value || 0) - parseFloat(juros_aplicado.value || 0);
                    const selectedOption = numeroParcelas.options[numeroParcelas.selectedIndex]; // Option selecionado
                    const juros = parseFloat(selectedOption?.dataset?.juros_parcela_i || 0); // Obtém o valor do data-juros_parcela_i

                    // Verifica se é possível aplicar o juros
                    if (totalSemJuros && !isNaN(juros)) {
                        const totalComJuros = totalSemJuros * (1 + juros / 100); // Aplica o juros
                        const jurosAplicado = totalComJuros - totalSemJuros; // Calcula o valor do juros aplicado

                        // Atualiza os campos
                        totalField.value = totalComJuros.toFixed(2); // Atualiza o campo de total
                        juros_aplicado.value = jurosAplicado.toFixed(2); // Atualiza o hidden com o novo valor de juros
                    }
                }

                // Função para inicializar o valor base total
                function initializeBaseTotal() {
                    const baseTotal = parseFloat(totalField.value || 0); // Obtém o valor atual do total ou usa 0
                    totalField.dataset.baseTotal = baseTotal; // Armazena o valor inicial do total como base
                    juros_aplicado.value = '0'; // Reseta o campo de juros
                }

                // Inicializa o valor base do total ao carregar a página
                initializeBaseTotal();

                // Evento para atualizar os juros ao mudar o número de parcelas
                numeroParcelas.addEventListener('change', () => {
                    // Aplica o delay antes de recalcular os juros
                    clearTimeout(timeout); // Cancela qualquer timeout anterior
                    timeout = setTimeout(() => {
                        applyJuros(); // Aplica os juros com base na parcela selecionada
                    }, 1000); // Delay de 1 segundo
                });

                // Observa mudanças no valor do campo de total
                const observer = new MutationObserver(() => {
                    const currentTotalValue = totalField.value;

                    // Verifica se o valor do total realmente mudou
                    if (currentTotalValue !== previousTotalValue) {
                        previousTotalValue = currentTotalValue; // Atualiza o valor anterior

                        // Aplica o delay antes de recalcular os juros
                        clearTimeout(timeout); // Cancela qualquer timeout anterior
                        timeout = setTimeout(() => {
                            applyJuros(); // Recalcula os juros com o novo total
                        }, 1000); // Delay de 1 segundo
                    }
                });

                // Configurar o observer para monitorar alterações no atributo 'value' do totalField
                observer.observe(totalField, {
                    attributes: true,
                    attributeFilter: ['value']
                });

                // Tornando as funções globais
                window.applyJuros = applyJuros; // Agora você pode chamar applyJuros() globalmente
                window.initializeBaseTotal = initializeBaseTotal; // Para garantir que o baseTotal seja atualizado
            });

            // Botão para alterar cartão
            document.addEventListener('DOMContentLoaded', () => {
                const alterarCartaoButton = document.getElementById('altera cartão'); // Botão para alterar o cartão
                const formaPagamentoSelect = document.getElementById('forma_pagamento'); // Select de forma de pagamento

                // Evento de clique no botão
                alterarCartaoButton.addEventListener('click', () => {
                    formaPagamentoSelect.selectedIndex = 0; // Define o select para a primeira opção
                });
            });

            document.addEventListener('DOMContentLoaded', () => {
                const cartaoTipo = document.getElementById('cartao_tipo'); // Select de cartão
                const formaPagamento = document.getElementById('forma_pagamento'); // Select de forma de pagamento
                const jurosAplicado = document.getElementById('juros_aplicado'); // Campo hidden de juros aplicado
                const totalField = document.getElementById('total'); // Campo de total

                // Função para subtrair o valor de juros do total
                function removeJurosFromTotal() {
                    const juros = parseFloat(jurosAplicado.value || 0); // Obtém o valor do juros aplicado
                    const total = parseFloat(totalField.value || 0); // Obtém o valor atual do total

                    if (!isNaN(juros) && !isNaN(total)) {
                        const newTotal = total - juros; // Subtrai o juros do total
                        totalField.value = newTotal.toFixed(2); // Atualiza o total com o novo valor
                        jurosAplicado.value = '0'; // Reseta o campo de juros aplicado
                    }
                }

                // Adiciona os eventos de mudança nos selects
                cartaoTipo.addEventListener('change', removeJurosFromTotal);
                formaPagamento.addEventListener('change', removeJurosFromTotal);
            });

            document.addEventListener('change', function(event) {
  // Verifica se o input que disparou o evento é de preço
  if (event.target.matches('input[name*="[preco]"]')) {
    const inputPreco = event.target;
    const nome = inputPreco.getAttribute('name');
    const regex = /produtos\[(\d+)\]\[preco\]/;
    const match = nome.match(regex);

    if(match) {
      const indice = match[1];
      // Seleciona o input oculto correspondente com o mesmo índice
      const inputValorUnitario = document.querySelector('input[name="produtos[' + indice + '][valor_unitario]"]');
      if(inputValorUnitario) {
        inputValorUnitario.value = inputPreco.value;
      }
    }
  }
});
        </script>