<?php

use App\Models\Orcamento\Controller;

$controller = new Controller();
$pedidos = $controller->listarFabrica();

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Lista de Pedidos em Aberto</h3>
  </div>

  <div class="card-body">
    <table id="example1" class="table table-striped table-hover">
      <thead class="bg-light">
        <tr>
          <th>ID</th>
          <th>Pedido</th>
          <th>Cliente</th>
          <th>Data do Pedido</th>
          <th>Data de Entrega</th>
          <th>Status</th>
          <th>Fábrica</th>
          <th>Total (R$)</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pedidos as $pedido): ?>
          <tr>
            <td><?= $pedido['fid'] ?></td>
            <td><?= $pedido['id'] ?></td>
            <td>
              <?= !empty($pedido['nome_pf']) ? $pedido['nome_pf'] : $pedido['nome_fantasia_pj']?>
            </td>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($pedido['data_pedido']))) ?></td>
            <td><?= !empty($pedido['data_entrega'])
                  ? htmlspecialchars(date('d/m/Y', strtotime($pedido['data_entrega'])))
                  : 'Não informado'; ?>
            </td>
            <td><span class="badge bg-info"><?= htmlspecialchars($pedido['status'] ?? 'Necessário Iniciar') ?> </span></td>
            <td><span class="badge bg-<?= !empty($pedido['status_fabrica']) ? 'danger' : 'success' ?>"><?= ($pedido['status_fabrica'] ?? 'Balcão') ?> </span></td>
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
                  <?php if( empty($pedido['fid']) ){ ?>
                  <li>
                      <a href="<?= "{$url}!/Fabrica/registrar/{$pedido['fid']}/{$pedido['id']}" ?>" class="dropdown-item">Iniciar Produção</a>
                  </li>
                  <?php }else{  ?>

                    <a href="<?= "{$url}!/Fabrica/pedido/{$pedido['fid']}/{$pedido['id']}" ?>" class="dropdown-item">Acomparnhar Produção</a>
                  <?php } ?>
                  <li>
                    <a href="<?= "{$url}!/{$link[1]}/ver/{$pedido['id']}" ?>" class="dropdown-item">Visualizar</a>
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