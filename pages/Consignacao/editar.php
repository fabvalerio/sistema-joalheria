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

  // Função para converter valor brasileiro para formato americano
  $converterValor = function($valor) {
    if (empty($valor) || $valor === '') return 0;
    
    // Converte para string e remove espaços
    $valor = trim((string)$valor);
    
    // Se tem vírgula, é formato brasileiro (ex: 100,00 ou 1.000,50)
    if (strpos($valor, ',') !== false) {
      // Remove pontos de milhar primeiro
      $valor = str_replace('.', '', $valor);
      // Depois substitui vírgula por ponto decimal
      $valor = str_replace(',', '.', $valor);
    } elseif (strpos($valor, '.') !== false) {
      // Se tem ponto mas não vírgula, verifica se é formato americano
      $partes = explode('.', $valor);
      // Se tem apenas um ponto e a parte após o ponto tem 1-2 dígitos = formato americano
      if (count($partes) === 2 && strlen($partes[1]) <= 2 && strlen($partes[1]) > 0) {
        // Formato americano válido (ex: 100.50), mantém como está
      } else {
        // Múltiplos pontos ou formato inválido = remove todos os pontos (trata como milhares)
        $valor = str_replace('.', '', $valor);
      }
    }
    
    return floatval($valor);
  };

  // Captura os dados do status e itens
  $dadosAtualizados = [
    'id' => $id,
    'status' => $_POST['status'] ?? null,
    'valor' => $_POST['valor'] ?? $consignacao['valor'], // Novo valor atualizado
    'desconto_percentual' => $_POST['desconto_percentual'] ?? $consignacao['desconto_percentual'],
    'bonificacao' => $converterValor($_POST['bonificacao'] ?? $consignacao['bonificacao'] ?? 0),
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
      <div class="col-lg-4">
        <strong>Cliente:</strong>
        <br>
        <?= htmlspecialchars(
          !empty($consignacao['nome_pf'])
            ? $consignacao['nome_pf']
            : ($consignacao['nome_fantasia_pj'] ?? 'Não informado')
        ) ?>
      </div>
      <div class="col-lg-4">
        <strong>Data da Consignação:</strong>
        <br>
        <?= htmlspecialchars(date('d/m/Y', strtotime($consignacao['data_consignacao']))) ?>
      </div>
      
      <?php 
      // Calcular subtotal dos itens
      $subtotal = 0;
      foreach ($itens as $item) {
          $qtd_restante = $item['quantidade'] - ($item['qtd_devolvido'] ?? 0);
          $subtotal += $qtd_restante * $item['valor'];
      }
      
      // Obter desconto percentual
      $desconto_percentual = $consignacao['desconto_percentual'] ?? 0;
      
      // Calcular valor do desconto
      $valor_desconto = ($subtotal * $desconto_percentual) / 100;
      
      // Calcular total com desconto
      $valor_total = $subtotal - $valor_desconto;
      ?>
      
      <div class="col-lg-3">
        <strong>Subtotal:</strong>
        <p class="mb-0">R$ <span id="subtotal_display"><?= number_format($subtotal, 2, ',', '.'); ?></span></p>
      </div>
      <div class="col-lg-3">
        <strong>Desconto (%):</strong>
        <p class="mb-0"><span id="desconto_percentual"><?= number_format($desconto_percentual, 2, ',', '.'); ?></span>%</p>
        <input type="hidden" id="desconto_percentual_hidden" name="desconto_percentual" value="<?= $desconto_percentual ?>">
      </div>
      <div class="col-lg-3">
        <strong>Valor do Desconto:</strong>
        <p class="mb-0">R$ <span id="valor_desconto_display"><?= number_format($valor_desconto, 2, ',', '.'); ?></span></p>
      </div>
      <div class="col-lg-3">
        <strong>Valor Total:</strong>
        <p class="mb-0 text-success fw-bold fs-5">R$ <span id="valor_total"><?= number_format($valor_total, 2, ',', '.'); ?></span></p>
      </div>
    </div>

    <hr>
    <h4 class="card-title">Itens da Consignação</h4>
    <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$id}" ?>" class="needs-validation" novalidate>
      <div class="row g-3 mb-3">
        <div class="col-lg-6">
          <label for="status" class="form-label"><strong>Status:</strong></label>
          <select class="form-select" id="status" name="status">
            <option value="Aberta" <?= ($consignacao['status'] ?? '') === 'Aberta' ? 'selected' : '' ?>>Aberta</option>
            <option value="Finalizada" <?= ($consignacao['status'] ?? '') === 'Finalizada' ? 'selected' : '' ?>>Finalizada</option>
          </select>
        </div>
        <div class="col-lg-6">
          <label for="bonificacao" class="form-label"><strong>Bonificação (%)*</strong></label>
          <input type="text" class="form-control" id="bonificacao" name="bonificacao" placeholder="0" value="<?= floatval($consignacao['bonificacao'] ?? 0) ?>">
        </div>
      </div>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Quantidade Devolvida</th>
            <th>Valor Unitário (R$)</th>
            <th>Valor c/ Desconto (R$)</th>
            <th>Subtotal (R$)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($itens as $item): 
            $valor_com_desconto = $item['valor'] - ($item['valor'] * $desconto_percentual / 100);
          ?>
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
                R$<?= number_format($valor_com_desconto, 2, ',', '.'); ?>
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

      <!-- Campo Hidden para o valor atualizado (com desconto aplicado) -->
      <input type="hidden" id="valor" name="valor" value="<?= number_format($valor_total, 2, '.', '') ?>">

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
    const valorTotalDisplay = document.getElementById('valor_total');
    const subtotalDisplay = document.getElementById('subtotal_display');
    const valorDescontoDisplay = document.getElementById('valor_desconto_display');
    const descontoPercentualElement = document.getElementById('desconto_percentual');
    
    // Obter desconto percentual
    const descontoPercentual = parseFloat(descontoPercentualElement.textContent.replace(',', '.')) || 0;

    function calcularValores() {
      let subtotal = 0;

      // Calcular subtotal dos itens restantes (não devolvidos)
      devolvidoInputs.forEach(el => {
        const preco = parseFloat(el.dataset.preco) || 0;
        const quantidade = parseFloat(el.dataset.quantidade) || 0;
        const devolvido = parseFloat(el.value) || 0;
        const restante = quantidade - devolvido;

        subtotal += restante * preco;
      });

      // Calcular valor do desconto
      const valorDesconto = (subtotal * descontoPercentual) / 100;
      
      // Calcular total com desconto
      const total = subtotal - valorDesconto;

      // Atualizar displays
      subtotalDisplay.textContent = subtotal.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
      
      valorDescontoDisplay.textContent = valorDesconto.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });

      valorTotalDisplay.textContent = total.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
      
      // Atualizar campo hidden com o valor total (com desconto)
      valorHidden.value = total.toFixed(2);
    }

    // Adicionar evento de input para todos os campos de quantidade devolvida
    devolvidoInputs.forEach(input => {
      input.addEventListener('input', calcularValores);
    });
  });
</script>