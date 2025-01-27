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
        'itens' => [] // Inicializa o array de itens da consignação
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
                <div class="col-lg-3">
                    <label for="data_consignacao" class="form-label">Data da Consignação</label>
                    <input type="date" class="form-control" id="data_consignacao" name="data_consignacao" required>
                </div>
                <div class="col-lg-3">
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
                            <div class="col-lg-2">
                                <label class="form-label">Preço Unitário</label>
                                <input type="number" step="0.01" class="form-control product-price-display" name="produtos[0][valor_unitario]" readonly>
                            </div>
                            <div class="col-lg-2">
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
                <div class="col-lg-3">
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
                        <table class="table table-bordered table-hover">
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
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const productList = document.getElementById('product-list');
        const totalField = document.getElementById('valor'); // Campo de valor total
        const modalElement = document.getElementById('productModal');
        const modal = new bootstrap.Modal(modalElement); // Modal Bootstrap
        let activeIndex = null;

        // Função para calcular o valor total
        function calculateTotal() {
            let total = 0;
            const productItems = document.querySelectorAll('.product-item');
            productItems.forEach(item => {
                const price = parseFloat(item.querySelector('.product-price').value) || 0;
                const quantity = parseFloat(item.querySelector('input[name*="[quantidade]"]').value) || 0;
                total += price * quantity;
            });
            totalField.value = total.toFixed(2);
        }

        // Abrir o modal ao clicar no input de produto
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('product-input')) {
                activeIndex = e.target.dataset.index; // Índice do produto
                modal.show(); // Abre o modal
            }
        });

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
                }

                modal.hide(); // Fecha o modal
                calculateTotal(); // Atualiza o total
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
                        <div class="col-lg-2">
                            <input type="number" step="0.01" class="form-control product-price-display" name="produtos[${productIndex}][valor_unitario]" readonly>
                        </div>
                        <div class="col-lg-2">
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
                calculateTotal();
            }
        });

        // Remover produto
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('btn-remove')) {
                e.preventDefault();
                e.target.closest('.product-item').remove();
                calculateTotal(); // Atualiza o total após remover
            }
        });
    });
</script>


