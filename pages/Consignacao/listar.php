<?php

use App\Models\Consignacao\Controller;

$controller = new Controller();
$consignacoes = $controller->listar();

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Lista de Consignações</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/cadastro"; ?>" class="btn btn-white text-primary">Adicionar Consignação</a>
  </div>

  <div class="card-body">
    <table id="example1" class="table table-striped table-hover">
      <thead class="bg-light">
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Data da Consignação</th>
          <th>Status</th>
          <th>Valor Total (R$)</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($consignacoes as $consignacao): ?>
          <tr>
            <td><?= htmlspecialchars($consignacao['id']) ?></td>
            <td>
              <?= htmlspecialchars($consignacao['nome_fantasia_pj'] ?? $consignacao['nome_fantasia_pj'] ?? 'Não informado') ?>
            </td>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($consignacao['data_consignacao']))) ?></td>
            <td><?= htmlspecialchars($consignacao['status']) ?></td>
            <td>
              R$<?= isset($consignacao['valor']) && $consignacao['valor'] !== null
                  ? number_format($consignacao['valor'], 2, ',', '.')
                  : '0,00'; ?>
            </td>
            <td>
              <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Ação
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <a href="<?= "{$url}!/{$link[1]}/ver/{$consignacao['id']}" ?>" class="dropdown-item">Ver</a>
                  </li>
                  <?php if ($consignacao['status'] === 'Aberta'): ?>
                  <li>
                    <a href="<?= "{$url}!/{$link[1]}/editar/{$consignacao['id']}" ?>" class="dropdown-item">Editar</a>
                  </li>
                  <li>
                    <a href="<?= "{$url}!/{$link[1]}/deletar/{$consignacao['id']}" ?>" class="dropdown-item text-danger">Excluir</a>
                  </li>
                  <?php endif; ?>
                </ul>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
