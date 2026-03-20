<?php

use App\Models\Pedidos\Controller;
use App\Models\Caixa\Controller as CaixaController;

$controller = new Controller();
$pedidos = $controller->listar();

$loja_id = $_COOKIE['loja_id'] ?? 1;
$caixaController = new CaixaController();
$caixas = $caixaController->listarCaixasPorLoja($loja_id);

$caixa_drawer_id = isset($_GET['caixa_drawer_id']) && $_GET['caixa_drawer_id'] !== ''
    ? (int)$_GET['caixa_drawer_id']
    : (( !empty($caixas) ) ? (int)$caixas[0]['id'] : 0);
$drawerSegment = $caixa_drawer_id ? '/' . $caixa_drawer_id : '';

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Lista de Pedidos</h3>
    <?php if (isset($podeManipular) && $podeManipular($link[1])): ?><a href="<?php echo "{$url}!/{$link[1]}/cadastro"; ?>" class="btn btn-white text-primary">Adicionar Pedido</a><?php endif; ?>
  </div>

  <div class="card-body">
    <?php if (!empty($caixas)): ?>
      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label text-dark">Gaveta (Caixa #)</label>
          <select class="form-select" onchange="window.location.href='<?= $url ?? '' ?>!/Pedidos/listar?caixa_drawer_id='+this.value">
            <?php foreach ($caixas as $caixa): ?>
              <option value="<?= (int)$caixa['id'] ?>" <?= ((int)$caixa['id'] === (int)($caixa_drawer_id ?? 0)) ? 'selected' : '' ?>>
                Caixa #<?= htmlspecialchars($caixa['numero']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6 d-flex align-items-end">
          <div class="text-muted">
            Ao marcar <strong>Pago</strong>, o movimento será lançado na gaveta selecionada.
          </div>
        </div>
      </div>
    <?php endif; ?>
    <table id="example1" class="table table-striped table-hover">
      <thead class="bg-light">
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Forma de Pagamento</th>
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
            <td><?= htmlspecialchars($pedido['id']) ?></td>
            <td>
              <?= !empty($pedido['nome_pf']) ? $pedido['nome_pf'] : $pedido['nome_fantasia_pj'] ?>
            </td>
            <td><?= htmlspecialchars($pedido['forma_pagamento'] ?? 'Não informado') ?></td>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($pedido['data_pedido']))) ?></td>
            <td><?= !empty($pedido['data_entrega'])
                  ? htmlspecialchars(date('d/m/Y', strtotime($pedido['data_entrega'])))
                  : 'Não informado'; ?>
            </td>
            <td><span class="badge bg-<?= $pedido['status_pedido'] == 'Pendente' ? 'warning' : 'success' ?>"><?= htmlspecialchars($pedido['status_pedido']) ?> </span></td>
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
                  <?php if (isset($podeManipular) && $podeManipular($link[1])): ?>
                  <li>
                    <?php if ($pedido['status_pedido'] === 'Pendente') { ?>
                      <a href="<?= "{$url}!/{$link[1]}/mudarStatus/{$pedido['id']}/Pago" . $drawerSegment ?>"
                        class="dropdown-item">
                        Alterar para PAGO
                      </a>
                    <?php } else { ?>
                      <a href="<?= "{$url}!/{$link[1]}/mudarStatus/{$pedido['id']}/Pendente" . $drawerSegment ?>"
                        class="dropdown-item">
                        Alterar para PENDENTE
                      </a>
                    <?php } ?>
                  </li>
                  <li>
                    <a href="<?= "{$url}!/Notas/emitir-nota/{$pedido['id']}" ?>" class="dropdown-item">Emitir Nota</a>
                  </li>
                  <?php endif; ?>
                  <li>
                    <a href="<?= "{$url}pages/Pedidos/imprimir.php?id={$pedido['id']}&via=cliente" ?>" target="_blank" class="dropdown-item">Imprimir Pedido de Venda</a>
                  </li>
                  <li>
                    <?php if (!empty($pedido['status_fabrica'])) { ?>
                      <a href="<?= "{$url}!/Fabrica/pedido/{$pedido['id']}" ?>"
                        class="dropdown-item">
                        Acompanhar Produção
                      </a>
                    <?php } ?>
                  </li>
                  <li>
                    <a href="<?= "{$url}!/{$link[1]}/ver/{$pedido['id']}" ?>" class="dropdown-item">Visualizar</a>
                  </li>
                  <?php if (isset($podeManipular) && $podeManipular($link[1])): ?>
                  <li>
                    <a href="<?= "{$url}!/{$link[1]}/deletar/{$pedido['id']}" ?>" class="dropdown-item text-danger">Excluir</a>
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