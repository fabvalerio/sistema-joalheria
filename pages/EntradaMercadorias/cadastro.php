<?php

use App\Models\EntradaMercadorias\Controller;

$controller = new Controller();
$fornecedores = $controller->listarFornecedores();
$produtos = $controller->listarProdutos(); // Obtemos os produtos para o modal

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $dados = [
    'nf_fiscal' => $_POST['nf_fiscal'] ?? null,
    'data_pedido' => $_POST['data_pedido'] ?? null,
    'fornecedor_id' => $_POST['fornecedor_id'] ?? null,
    'data_prevista_entrega' => $_POST['data_prevista_entrega'] ?? null,
    'data_entregue' => !empty($_POST['data_entregue']) ? $_POST['data_entregue'] : null,
    'transportadora' => !empty($_POST['transportadora']) ? $_POST['transportadora'] : null,
    'valor' => !empty($_POST['valor']) ? $_POST['valor'] : null,
    'observacoes' => !empty($_POST['observacoes']) ? $_POST['observacoes'] : null,
    'produtos' => [] // Inicializa o array de produtos
  ];

  // Capturar os produtos enviados via POST
  if (!empty($_POST['produtos'])) {
    foreach ($_POST['produtos'] as $produto) {
      // Certifique-se de que cada campo essencial esteja presente
      if (!empty($produto['id']) && !empty($produto['nome']) && !empty($produto['quantidade'])) {
        $dados['produtos'][] = [
          'id' => (int) $produto['id'], // Garante que o ID seja um número inteiro
          'nome_produto' => $produto['nome'], // Nome do produto
          'quantidade' => (int) $produto['quantidade'], // Quantidade como inteiro
          'estoque' => (int) $produto['estoque']
        ];
      }
    }
  }

  // Debug para verificar o conteúdo do array
  // echo '<pre>';
  // print_r($dados['produtos']);
  // echo '</pre>';
  // exit;

  $return = $controller->cadastro($dados);

  if ($return) {
    echo notify('success', "Entrada de mercadoria cadastrada com sucesso!");
    echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
  } else {
    echo notify('danger', "Erro ao cadastrar a entrada de mercadoria.");
  }
}
?>



