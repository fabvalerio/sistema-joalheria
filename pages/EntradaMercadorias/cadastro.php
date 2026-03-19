<?php

use App\Models\EntradaMercadorias\Controller;

$controller = new Controller();
$fornecedores = $controller->listarFornecedores();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $erros = [];

  $nfFiscal = trim((string)($_POST['nf_fiscal'] ?? ''));
  $dataPedido = trim((string)($_POST['data_pedido'] ?? ''));
  $fornecedorIdRaw = $_POST['fornecedor_id'] ?? null;
  $fornecedorId = $fornecedorIdRaw !== null && $fornecedorIdRaw !== '' ? (int)$fornecedorIdRaw : 0;
  $dataPrevistaEntrega = trim((string)($_POST['data_prevista_entrega'] ?? ''));
  $valorRaw = $_POST['valor'] ?? null;

  if ($nfFiscal === '') $erros[] = 'Nota Fiscal é obrigatória.';
  if ($dataPedido === '') $erros[] = 'Data do Pedido é obrigatória.';
  if ($fornecedorId <= 0) $erros[] = 'Fornecedor é obrigatório.';
  if ($dataPrevistaEntrega === '') $erros[] = 'Data de Entrega é obrigatória.';
  if ($valorRaw === null || $valorRaw === '') $erros[] = 'Valor é obrigatório.';
  if ($valorRaw !== null && $valorRaw !== '' && (float)$valorRaw <= 0) $erros[] = 'Valor deve ser maior que zero.';

  $dados = [
    'nf_fiscal' => $nfFiscal !== '' ? $nfFiscal : null,
    'data_pedido' => $dataPedido !== '' ? $dataPedido : null,
    'fornecedor_id' => $fornecedorId > 0 ? $fornecedorId : null,
    'data_prevista_entrega' => $dataPrevistaEntrega !== '' ? $dataPrevistaEntrega : null,
    'data_entregue' => !empty($_POST['data_entregue']) ? $_POST['data_entregue'] : null,
    'transportadora' => !empty($_POST['transportadora']) ? $_POST['transportadora'] : null,
    'valor' => ($valorRaw !== null && $valorRaw !== '') ? $valorRaw : null,
    'observacoes' => !empty($_POST['observacoes']) ? $_POST['observacoes'] : null,
    'produtos' => [] // Inicializa o array de produtos
  ];

  // Capturar os produtos enviados via POST
  $produtosPost = $_POST['produtos'] ?? [];
  if (empty($produtosPost) || !is_array($produtosPost)) {
    $erros[] = 'Produtos e Quantidades são obrigatórios.';
  } else {
    foreach ($produtosPost as $produto) {
      $id = $produto['id'] ?? null;
      $nomeProduto = $produto['nome'] ?? null;
      $qtd = $produto['quantidade'] ?? null;
      $estoque = $produto['estoque'] ?? 0;

      if (empty($id) || empty($nomeProduto) || $qtd === null || $qtd === '') {
        $erros[] = 'Produtos e Quantidades não podem ficar em branco.';
        continue;
      }

      $idInt = (int)$id;
      $qtdInt = (int)$qtd;
      if ($idInt <= 0) {
        $erros[] = 'ID do produto inválido.';
        continue;
      }
      if ($qtdInt <= 0) {
        $erros[] = 'Quantidade deve ser maior que zero.';
        continue;
      }

      $dados['produtos'][] = [
        'id' => $idInt,
        'nome_produto' => (string)$nomeProduto,
        'quantidade' => $qtdInt,
        'estoque' => (int)$estoque
      ];
    }
  }

  if (!empty($erros)) {
    echo notify('danger', implode('<br>', array_unique($erros)));
    exit;
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
          <input
            type="hidden"
            id="valor_raw"
            name="valor"
            value="0.00"
          >
          <input
            type="text"
            class="form-control"
            id="valor_display"
            value="R$ 0,00"
            required
            autocomplete="off"
          >
          <div class="form-text">Digite o valor da compra. O sistema salva no formato decimal (SQL).</div>
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
        <div class="mb-3">
          <label for="modalProductSearch" class="form-label fw-bold">Filtrar Produto (ID ou Nome)</label>
          <input id="modalProductSearch" type="text" class="form-control" placeholder="Digite o ID ou o nome do produto...">
        </div>
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
            <tr>
              <td colspan="4" class="text-center text-muted">Digite para buscar produtos...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Mantém o campo "valor" em formato exibido (R$ 0,00) e envia ao backend
    // um decimal com 2 casas usando "." (ex.: 1234.56), compatível com SQL DECIMAL(10,2).
    const valorDisplay = document.getElementById('valor_display');
    const valorRaw = document.getElementById('valor_raw');

    function syncValorFromDisplay() {
      if (!valorDisplay || !valorRaw) return;

      const raw = (valorDisplay.value || '').toString();
      // Remove "R$", espaços e separadores de milhar comuns
      let s = raw.replace(/R\$/gi, '').replace(/\s/g, '');
      s = s.replace(/\.(?=\d{3}(\D|$))/g, ''); // remove pontos de milhar (se houver)
      s = s.replace(',', '.'); // "," vira decimal
      s = s.replace(/[^0-9.]/g, ''); // só números e ponto decimal

      // Se houver mais de um ponto, mantém só o último como decimal
      const firstDot = s.indexOf('.');
      if (firstDot !== -1) {
        const parts = s.split('.');
        const decimals = parts.pop();
        s = parts.join('') + '.' + decimals;
      }

      let num = parseFloat(s);
      if (!Number.isFinite(num)) num = 0;

      valorRaw.value = num.toFixed(2);
      // Reaplica formatação na tela
      const formatted = num.toFixed(2).replace('.', ',');
      valorDisplay.value = `R$ ${formatted}`;
    }

    if (valorDisplay && valorRaw) {
      // Inicializa raw coerente com o valor exibido
      syncValorFromDisplay();
      valorDisplay.addEventListener('input', () => {
        // Evita loops: ajusta raw e normaliza texto
        syncValorFromDisplay();
      });
    }

    const productList = document.getElementById('product-list');
    const modalElement = document.getElementById('productModal');
    const modal = new bootstrap.Modal(modalElement);
    const modalProductSearch = document.getElementById('modalProductSearch');
    let activeInput = null;
    let productIndex = 0;
    const baseUrlProdutos = '<?= $url ?>pages/EntradaMercadorias/produtos_json.php';

    // Abrir modal ao clicar no input de produto
    document.addEventListener('click', function(e) {
      if (e.target && e.target.classList.contains('product-input')) {
        activeInput = e.target; // Define qual campo foi clicado
        if (modalProductSearch) {
          modalProductSearch.value = '';
          modalProductSearch.dispatchEvent(new Event('input'));
        }
        modal.show(); // Abre o modal
      }
    });

    async function buscarProdutos(q) {
      if (!modalProductSearch) return;

      const tbody = document.getElementById('modalProductList');
      if (!tbody) return;

      tbody.innerHTML = `
        <tr>
          <td colspan="4" class="text-center text-muted">Carregando...</td>
        </tr>
      `;

      const params = new URLSearchParams();
      params.set('q', q || '');
      params.set('limit', '50');

      try {
        const resp = await fetch(baseUrlProdutos, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
          body: params.toString()
        });

        const json = await resp.json();
        if (!json || !json.ok) throw new Error(json?.msg || 'Erro ao buscar produtos');

        const rows = json.data || [];
        if (!rows.length) {
          tbody.innerHTML = `
            <tr>
              <td colspan="4" class="text-center text-muted">Nenhum produto encontrado.</td>
            </tr>
          `;
          return;
        }

        tbody.innerHTML = '';
        rows.forEach(prod => {
          const tr = document.createElement('tr');

          const tdId = document.createElement('td');
          tdId.textContent = prod.id ?? '';

          const tdNome = document.createElement('td');
          tdNome.textContent = prod.nome_produto ?? '';

          const tdEstoque = document.createElement('td');
          tdEstoque.textContent = prod.estoque ?? 0;

          const tdAcoes = document.createElement('td');
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'btn btn-primary btn-select-product';
          btn.textContent = 'Selecionar';
          btn.dataset.id = prod.id ?? '';
          btn.dataset.name = prod.nome_produto ?? '';
          btn.dataset.estoque = prod.estoque ?? 0;

          tdAcoes.appendChild(btn);
          tr.appendChild(tdId);
          tr.appendChild(tdNome);
          tr.appendChild(tdEstoque);
          tr.appendChild(tdAcoes);
          tbody.appendChild(tr);
        });
      } catch (e) {
        tbody.innerHTML = `
          <tr>
            <td colspan="4" class="text-center text-danger">Erro ao carregar produtos.</td>
          </tr>
        `;
      }
    }

    // Busca por digitação (debounce) - pesquisa por ID ou nome
    if (modalProductSearch) {
      let timer = null;
      modalProductSearch.addEventListener('input', function() {
        const q = (this.value || '').trim();
        clearTimeout(timer);
        timer = setTimeout(() => buscarProdutos(q), 250);
      });
    }

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