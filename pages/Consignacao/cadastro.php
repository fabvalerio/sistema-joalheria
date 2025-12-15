<!-- Select2 (busca interna no select) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

<style>
.select2 {
  width: 100%!important;
}
.select2-container--default .select2-selection--single {
    height: 42px;
}
</style>

<?php

use App\Models\Consignacao\Controller;

$controller = new Controller();
$clientes = $controller->listarClientes(); // Obter lista de clientes
$produtos = $controller->listarProdutos(); // Obter lista de produtos para o modal

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'cliente_id' => $_POST['cliente_id'] ?? null,
        'data_consignacao' => $_POST['data_consignacao'] ?? null,
        'valor' => $_POST['valor'] ?? 0,
        'status' => $_POST['status'] ?? 'Aberta',
        'observacao' => $_POST['observacao'] ?? null,
        'desconto_percentual' => $_POST['desconto_percentual'] ?? 0,
        'itens' => [], // Inicializa o array de itens da consignação,
        'bonificacao' => floatval($_POST['bonificacao'] ?? 0)
    ];

    // Capturar os produtos enviados via POST
    if (!empty($_POST['produtos'])) {
        foreach ($_POST['produtos'] as $produto) {
            if (!empty($produto['id']) && !empty($produto['quantidade']) && !empty($produto['valor_unitario'])) {
                $dados['itens'][] = [
                    'produto_id' => (int)$produto['id'], // ID do produto
                    'quantidade' => (float)$produto['quantidade'], // Quantidade
                    'valor' => (float)$produto['valor_unitario'], // Valor unitário
                    'qtd_devolvido' => 0 // Inicialmente, nenhum item devolvido
                ];
            }
        }
    }

    $return = $controller->cadastro($dados);

    if ($return) {
        echo notify('success', "Consignação cadastrada com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao cadastrar a consignação.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro de Consignação</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-12">
                    <h4 class="card-title">Dados da Consignação</h4>
                </div>
                <!-- Dados principais -->
                <div class="col-lg-6">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select class="form-select" id="cliente_id" name="cliente_id" required>
                        <option value="" disabled selected>Selecione um cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <?php if (!empty($cliente['nome_pf'])) { ?>
                                <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nome_pf']; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nome_fantasia_pj']; ?></option>
                            <?php } ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 col-6">
                    <label for="data_consignacao" class="form-label">Data da Consignação</label>
                    <input type="date" class="form-control" id="data_consignacao" name="data_consignacao" required>
                </div>
                <div class="col-lg-3 col-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Aberta">Aberta</option>
                    </select>
                </div>

                <div class="col-12">
                    <h4 class="card-title">Produtos da Consignação</h4>
                </div>

                <!-- Seção de Produtos -->
                <div class="col-lg-12">
                    <div id="product-list">
                        <!-- Campo inicial para produtos -->
                        <div class="row g-3 align-items-end product-item mb-2">
                            <div class="col-lg-4">
                                <label class="form-label">Produto</label>
                                <input type="text" class="form-control product-input" name="produtos[0][descricao]" placeholder="Clique para selecionar um produto" readonly data-index="0">
                                <input type="hidden" name="produtos[0][id]" class="product-id">
                                <input type="hidden" name="produtos[0][valor_unitario]" class="product-price">
                            </div>
                                <input type="hidden" step="0.01" class="form-control product-price-display" name="produtos[0][valor_unitario]" readonly>
                            <div class="col-lg-2 col-6">
                                <label class="form-label">Preço R$</label>
                                <input type="number" step="0.01" class="form-control product-price-display" name="produtos[0][valor_unitario_desconto]" readonly>
                            </div>
                            <div class="col-lg-2 col-6">
                                <label class="form-label">Quantidade</label>
                                <input type="number" class="form-control" name="produtos[0][quantidade]" required>
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-success btn-add">Adicionar +</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label for="observacao" class="form-label">Observações</label>
                    <textarea class="form-control" id="observacao" name="observacao" rows="3"></textarea>
                </div>
                
                <!-- Campos de valores -->
                <div class="col">
                    <!-- <label for="subtotal" class="form-label">Subtotal</label> -->
                    <input type="hidden" step="0.01" class="form-control" id="subtotal" readonly>
                </div>
                <div class="col">
                    <!-- <label for="desconto_percentual" class="form-label">Desconto (%)</label> -->
                    <input type="hidden" step="0.01" class="form-control" id="desconto_percentual" name="desconto_percentual" readonly>
                </div>
                <div class="col-lg-3 col-6">
                    <label for="bonificacao" class="form-label">Bonificação (%) *Opcional</label> 
                    <input type="number" class="form-control" id="bonificacao" name="bonificacao">
                </div>
                <div class="col-lg-3 col-6">
                    <!-- <label for="desconto_valor" class="form-label">Valor do Desconto</label> -->
                    <input type="hidden" step="0.01" class="form-control" id="desconto_valor" readonly>
                    <label for="total_itens" class="form-label">Total de Itens</label> 
                    <input type="number" class="form-control" id="total_itens" readonly>
                </div>
                <div class="col-lg-3 col-6">
                    <label for="valor" class="form-label">Valor Total</label>
                    <input type="number" step="0.01" class="form-control text-white" id="valor" name="valor" style="background-color: #198754;" readonly>
                </div>

            </div>

            <!-- Botão de Cadastro -->
            <div class="col-lg-12 mt-4">
                <button type="submit" class="btn btn-primary float-end">Cadastrar Consignação</button>
            </div>
        </form>

        <!-- Modal para Seleção de Produtos -->
        <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel">Selecione um Produto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Campo de pesquisa -->
                        <div class="mb-3">
                            <label for="searchProduct" class="form-label">Pesquisar Produto</label>
                            <input type="text" class="form-control" id="searchProduct" placeholder="Digite para pesquisar...">
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="productTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Preço</th>
                                        <th>Estoque Atual</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="modalProductList">
                                    <?php foreach ($produtos as $produto): ?>
                                        <tr>
                                            <td><?php echo $produto['id']; ?></td>
                                            <td><?php echo $produto['nome_produto']; ?></td>
                                            <td><?php echo $produto['preco']; ?></td>
                                            <td><?php echo $produto['estoque']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm btn-select-product"
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
    </div>
</div>

<!-- Select2 Script - Carregado primeiro -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Variável global para o cálculo de totais
    window.descontoAtual = 0;
    
    // URL base do sistema
    window.urlBase = '<?php echo $url; ?>';

    document.addEventListener('DOMContentLoaded', () => {
        const productList = document.getElementById('product-list');
        const subtotalField = document.getElementById('subtotal'); // Campo de subtotal
        const descontoPercentualField = document.getElementById('desconto_percentual'); // Campo de desconto %
        const descontoValorField = document.getElementById('desconto_valor'); // Campo de valor do desconto
        const totalField = document.getElementById('valor'); // Campo de valor total
        const modalElement = document.getElementById('productModal');
        const modal = new bootstrap.Modal(modalElement); // Modal Bootstrap
        let activeIndex = null;

        // Função para calcular o valor total
        window.calculateTotal = function() {
            let subtotal = 0;
            let totalItens = 0;
            const productItems = document.querySelectorAll('.product-item');
            const totalItensField = document.getElementById('total_itens');
            
            productItems.forEach(item => {
                const price = parseFloat(item.querySelector('.product-price').value) || 0;
                const quantity = parseFloat(item.querySelector('input[name*="[quantidade]"]').value) || 0;
                subtotal += price * quantity;
                totalItens += quantity;
            });
            
            console.log('Calculando total - Subtotal:', subtotal, 'Desconto atual:', window.descontoAtual, 'Total de itens:', totalItens);
            
            // Atualizar subtotal
            subtotalField.value = subtotal.toFixed(2);
            
            // Atualizar total de itens
            if (totalItensField) {
                totalItensField.value = totalItens;
            }
            
            // Calcular desconto
            const valorDesconto = (subtotal * window.descontoAtual) / 100;
            descontoValorField.value = valorDesconto.toFixed(2);
            
            // Calcular total com desconto
            const total = subtotal - valorDesconto;
            totalField.value = total.toFixed(2);
            
            console.log('Valores calculados - Subtotal:', subtotal, 'Desconto valor:', valorDesconto, 'Total:', total, 'Total Itens:', totalItens);
        }
        
        // Disponibilizar campos globalmente para debug
        window.descontoPercentualField = descontoPercentualField;
        window.subtotalField = subtotalField;
        window.descontoValorField = descontoValorField;
        window.totalField = totalField;

        // Abrir o modal ao clicar no input de produto
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('product-input')) {
                activeIndex = e.target.dataset.index; // Índice do produto
                modal.show(); // Abre o modal
            }
        });

        // Função para calcular preço com desconto para cada produto
        window.calculateProductDiscount = function(productItem) {
            const priceField = productItem.querySelector('.product-price');
            const priceDiscountField = productItem.querySelector('input[name*="[valor_unitario_desconto]"]');
            
            if (priceField && priceDiscountField) {
                const price = parseFloat(priceField.value) || 0;
                const discount = window.descontoAtual || 0;
                const discountedPrice = price - (price * discount / 100);
                priceDiscountField.value = discountedPrice.toFixed(2);
            }
        }

        // Atualizar todos os preços com desconto
        window.updateAllProductDiscounts = function() {
            const productItems = document.querySelectorAll('.product-item');
            productItems.forEach(item => {
                window.calculateProductDiscount(item);
            });
        }

        // Selecionar produto no modal
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('btn-select-product')) {
                const productId = e.target.getAttribute('data-id');
                const productName = e.target.getAttribute('data-name');
                const productPrice = e.target.getAttribute('data-price');

                // Preencher os campos no formulário
                const activeInput = document.querySelector(`.product-input[data-index="${activeIndex}"]`);
                if (activeInput) {
                    const parentItem = activeInput.closest('.product-item');
                    activeInput.value = productName;
                    parentItem.querySelector('.product-id').value = productId;
                    parentItem.querySelector('.product-price').value = productPrice;
                    parentItem.querySelector('.product-price-display').value = productPrice;
                    
                    // Calcular preço com desconto
                    window.calculateProductDiscount(parentItem);
                }

                modal.hide(); // Fecha o modal
                window.calculateTotal(); // Atualiza o total
            }
        });

        // Adicionar novo produto
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('btn-add')) {
                e.preventDefault();
                const productIndex = productList.children.length;
                const newProductItem = `
                    <div class="row g-3 align-items-end product-item mb-2">
                        <div class="col-lg-4">
                            <input type="text" class="form-control product-input" placeholder="Clique para selecionar um produto" name="produtos[${productIndex}][descricao]" readonly data-index="${productIndex}">
                            <input type="hidden" name="produtos[${productIndex}][id]" class="product-id">
                            <input type="hidden" name="produtos[${productIndex}][valor_unitario]" class="product-price">
                        </div>
                            <input type="hidden" step="0.01" class="form-control product-price-display" name="produtos[${productIndex}][valor_unitario]" readonly>
                        <div class="col-lg-2 col-6">
                            <input type="number" step="0.01" class="form-control" name="produtos[${productIndex}][valor_unitario_desconto]" readonly>
                        </div>
                        <div class="col-lg-2 col-6">
                            <input type="number" class="form-control" name="produtos[${productIndex}][quantidade]" required>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-danger btn-remove">Remover</button>
                        </div>
                    </div>
                `;
                productList.insertAdjacentHTML('beforeend', newProductItem);
            }
        });

        // Recalcular total ao alterar quantidade
        document.addEventListener('input', function(e) {
            if (e.target && e.target.matches('input[name*="[quantidade]"]')) {
                window.calculateTotal();
            }
        });

        // Remover produto
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('btn-remove')) {
                e.preventDefault();
                e.target.closest('.product-item').remove();
                window.calculateTotal(); // Atualiza o total após remover
            }
        });
    });

    // Funcionalidade de pesquisa na tabela
    document.getElementById('searchProduct').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#modalProductList tr');
        
        tableRows.forEach(row => {
            const productName = row.cells[1].textContent.toLowerCase();
            const productId = row.cells[0].textContent.toLowerCase();
            
            if (productName.includes(searchValue) || productId.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Limpar pesquisa ao fechar o modal
    document.getElementById('productModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('searchProduct').value = '';
        const tableRows = document.querySelectorAll('#modalProductList tr');
        tableRows.forEach(row => {
            row.style.display = '';
        });
    });
    
    // Inicializar Select2 após DOM carregar
    $(document).ready(function() {
        console.log('Inicializando Select2...');
        
        $('#cliente_id').select2({
            width: '100%',
            placeholder: 'Selecione um cliente',
            allowClear: true
        });

        console.log('Select2 inicializado. Registrando evento change...');
        
        // Buscar desconto do cliente quando selecionado
        $('#cliente_id').on('change', function() {
            const clienteId = $(this).val();
            
            console.log('=== EVENTO CHANGE DISPARADO ===');
            console.log('Cliente selecionado ID:', clienteId);
            
            if (clienteId) {
                // Adicionar timestamp para evitar cache
                const timestamp = new Date().getTime();
                // Usar URL base do sistema + api
                const url = `${window.urlBase}api/buscar_desconto_cliente.php?cliente_id=${clienteId}&_=${timestamp}`;
                console.log('Fazendo requisição para:', url);
                console.log('URL base:', window.urlBase);
                
                fetch(url)
                    .then(response => {
                        console.log('Resposta recebida - Status:', response.status);
                        console.log('Content-Type:', response.headers.get('content-type'));
                        return response.text(); // Primeiro pegar como texto para ver o conteúdo
                    })
                    .then(text => {
                        console.log('Resposta raw (texto):', text);
                        try {
                            const data = JSON.parse(text);
                            return data;
                        } catch (e) {
                            console.error('Erro ao fazer parse do JSON:', e);
                            console.error('Conteúdo recebido:', text.substring(0, 500)); // Primeiros 500 caracteres
                            throw new Error('Resposta não é um JSON válido');
                        }
                    })
                    .then(data => {
                        console.log('Dados recebidos:', data);
                        
                        if (data.success) {
                            // Atualizar variável global
                            window.descontoAtual = parseFloat(data.desconto) || 0;
                            console.log('Desconto atual atualizado para:', window.descontoAtual);
                            
                            // Buscar campo diretamente pelo ID
                            const campoDesconto = document.getElementById('desconto_percentual');
                            console.log('Campo desconto encontrado:', campoDesconto);
                            console.log('Valor atual do campo:', campoDesconto ? campoDesconto.value : 'null');
                            
                            if (campoDesconto) {
                                const valorAnterior = campoDesconto.value;
                                campoDesconto.value = window.descontoAtual.toFixed(2);
                                console.log('✓ Campo desconto_percentual atualizado');
                                console.log('  - Valor anterior:', valorAnterior);
                                console.log('  - Valor novo:', campoDesconto.value);
                                console.log('  - Campo readonly:', campoDesconto.readOnly);
                                
                                // Forçar atualização visual
                                campoDesconto.setAttribute('value', window.descontoAtual.toFixed(2));
                            } else {
                                console.error('✗ Campo desconto_percentual não encontrado!');
                            }
                            
                            // Atualizar todos os preços com desconto dos produtos
                            console.log('Atualizando preços com desconto...');
                            if (typeof window.updateAllProductDiscounts === 'function') {
                                window.updateAllProductDiscounts();
                                console.log('✓ Preços com desconto atualizados');
                            }
                            
                            // Recalcular total
                            console.log('Chamando calculateTotal()...');
                            if (typeof window.calculateTotal === 'function') {
                                window.calculateTotal();
                                console.log('✓ calculateTotal() executado');
                            } else {
                                console.error('✗ Função calculateTotal não está disponível!');
                            }
                        } else {
                            console.error('Erro na resposta:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao buscar desconto:', error);
                        window.descontoAtual = 0;
                        const campoDesconto = document.getElementById('desconto_percentual');
                        if (campoDesconto) {
                            campoDesconto.value = '0.00';
                        }
                        // Atualizar preços com desconto
                        if (typeof window.updateAllProductDiscounts === 'function') {
                            window.updateAllProductDiscounts();
                        }
                        if (typeof window.calculateTotal === 'function') {
                            window.calculateTotal();
                        }
                    });
            } else {
                console.log('Nenhum cliente selecionado, zerando desconto');
                window.descontoAtual = 0;
                const campoDesconto = document.getElementById('desconto_percentual');
                if (campoDesconto) {
                    campoDesconto.value = '0.00';
                }
                // Atualizar preços com desconto
                if (typeof window.updateAllProductDiscounts === 'function') {
                    window.updateAllProductDiscounts();
                }
                if (typeof window.calculateTotal === 'function') {
                    window.calculateTotal();
                }
            }
        });
        
        console.log('Evento change registrado com sucesso!');
    });
</script>