<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Cadastro de Entrada de Mercadoria</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
  </div>

  <div class="card-body">
    <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>
      <div class="row g-3">
        <!-- Dados principais -->
        <div class="col-lg-4">
          <label for="nf_fiscal" class="form-label">Nota Fiscal</label>
          <input type="text" class="form-control" id="nf_fiscal" name="nf_fiscal" required>
        </div>
        <div class="col-lg-4">
          <label for="data_pedido" class="form-label">Data do Pedido</label>
          <input type="date" class="form-control" id="data_pedido" name="data_pedido" required>
        </div>
        <div class="col-lg-4">
          <label for="fornecedor_id" class="form-label">Fornecedor</label>
          <select class="form-select" id="fornecedor_id" name="fornecedor_id" required>
            <option value="" disabled selected>Selecione um fornecedor</option>
            <?php foreach ($fornecedores as $fornecedor): ?>
              <option value="<?php echo $fornecedor['id']; ?>"><?php echo $fornecedor['nome_fantasia']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-lg-4">
          <label for="data_prevista_entrega" class="form-label">Data da Entrega</label>
          <input type="date" class="form-control" id="data_prevista_entrega" name="data_prevista_entrega" required>
        </div>
        <!-- valor -->
        <div class="col-lg-4">
          <label for="valor" class="form-label">Valor</label>
          <input type="number" step="0.01" class="form-control" id="valor" name="valor" required>
        </div>
        <!-- trsnaportadora -->
        <div class="col-lg-4">
          <label for="transportadora" class="form-label">Transportadora</label>
          <input type="text" class="form-control" id="transportadora" name="transportadora" required>
        </div>
        <div class="col-lg-12">
          <label for="observacoes" class="form-label">Observações</label>
          <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
        </div>

        <!-- Seção de Produtos -->
        <div class="col-lg-12">
          <label for="produtos" class="form-label">Produtos</label>
          <div id="product-list">
            <!-- Primeiro campo de produto -->
            <div class="row g-3 align-items-end product-item mb-2">
              <div class="col-lg-6">
                <input
                  type="text"
                  class="form-control product-input"
                  placeholder="Clique para selecionar um produto"
                  readonly
                  data-index="0">
                <input type="hidden" name="produtos[0][id]" class="product-id">
                <input type="hidden" name="produtos[0][estoque]" class="product-stock">
              </div>
              <div class="col-lg-4">
                <input type="number" class="form-control" name="produtos[0][quantidade]" placeholder="Quantidade" required>
              </div>
              <div class="col-lg-2">
                <button type="button" class="btn btn-success btn-add">+</button>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Botão de submissão -->
      <div class="col-lg-12 mt-4">
        <button type="submit" class="btn btn-primary float-end">Salvar</button>
      </div>
    </form>
  </div>
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
              <th>Estoque Atual</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody id="modalProductList">
            <?php foreach ($produtos as $produto): ?>
              <tr>
                <td><?php echo $produto['id']; ?></td>
                <td><?php echo $produto['nome_produto']; ?></td>
                <td><?php echo $produto['estoque']; ?></td>
                <td>
                  <button
                    type="button"
                    class="btn btn-primary btn-select-product"
                    data-id="<?php echo $produto['id']; ?>"
                    data-name="<?php echo $produto['nome_produto']; ?>"
                    data-estoque="<?php echo $produto['estoque']; ?>">
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

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const productList = document.getElementById('product-list');
    const modalElement = document.getElementById('productModal');
    const modal = new bootstrap.Modal(modalElement);
    let activeInput = null;
    let productIndex = 0;

    // Abrir modal ao clicar no input de produto
    document.addEventListener('click', function(e) {
      if (e.target && e.target.classList.contains('product-input')) {
        activeInput = e.target; // Define qual campo foi clicado
        modal.show(); // Abre o modal
      }
    });

    // Selecionar produto no modal
    document.addEventListener('click', function(e) {
      if (e.target && e.target.classList.contains('btn-select-product')) {
        const productName = e.target.getAttribute('data-name'); // Nome do produto
        const productId = e.target.getAttribute('data-id'); // ID do produto
        const productStock = e.target.getAttribute('data-estoque'); // Estoque do produto

        if (activeInput) {
          activeInput.value = productName;
          activeInput.name = `produtos[${activeInput.dataset.index}][nome]`;

          // Campo hidden para o ID do produto
          const hiddenIdInput = activeInput.parentElement.querySelector('.product-id');
          if (hiddenIdInput) {
            hiddenIdInput.value = productId;
            hiddenIdInput.name = `produtos[${activeInput.dataset.index}][id]`;
          }

          // Campo hidden para o estoque do produto
          const hiddenStockInput = activeInput.parentElement.querySelector('.product-stock');
          if (hiddenStockInput) {
            hiddenStockInput.value = productStock;
            hiddenStockInput.name = `produtos[${activeInput.dataset.index}][estoque]`;
          }

          modal.hide(); // Fecha o modal
        }
      }
    });

    // Adicionar novo campo de produto
    document.addEventListener('click', function(e) {
      if (e.target && e.target.classList.contains('btn-add')) {
        e.preventDefault();
        productIndex++;

        // Adiciona novo produto
        const productItem = document.createElement('div');
        productItem.classList.add('row', 'g-3', 'align-items-end', 'product-item');
        productItem.innerHTML = `
        <div class="col-lg-6">
          <input
            type="text"
            class="form-control product-input"
            placeholder="Clique para selecionar um produto"
            readonly
            data-index="${productIndex}"
          >
          <input type="hidden" name="produtos[${productIndex}][id]" class="product-id">
          <input type="hidden" name="produtos[${productIndex}][estoque]" class="product-stock">
        </div>
        <div class="col-lg-4">
          <input type="number" class="form-control" name="produtos[${productIndex}][quantidade]" placeholder="Quantidade" required>
        </div>
        <div class="col-lg-2">
          <button type="button" class="btn btn-danger btn-remove">-</button>
        </div>
      `;
        productList.appendChild(productItem);
      }
    });

    // Remover um campo de produto
    document.addEventListener('click', function(e) {
      if (e.target && e.target.classList.contains('btn-remove')) {
        e.preventDefault();
        e.target.closest('.product-item').remove();
      }
    });
  });
</script>