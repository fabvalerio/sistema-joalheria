<?php

use App\Models\Fabrica\Controller;

$controller = new Controller();
$pedidos = $controller->ver($link[3]);

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
          <th>Data do Pedido</th>
          <th>Data de Entrega</th>
          <th>Fábrica</th>
          <th>Responsável Atual</th>
          <th>Início</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pedidos as $pedido): ?>
          <tr>
            <td><?= htmlspecialchars($pedido['id']) ?></td>
            <td>
              <?= !empty($pedido['nome_pf']) ? $pedido['nome_pf'] : $pedido['nome_fantasia_pj']?>
            </td>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($pedido['data_pedido']))) ?></td>
            <td><?= !empty($pedido['data_entrega'])
                  ? htmlspecialchars(date('d/m/Y', strtotime($pedido['data_entrega'])))
                  : 'Não informado'; ?>
            </td>
            <td><span class="badge bg-<?= !empty($pedido['status_fabrica']) ? 'danger' : 'success' ?>"><?= ($pedido['status_fabrica'] ?? 'Balcão') ?> </span></td>
            <td><?= ($pedido['nome'] ?? '') ?></td>
            <td><?= dia($pedido['data_inicio'] ?? '') ?></td>
            <td><?= statusFabrica($pedido['status']) ?? '' ?></td>
            <td>
              <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Ação
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <a href="<?= "{$url}!/{$link[1]}/pedido/{$pedido['id']}" ?>" class="dropdown-item">Acompanhar</a>
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