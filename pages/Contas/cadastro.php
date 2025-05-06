<?php

use App\Models\FinanceiroContas\Controller;

$link[3] = empty($link[3]) ? null : $link[3];

// Obter listas de clientes, fornecedores e categorias
$controller = new Controller();
$clientes = $controller->listarClientes();
$fornecedores = $controller->listarFornecedores();
$categorias = $controller->listarCategorias();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $dados = [
    'fornecedor_id' => $_POST['fornecedor_id'] ?? null,
    'cliente_id' => $_POST['cliente_id'] ?? null,
    'categoria_id' => $_POST['categoria_id'] ?? null,
    'data_vencimento' => $_POST['data_vencimento'],
    'valor' => $_POST['valor'],
    'data_pagamento' => $_POST['data_pagamento'] ?? null,
    'status' => $_POST['status'],
    'observacao' => $_POST['observacao'],
    'recorrente' => $_POST['recorrente'],
    'tipo' => $_POST['tipo'],
    'num_parcelas' => $_POST['num_parcelas'] ?? null,
    'val_par1' => $_POST['val_par1'] ?? null,
    'dt_par1' => $_POST['dt_par1'] ?? null,
    'val_par2' => $_POST['val_par2'] ?? null,
    'dt_par2' => $_POST['dt_par2'] ?? null,
    'val_par3' => $_POST['val_par3'] ?? null,
    'dt_par3' => $_POST['dt_par3'] ?? null,
    'val_par4' => $_POST['val_par4'] ?? null,
    'dt_par4' => $_POST['dt_par4'] ?? null,
    'val_par5' => $_POST['val_par5'] ?? null,
    'dt_par5' => $_POST['dt_par5'] ?? null,
    'val_par6' => $_POST['val_par6'] ?? null,
    'dt_par6' => $_POST['dt_par6'] ?? null,
    'val_par7' => $_POST['val_par7'] ?? null,
    'dt_par7' => $_POST['dt_par7'] ?? null,
    'val_par8' => $_POST['val_par8'] ?? null,
    'dt_par8' => $_POST['dt_par8'] ?? null,
    'val_par9' => $_POST['val_par9'] ?? null,
    'dt_par9' => $_POST['dt_par9'] ?? null,
    'val_par10' => $_POST['val_par10'] ?? null,
    'dt_par10' => $_POST['dt_par10'] ?? null,
    'val_par11' => $_POST['val_par11'] ?? null,
    'dt_par11' => $_POST['dt_par11'] ?? null,
    'val_par12' => $_POST['val_par12'] ?? null,
    'dt_par12' => $_POST['dt_par12'] ?? null
  ];

  $return = $controller->cadastro($dados);

  if ($return) {
    echo notify('success', "Conta cadastrada com sucesso!");
    echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar/' . $link[3] . '">';
  } else {
    echo notify('danger', "Erro ao cadastrar a conta.");
  }
}

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Cadastro de Contas</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/listar/{$link[3]}" ?>" class="btn btn-warning text-primary">Voltar</a>
  </div>

  <div class="card-body">
    <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>
      <div class="row g-3">
        <!-- Tipo da Conta -->
        <div class="col-lg-6">
          <label class="form-label">Tipo</label>
          <select class="form-select" name="tipo" id="tipo-conta" required>
            <option value="">Selecione o Tipo</option>
            <option value="R">Contas a Receber</option>
            <option value="P">Contas a Pagar</option>
          </select>
        </div>

        <!-- Cliente (para Contas a Receber) -->
        <div class="col-lg-6" id="cliente-field" style="display: none;">
          <label class="form-label">Cliente</label>
          <select class="form-select" name="cliente_id">
            <option value="">Selecione um Cliente</option>
            <?php foreach ($clientes as $cliente): ?>
              <option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['nome_pf'] ?: $cliente['razao_social_pj']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Fornecedor (para Contas a Pagar) -->
        <div class="col-lg-6" id="fornecedor-field" style="display: none;">
          <label class="form-label">Fornecedor</label>
          <select class="form-select" name="fornecedor_id">
            <option value="">Selecione um Fornecedor</option>
            <?php foreach ($fornecedores as $fornecedor): ?>
              <option value="<?= $fornecedor['id'] ?>"><?= htmlspecialchars($fornecedor['razao_social']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Categoria (somente para Contas a Pagar) -->
        <div class="col-lg-6" id="categoria-field" style="display: none;">
          <label class="form-label">Categoria</label>
          <select class="form-select" name="categoria_id">
            <option value="">Selecione uma Categoria</option>
            <?php foreach ($categorias as $categoria): ?>
              <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['descricao']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Data de Vencimento -->
        <div class="col-lg-6">
          <label class="form-label">Data de Vencimento</label>
          <input type="date" class="form-control" name="data_vencimento" required>
        </div>

        <!-- Número de Parcelas (somente para Contas a Pagar) -->
        <div class="col-lg-6" id="num-parcelas-field" style="display: none;">
          <label class="form-label">Número de Parcelas</label>
          <select class="form-select" name="num_parcelas" id="num_parcelas">
            <?php for ($i = 1; $i <= 12; $i++): ?>
              <option value="<?= $i ?>"><?= $i ?>x</option>
            <?php endfor; ?>
          </select>
        </div>

        <!-- Valor Total -->
        <div class="col-lg-6">
          <label class="form-label">Valor Total (R$)</label>
          <input type="number" step="0.01" class="form-control" name="valor" id="valor_total" required>
        </div>

        <!-- Campos de Parcelas -->
        <div id="parcelas-container" style="display: none;">
          <?php for ($i = 1; $i <= 12; $i++): ?>
            <div class="row g-3 parcela-fields" id="parcela_<?= $i ?>" style="display: none;">
              <div class="col-lg-6">
                <label class="form-label">Valor da Parcela <?= $i ?> (R$)</label>
                <input type="number" step="0.01" class="form-control parcela-input" name="val_par<?= $i ?>" data-parcela="<?= $i ?>" readonly>
              </div>
              <div class="col-lg-6">
                <label class="form-label">Data da Parcela <?= $i ?></label>
                <input type="date" class="form-control" name="dt_par<?= $i ?>">
              </div>
            </div>
          <?php endfor; ?>
        </div>



        <!-- Status -->
        <div class="col-lg-6">
          <label class="form-label">Status</label>
          <select class="form-select" name="status" required>
            <option value="Pendente">Pendente</option>
            <option value="Pago">Pago</option>
          </select>
        </div>

        <!-- Recorrente -->
        <div class="col-lg-6" id="recorrente-field" style="display: none;">
          <label class="form-label">Recorrente</label>
          <select class="form-select" name="recorrente">
            <option value="N">Não</option>
            <option value="S">Sim</option>
          </select>
        </div>

        <!-- Observação -->
        <div class="col-lg-12">
          <label class="form-label">Observação</label>
          <textarea class="form-control" name="observacao" rows="3"></textarea>
        </div>

        <!-- Botão Salvar -->
        <div class="col-lg-12">
          <button type="submit" class="btn btn-primary float-end">Salvar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  // Resetar formulário ao mudar tipo de conta
  document.getElementById('tipo-conta').addEventListener('change', function() {
    const tipo = this.value;
    document.getElementById('cliente-field').style.display = (tipo === 'R') ? 'block' : 'none';
    document.getElementById('fornecedor-field').style.display = (tipo === 'P') ? 'block' : 'none';
    document.getElementById('categoria-field').style.display = (tipo === 'P') ? 'block' : 'none';
    document.getElementById('recorrente-field').style.display = (tipo === 'P') ? 'block' : 'none';
    document.getElementById('num-parcelas-field').style.display = (tipo === 'P') ? 'block' : 'none';
    document.getElementById('parcelas-container').style.display = 'none';
    document.getElementById('valor_total').value = '';
    document.getElementById('num_parcelas').value = '1';
    document.querySelectorAll('.parcela-fields').forEach(field => field.style.display = 'none');
  });

  // Exibir/ocultar parcelas e atualizar parcelas dinamicamente
  document.getElementById('num_parcelas').addEventListener('change', function() {
    const numParcelas = parseInt(this.value);
    const parcelasContainer = document.getElementById('parcelas-container');
    const valorTotalInput = document.getElementById('valor_total');
    const parcelaFields = document.querySelectorAll('.parcela-fields');

    // Mostrar ou esconder os campos de parcelas
    parcelasContainer.style.display = (numParcelas > 1) ? 'block' : 'none';
    parcelaFields.forEach((field, index) => {
      field.style.display = (index < numParcelas) ? 'flex' : 'none';
    });

    // Atualizar datas automaticamente se a data de vencimento estiver preenchida
    const dataVencimento = document.querySelector('input[name="data_vencimento"]').value;
    if (dataVencimento) {
      atualizarDatasParcelas(dataVencimento, numParcelas);
    }

    // Recalcular valores das parcelas automaticamente
    if (valorTotalInput.value) {
      atualizarParcelas(parseFloat(valorTotalInput.value), numParcelas);
    }
  });

  // Atualizar valores das parcelas com base no valor total
  document.getElementById('valor_total').addEventListener('input', function() {
    const valorTotal = parseFloat(this.value) || 0;
    const numParcelas = parseInt(document.getElementById('num_parcelas').value) || 1;
    atualizarParcelas(valorTotal, numParcelas);
  });

  // Atualizar datas automaticamente com base na data de vencimento
  document.querySelector('input[name="data_vencimento"]').addEventListener('change', function() {
    const dataVencimento = this.value;
    const numParcelas = parseInt(document.getElementById('num_parcelas').value) || 1;
    atualizarDatasParcelas(dataVencimento, numParcelas);
  });

  // Função para recalcular e distribuir valores das parcelas
  function atualizarParcelas(valorTotal, numParcelas) {
    const parcelaFields = document.querySelectorAll('.parcela-fields .parcela-input');
    const valorParcela = (valorTotal / numParcelas).toFixed(2);

    parcelaFields.forEach((input, index) => {
      if (index < numParcelas) {
        input.value = valorParcela;
      } else {
        input.value = '';
      }
    });
  }

  // Função para calcular e distribuir as datas das parcelas
  function atualizarDatasParcelas(dataInicial, numParcelas) {
    const parcelaDates = document.querySelectorAll('.parcela-fields input[type="date"]');
    const dataBase = new Date(dataInicial);

    parcelaDates.forEach((input, index) => {
      if (index < numParcelas) {
        const novaData = new Date(dataBase);
        novaData.setMonth(dataBase.getMonth() + index); // Incrementa meses
        input.value = novaData.toISOString().split('T')[0]; // Formata para AAAA-MM-DD
      } else {
        input.value = '';
      }
    });
  }
</script>
