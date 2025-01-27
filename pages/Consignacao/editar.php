<?php

use App\Models\Consignacao\Controller;

$controller = new Controller();
$id = $link[3] ?? null;

if (!$id) {
  echo notify('danger', 'ID da consignação não informado.');
  echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
  exit;
}

$dados = $controller->ver($id);
$consignacao = $dados['consignacao'];
$itens = $dados['itens'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  // Captura os dados do status e itens
  $dadosAtualizados = [
    'id' => $id,
    'status' => $_POST['status'] ?? null,
    'valor' => $_POST['valor'] ?? $consignacao['valor'], // Novo valor atualizado
    'itens' => []
  ];

  // Captura as quantidades devolvidas de cada item
  if (!empty($_POST['itens'])) {
    foreach ($_POST['itens'] as $itemId => $item) {
      $dadosAtualizados['itens'][] = [
        'id' => $item['id'] ?? null,
        'qtd_devolvido' => (float)($item['qtd_devolvido'] ?? 0),
        'produto_id' => $item['produto_id']
      ];
    }
  }

  // Debug para verificar os dados enviados no POST
  // echo '<pre>';
  // print_r($dadosAtualizados);
  // echo '</pre>';
  // exit;

  $return = $controller->editar($id, $dadosAtualizados);

  if ($return) {
    echo notify('success', "Consignação atualizada com sucesso!");
    echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
  } else {
    echo notify('danger', "Erro ao atualizar a consignação.");
  }
}

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Editar Consignação</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/listar"; ?>" class="btn btn-warning text-primary">Voltar</a>
  </div>

  <div class="card-body">
    <h4 class="card-title">Dados da Consignação</h4>
    <div class="row g-3">
      <div class="col-lg-6">
        <strong>Cliente:</strong>
        <?= htmlspecialchars(
          !empty($consignacao['nome_pf'])
            ? $consignacao['nome_pf']
            : ($consignacao['nome_fantasia_pj'] ?? 'Não informado')
        ) ?>
      </div>
      <div class="col-lg-6">
        <strong>Data da Consignação:</strong>
        <?= htmlspecialchars(date('d/m/Y', strtotime($consignacao['data_consignacao']))) ?>
      </div>
      <div class="col-lg-6">
        <strong>Valor Total:</strong>
        <span class="text-success text-lg">R$<span id="valor_total">
            <?= isset($consignacao['valor'])
              ? number_format($consignacao['valor'], 2, ',', '.')
              : '0,00'; ?>
          </span></span>
      </div>
    </div>

    <hr>
    <h4 class="card-title">Itens da Consignação</h4>
    <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$id}" ?>" class="needs-validation" novalidate>
      <div class="col-lg-6">
        <label for="status" class="form-label"><strong>Status:</strong></label>
        <select class="form-select" id="status" name="status">
          <option value="Finalizada">Finalizada</option>
        </select>
      </div>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Quantidade Devolvida</th>
            <th>Valor Unitário (R$)</th>
            <th>Subtotal (R$)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($itens as $item): ?>
            <tr>
              <td><?= htmlspecialchars($item['nome_produto'] ?? 'Produto não encontrado') ?></td>
              <td><?= htmlspecialchars($item['quantidade'] ?? '0') ?></td>
              <td>
                <!-- Campo para a quantidade devolvida -->
                <input type="number" step="0.01" class="form-control devolvido-input"
                  name="itens[<?= htmlspecialchars($item['id']) ?>][qtd_devolvido]"
                  data-preco="<?= htmlspecialchars($item['valor']) ?>"
                  data-quantidade="<?= htmlspecialchars($item['quantidade']) ?>"
                  value="<?= htmlspecialchars($item['qtd_devolvido'] ?? '0.00') ?>"
                  <?php if ($consignacao['status'] === 'Finalizada') {
                    echo 'readonly';
                  } ?>>
                <!-- Campo oculto para o identificador correto -->
                <input type="hidden"
                  name="itens[<?= htmlspecialchars($item['id']) ?>][id]"
                  value="<?= htmlspecialchars($item['id'] ?? '') ?>">
                <input type="hidden"
                  name="itens[<?= htmlspecialchars($item['id']) ?>][produto_id]"
                  value="<?= htmlspecialchars($item['produto_id'] ?? '') ?>">
              </td>
              <td>
                R$<?= isset($item['valor'])
                    ? number_format($item['valor'], 2, ',', '.')
                    : '0,00'; ?>
              </td>
              <td>
                R$<span class="subtotal">
                  <?= number_format(($item['quantidade'] ?? 0) * ($item['valor'] ?? 0), 2, ',', '.'); ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Campo Hidden para o valor atualizado -->
      <input type="hidden" id="valor" name="valor" value="<?= htmlspecialchars($consignacao['valor']) ?>">

      <div class="col-lg-12 mt-1 text-danger">
        <?php if ($consignacao['status'] === 'Finalizada' || $consignacao['status'] === 'Cancelada') {
          echo 'Voce nao pode editar a Quantidade devolvida de uma consignacao Finalizada.';
        } ?>
      </div>

      <div class="col-lg-12 mt-4">
        <button type="submit" class="btn btn-primary float-end">Salvar Alterações</button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const devolvidoInputs = document.querySelectorAll('.devolvido-input');
    const valorHidden = document.getElementById('valor');
    const valorTotal = document.getElementById('valor_total');

    devolvidoInputs.forEach(input => {
      input.addEventListener('input', () => {
        let total = 0;

        devolvidoInputs.forEach(el => {
          const preco = parseFloat(el.dataset.preco) || 0;
          const quantidade = parseFloat(el.dataset.quantidade) || 0;
          const devolvido = parseFloat(el.value) || 0;
          const restante = quantidade - devolvido;

          total += restante * preco;
        });

        valorHidden.value = total.toFixed(2);
        valorTotal.textContent = total.toLocaleString('pt-BR', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
      });
    });
  });
</script>