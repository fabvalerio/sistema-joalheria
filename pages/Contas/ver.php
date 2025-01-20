<?php

use App\Models\FinanceiroContas\Controller;

// Instanciar o Controller
$controller = new Controller();

// Capturar o ID da conta na URL
$id = $link['3'];

if (!$id) {
    echo notify('danger', "ID da conta não foi especificado.");
    exit;
}

// Obter a conta existente
$conta = $controller->ver($id);

if (!$conta) {
    echo notify('danger', "Conta não encontrada.");
    exit;
}

// Obter listas de clientes, fornecedores e categorias
$clientes = $controller->listarClientes();
$fornecedores = $controller->listarFornecedores();
$categorias = $controller->listarCategorias();

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Detalhes da Conta</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
  </div>

  <div class="card-body">
    <div class="row g-3">
      <!-- Tipo da Conta -->
      <div class="col-lg-6">
        <label class="form-label"><strong>Tipo da Conta:</strong></label>
        <p><?= $conta['tipo'] === 'R' ? 'Contas a Receber' : 'Contas a Pagar' ?></p>
      </div>

      <!-- Cliente ou Fornecedor -->
      <?php if ($conta['tipo'] === 'R'): ?>
      <div class="col-lg-6">
        <label class="form-label"><strong>Cliente:</strong></label>
        <p>
          <?= htmlspecialchars(
                array_column($clientes, 'nome_pf', 'id')[$conta['cliente_id']] ?? 'N/A'
              ) ?>
        </p>
      </div>
      <?php else: ?>
      <div class="col-lg-6">
        <label class="form-label"><strong>Fornecedor:</strong></label>
        <p>
          <?= htmlspecialchars(
                array_column($fornecedores, 'razao_social', 'id')[$conta['fornecedor_id']] ?? 'N/A'
              ) ?>
        </p>
      </div>
      <?php endif; ?>

      <!-- Categoria -->
      <div class="col-lg-6">
        <label class="form-label"><strong>Categoria:</strong></label>
        <p>
          <?= htmlspecialchars(
                array_column($categorias, 'descricao', 'id')[$conta['categoria_id']] ?? 'N/A'
              ) ?>
        </p>
      </div>

      <!-- Data de Vencimento -->
      <div class="col-lg-6">
        <label class="form-label"><strong>Data de Vencimento:</strong></label>
        <p><?= date('d/m/Y', strtotime($conta['data_vencimento'])) ?></p>
      </div>

      <!-- Valor Total -->
      <div class="col-lg-6">
        <label class="form-label"><strong>Valor Total (R$):</strong></label>
        <p><?= number_format($conta['valor'], 2, ',', '.') ?></p>
      </div>

      <!-- Data de Pagamento -->
      <div class="col-lg-6">
        <label class="form-label"><strong>Data de Pagamento:</strong></label>
        <p><?= $conta['data_pagamento'] ? date('d/m/Y', strtotime($conta['data_pagamento'])) : 'N/A' ?></p>
      </div>

      <!-- Status -->
      <div class="col-lg-6">
        <label class="form-label"><strong>Status:</strong></label>
        <p><?= htmlspecialchars($conta['status']) ?></p>
      </div>

      <!-- Recorrente -->
      <div class="col-lg-6">
        <label class="form-label"><strong>Recorrente:</strong></label>
        <p><?= $conta['recorrente'] === 'S' ? 'Sim' : 'Não' ?></p>
      </div>

      <!-- Observação -->
      <div class="col-lg-12">
        <label class="form-label"><strong>Observação:</strong></label>
        <p><?= htmlspecialchars($conta['observacao']) ?></p>
      </div>

      <!-- Parcelas -->
      <?php if ($conta['num_parcelas'] > 1): ?>
      <div class="col-lg-12">
        <label class="form-label"><strong>Parcelas:</strong></label>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Parcela</th>
              <th>Valor (R$)</th>
              <th>Data de Vencimento</th>
            </tr>
          </thead>
          <tbody>
            <?php for ($i = 1; $i <= $conta['num_parcelas']; $i++): ?>
              <tr>
                <td><?= $i ?>ª</td>
                <td><?= number_format($conta['val_par' . $i], 2, ',', '.') ?></td>
                <td><?= date('d/m/Y', strtotime($conta['dt_par' . $i])) ?></td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
