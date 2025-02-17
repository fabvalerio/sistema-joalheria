<?php

use App\Models\FinanceiroContas\Controller;

// Capturar o tipo de conta selecionado pelo usuário
$tipo = $_POST['tipo'] ?? null;

// Garantir que o tipo é válido ('R' para Receber, 'P' para Pagar ou null para todos)
if (!in_array($tipo, ['R', 'P'])) {
  $tipo = null;
}

// Capturar os outros filtros enviados pelo formulário via POST
// Definir datas padrão (primeiro e último dia do mês atual)
$dataInicioPadrao = date('Y-m-01');
$dataFimPadrao = date('Y-m-t');

// Capturar os filtros enviados pelo formulário via POST, mantendo os valores padrão
$dataInicio = $_POST['data_inicio'] ?? $dataInicioPadrao;
$dataFim = $_POST['data_fim'] ?? $dataFimPadrao;

$status = $_POST['status'] ?? null;

// Instanciar o Controller e buscar dados filtrados
$controller = new Controller();
$contas = $controller->listarComFiltro($tipo, $dataInicio, $dataFim, $status);
?>




<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Relatório de Contas</h3>
    <h4 class="mb-3 text-primary"><i class="fas fa-filter"></i> Filtros</h4>

  </div>

  <div class="card-body">


    <form method="POST" class="row  align-items-end mb-4">

      <div class="col-md-1">

        <div class="card-body d-flex justify-content-between align-items-center">
          <h4 class="text-primary d-flex align-items-center m-0">
            <i class="fas fa-filter me-2"></i>Filtros
          </h4>

        </div>
      </div>
      <div class="col-md-2">
        <label for="tipo" class="form-label">Tipo de Conta:</label>
        <select id="tipo" name="tipo" class="form-control">
          <option value="">Todas</option>
          <option value="R" <?= ($_POST['tipo'] ?? '') == 'R' ? 'selected' : '' ?>>Contas a Receber</option>
          <option value="P" <?= ($_POST['tipo'] ?? '') == 'P' ? 'selected' : '' ?>>Contas a Pagar</option>
        </select>
      </div>
      <div class="col-md-2">
        <label for="data_inicio" class="form-label">Data Início:</label>
        <input type="date" id="data_inicio" name="data_inicio" value="<?= $dataInicio ?>" class="form-control">
      </div>
      <div class="col-md-2">
        <label for="data_fim" class="form-label">Data Fim:</label>
        <input type="date" id="data_fim" name="data_fim" value="<?= $dataFim ?>" class="form-control">
      </div>
      <div class="col-md-2">
        <label for="status" class="form-label">Status:</label>
        <select id="status" name="status" class="form-control">
          <option value="">Todos</option>
          <option value="Pago" <?= ($_POST['status'] ?? '') == 'Pago' ? 'selected' : '' ?>>Pago</option>
          <option value="Pendente" <?= ($_POST['status'] ?? '') == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
        </select>
      </div>
      <div class="col-md-2 d-flex">
        <button type="submit" class="btn btn-primary flex-grow-1">Filtrar</button>
      </div>
      <div class="col-md-1 d-flex">
        <a href="<?= "{$url}!/{$link[1]}/relatorio/" ?>" class="btn btn-secondary flex-grow-1 ms-2">Limpar</a>
      </div>
    </form>
    <hr>

    <?php
    $totalReceber = 0;
    $totalPagar = 0;

    foreach ($contas as $conta) {
      if ($conta['tipo'] == 'R') {
        $totalReceber += $conta['valor'];
      } elseif ($conta['tipo'] == 'P') {
        $totalPagar += $conta['valor'];
      }
    }
    ?>

    <div class="mt-4">
      <h4 class="mb-3 text-primary"><i class="fas fa-chart-line"></i> Resumo Financeiro</h4>

      <div class="row align-items-center g-3">
        <div class="col-md-4">
          <div class="card border-dark shadow-sm">
            <div class="card-body text-center">
              <p class="card-text m-0 text-sm"><i class="fas fa-search"></i>
                Exibindo <strong>
                  <?= $tipo === 'R' ? 'Contas a Receber' : ($tipo === 'P' ? 'Contas a Pagar' : 'Todas as Contas') ?></strong>
                no período de <strong><?= date('d/m/Y', strtotime($dataInicio)) ?></strong>
                até <strong><?= date('d/m/Y', strtotime($dataFim)) ?></strong> com Status <strong><?= $status === 'Pago' ? 'Pago' : ($status === 'Pendente' ? 'Pendente' : 'Todos') ?></strong>
              </p>
            </div>
          </div>
        </div>

        <?php if ($tipo === 'R' || $tipo === null): ?>
          <div class="col-md-4">
            <div class="card border-success shadow-sm">
              <div class="card-body text-center">
                <h5 class="text-success"><i class="fas fa-hand-holding-usd"></i> Total a Receber</h5>
                <h3 class="fw-bold text-success">R$ <?= number_format($totalReceber, 2, ',', '.') ?></h3>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($tipo === 'P' || $tipo === null): ?>
          <div class="col-md-4">
            <div class="card border-danger shadow-sm">
              <div class="card-body text-center">
                <h5 class="text-danger"><i class="fas fa-money-bill-wave"></i> Total a Pagar</h5>
                <h3 class="fw-bold text-danger">R$ <?= number_format($totalPagar, 2, ',', '.') ?></h3>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <hr>
    <table id="example1" class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Tipo</th>
          <th>Data Vencimento</th>
          <th>Valor</th>
          <th>Status</th>
          <th>Relacionado</th>
          <th>Categoria</th>
          <th width="220">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($contas as $conta): ?>
          <tr>
            <td><?= $conta['id'] ?></td>
            <td><?= htmlspecialchars($conta['tipo'] == 'P' ? 'Contas a Pagar' : 'Contas a Receber') ?></td>
            <td><?= htmlspecialchars($conta['data_vencimento']) ?></td>
            <td>R$ <?= number_format($conta['valor'], 2, ',', '.') ?></td>
            <td><span class="badge bg-<?= $conta['status'] == 'Pago' ? 'success' : 'warning' ?>"><?= htmlspecialchars($conta['status']) ?> </span></td>

            <!-- Coluna Relacionada (Fornecedor ou Cliente) -->
            <td>
              <?php if ($conta['tipo'] == 'P'): ?>
                <?= htmlspecialchars($conta['fornecedor_nome'] ?? 'Não informado') ?>
              <?php else: ?>
                <?= htmlspecialchars($conta['cliente_nome'] ?? 'Não informado') ?>
              <?php endif; ?>
            </td>

            <!-- Coluna Categoria (Só aparece para Contas a Pagar) -->
            <td>
              <?= $conta['tipo'] == 'P' ? htmlspecialchars($conta['categoria_nome'] ?? 'Não informado') : 'Cliente' ?>
            </td>

            <td>
              <a href="<?= "{$url}!/{$link[1]}/ver/{$conta['id']}/$link[3]" ?>" target="_blank" class="btn  btn-primary flex-grow-1 ms-2"> Ver</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>


  </div>
</div>