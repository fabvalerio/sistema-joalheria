<?php

use App\Models\Orcamento\Controller;

$controller = new Controller();
$pedidos = $controller->listarFabricaAndamentoEncerrado();

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Lista de Pedidos em Andamento</h3>
  </div>

  <div class="card-body">
    <table id="example1" class="table table-striped table-hover">
      <thead class="bg-light">
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Data do Pedido</th>
          <th>Data de Entrega</th>
          <th>Status</th>
          <th>Etapa</th>
          <th>Fábrica</th>
          <th>Total (R$)</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pedidos as $pedido): ?>
          <tr>
            <td><?= htmlspecialchars($pedido['id']) ?>/<?php echo $pedido['pidId'] ?></td>
            <td>
              <?= !empty($pedido['nome_pf']) ? $pedido['nome_pf'] : $pedido['nome_fantasia_pj'] ?>
            </td>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($pedido['data_pedido']))) ?></td>
            <td><?= !empty($pedido['data_entrega'])
                  ? htmlspecialchars(date('d/m/Y', strtotime($pedido['data_entrega'])))
                  : 'Não informado'; ?>
            </td>
            <td><span class="badge bg-info"><?= htmlspecialchars($pedido['status']) ?> </span></td>
            <td><span class="badge bg-dark"><?= htmlspecialchars($pedido['qtd_etapas'] ?? 'N/A') ?> </span></td>
            <td><span class="badge bg-<?= !empty($pedido['status_fabrica']) ? 'danger' : 'success' ?>"><?= ($pedido['status_fabrica'] ?? 'Balcão') ?> </span></td>
            <td>
              R$<?= isset($pedido['total']) && $pedido['total'] !== null
                  ? number_format($pedido['total'], 2, ',', '.')
                  : '0,00'; ?>
            </td>
            <td>
              <a href="<?= "{$url}!/Fabrica/pedido/{$pedido['id']}/{$pedido['pidId']}" ?>" class="btn btn-primary">
                Acompanhar
              </a>
            </td>


          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>