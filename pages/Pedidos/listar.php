<?php

use App\Models\Pedidos\Controller;

$controller = new Controller();
$pedidos = $controller->listar();

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Lista de Pedidos</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/cadastro"; ?>" class="btn btn-white text-primary">Adicionar Pedido</a>
  </div>

  <div class="card-body">
    <table id="example1" class="table table-striped table-hover">
      <thead class="bg-light">
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Forma de Pagamento</th>
          <th>Data do Pedido</th>
          <th>Data de Entrega</th>
          <th>Status</th>
          <th>Total (R$)</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pedidos as $pedido): ?>
          <tr>
            <td><?= htmlspecialchars($pedido['id']) ?></td>
            <td>
              <?= htmlspecialchars($pedido['nome_pf'] ?? $pedido['nome_fantasia_pj'] ?? 'Não informado') ?>
            </td>
            <td><?= htmlspecialchars($pedido['forma_pagamento'] ?? 'Não informado') ?></td>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($pedido['data_pedido']))) ?></td>
            <td><?= !empty($pedido['data_entrega'])
                  ? htmlspecialchars(date('d/m/Y', strtotime($pedido['data_entrega'])))
                  : 'Não informado'; ?>
            </td>
            <td><?= htmlspecialchars($pedido['status_pedido']) ?></td>
            <td>
              R$<?= isset($pedido['total']) && $pedido['total'] !== null
                  ? number_format($pedido['total'], 2, ',', '.')
                  : '0,00'; ?>
            </td>
            <td>
              <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Ação
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <?php if ($pedido['status_pedido'] === 'Pendente') { ?>
                      <a href="<?= "{$url}!/{$link[1]}/mudarStatus/{$pedido['id']}/Emandamento" ?>"
                        class="dropdown-item">
                        Alterar para PAGO
                      </a>
                    <?php } else { ?>
                      <a href="<?= "{$url}!/{$link[1]}/mudarStatus/{$pedido['id']}/Pendente" ?>"
                        class="dropdown-item">
                        Alterar para PENDENTE
                      </a>
                    <?php } ?>
                  </li>
                  <li>
                    <a href="<?= "{$url}!/{$link[1]}/ver/{$pedido['id']}" ?>" class="dropdown-item">Ver</a>
                  </li>
                  <li>
                    <a href="<?= "{$url}!/{$link[1]}/deletar/{$pedido['id']}" ?>" class="dropdown-item text-danger">Excluir</a>
                  </li>
                </ul>
              </div>
            </td>


          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>