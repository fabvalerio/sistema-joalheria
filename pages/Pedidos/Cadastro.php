<?php

use App\Models\Pedidos\Controller;

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
        'itens' => [] // Inicializa o array de itens do pedido
    ];

    // Capturar os produtos enviados via POST
    if (!empty($_POST['produtos'])) {
        foreach ($_POST['produtos'] as $produto) {
            if (!empty($produto['id']) && !empty($produto['quantidade']) && !empty($produto['valor_unitario'])) {
                $dados['itens'][] = [
                    'produto_id' => (int)$produto['id'], // ID do produto
                    'quantidade' => (float)$produto['quantidade'], // Quantidade
                    'valor_unitario' => (float)$produto['valor_unitario'], // Valor unitário
                    'desconto_percentual' => (float)($produto['desconto_percentual'] ?? 0) // Desconto percentual
                ];
            }
        }
    }

    $return = $controller->cadastro($dados);

    if ($return) {
        echo notify('success', "Pedido cadastrado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao cadastrar o pedido.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro de Pedido</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
            <div class="col-12">
                    <hr>
                    <h4 class="card-title">Dados do Pedido</h4>
                </div>
                <!-- Dados principais -->
                <div class="col-lg-4">
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
                    <input type="date" class="form-control" id="data_pedido" name="data_pedido" required>
                </div>
                <div class="col-lg-2">
                    <label for="data_entrega" class="form-label">Data de Entrega</label>
                    <input type="date" class="form-control" id="data_entrega" name="data_entrega">
                </div>
                <div class="col-lg-2">
                    <label for="valor_pago" class="form-label">Valor Pago</label>
                    <input type="number" step="0.01" class="form-control" id="valor_pago" name="valor_pago">
                </div>
                <div class="col-lg-4" style="display: none;">
                    <label for="cod_vendedor" class="form-label">Código do Vendedor</label>
                    <input type="text" class="form-control" id="cod_vendedor" name="cod_vendedor" value="2">
                </div>
                <div class="col-lg-2">
                    <label for="status_pedido" class="form-label">Status do Pedido</label>
                    <select class="form-select" id="status_pedido" name="status_pedido" required>
                        <option value="Pendente">Pendente</option>
                        <option value="Pago">Pago</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-12">
                    <hr>
                    <h4 class="card-title">Pagamento</h4>
                </div>
                <div class="col-lg-4">
                    <label for="forma_pagamento" class="form-label">Forma de Pagamento</label>
                    <select class="form-select" id="forma_pagamento" name="forma_pagamento" required>
                        <option value="" disabled selected>Selecione uma forma de pagamento</option>
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
                                    option.textContent = i + (i > 1 ? ' parcelas' : ' parcela');
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
                <div class="col-12">
                    <hr>
                    <h4 class="card-title">Produtos do Pedido</h4>
                </div>

                <!-- Seção de Produtos -->
                <div class="col-lg-12">
                    <label for="produtos" class="form-label">Produtos</label>
                    <div id="product-list">
                        <!-- Campo inicial para produtos -->
                        <div class="row g-3 align-items-end product-item mb-2">
                            <div class="col-lg-4">
                                <input type="text" class="form-control product-input" placeholder="Clique para selecionar um produto" readonly data-index="0">
                                <input type="hidden" name="produtos[0][id]" class="product-id">
                                <input type="hidden" name="produtos[0][valor_unitario]" class="product-price">
                            </div>
                            <div class="col-lg-2">
                                <input type="number" step="0.01" class="form-control product-price-display" placeholder="Preço" readonly>
                            </div>
                            <div class="col-lg-2">
                                <input type="number" class="form-control" name="produtos[0][quantidade]" placeholder="Quantidade" required>
                            </div>
                            <div class="col-lg-2">
                                <input type="number" step="0.01" class="form-control" name="produtos[0][desconto_percentual]" placeholder="Desconto (%)">
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-success btn-add">+</button>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="col-12">
                    <hr>
                    <h4 class="card-title">Complementos</h4>
                </div>
                <div class="col-lg-4">
                    <label for="acrescimo" class="form-label">Acréscimo Geral</label>
                    <input type="number" step="0.01" class="form-control" id="acrescimo" name="acrescimo">
                </div>
                <div class="col-lg-4">
                    <label for="desconto" class="form-label">Desconto Geral</label>
                    <input type="number" step="0.01" class="form-control" id="desconto" name="desconto">
                </div>
                <div class="col-lg-4">
                    <label for="total" class="form-label">Total do Pedido</label>
                    <input type="number" step="0.01" class="form-control" id="total" name="total" readonly>
                </div>
                <div class="col-lg-12">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
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
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Preço</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalProductList">
                                        <?php foreach ($produtos as $produto): ?>
                                            <tr>
                                                <td><?php echo $produto['id']; ?></td>
                                                <td><?php echo $produto['nome_produto']; ?></td>
                                                <td><?php echo $produto['preco']; ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-select-product"
                                                        data-id="<?php echo $produto['id']; ?>"
                                                        data-name="<?php echo $produto['nome_produto']; ?>"
                                                        data-price="<?php echo $produto['preco']; ?>">
                                                        Selecionar
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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

                        if (activeInput) {
                            activeInput.value = productName;
                            const parentItem = activeInput.closest('.product-item');
                            parentItem.querySelector('.product-id').value = productId;
                            parentItem.querySelector('.product-price').value = productPrice;
                            parentItem.querySelector('.product-price-display').value = productPrice;
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
                <div class="col-lg-4">
                    <input type="text" class="form-control product-input" placeholder="Clique para selecionar um produto" readonly data-index="${productIndex}">
                    <input type="hidden" name="produtos[${productIndex}][id]" class="product-id">
                    <input type="hidden" name="produtos[${productIndex}][valor_unitario]" class="product-price">
                </div>
                <div class="col-lg-2">
                    <input type="number" step="0.01" class="form-control product-price-display" placeholder="Preço" readonly>
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
        </script>