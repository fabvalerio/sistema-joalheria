<?php

use App\Models\Cartoes\Controller;

$id = $link['3']; // ID do cartão a ser visualizado

// Buscar os dados do cartão
$controller = new Controller();
$cartao = $controller->ver($id);

// Verificar se o cartão foi encontrado
if (!$cartao) {
    echo notify('danger', "Cartão não encontrado.");
    exit;
}

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Detalhes do Cartão</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
  </div>

  <div class="card-body">
    <div class="row g-3">
      <div class="col-lg-6">
        <label class="form-label d-block fw-bold">Nome do Cartão</label>
        <?php echo htmlspecialchars($cartao['nome_cartao'] ?? 'Não informado'); ?>
      </div>
      <div class="col-lg-6">
        <label class="form-label d-block fw-bold">Taxa Administradora (%)</label>
        <?php echo htmlspecialchars($cartao['taxa_administradora'] ?? 'Não informado'); ?>
      </div>
      <div class="col-lg-6">
        <label class="form-label d-block fw-bold">Tipo</label>
        <?php echo htmlspecialchars($cartao['tipo'] ?? 'Não informado'); ?>
      </div>
      <div class="col-lg-6">
        <label class="form-label d-block fw-bold">Bandeira</label>
        <?php echo htmlspecialchars($cartao['bandeira'] ?? 'Não informado'); ?>
      </div>
      <div class="col-lg-6">
        <label class="form-label d-block fw-bold">Máximo de Parcelas</label>
        <?php echo htmlspecialchars(($cartao['max_parcelas'] ?? 'Não informado') . 'x'); ?>
      </div>
      
      <?php for ($i = 1; $i <= 12; $i++): ?>
        <div class="col-lg-3">
          <label class="form-label d-block fw-bold">Juros Parcela <?= $i ?> (%)</label>
          <?php echo htmlspecialchars($cartao["juros_parcela_$i"] ?? 'Não informado'); ?>
        </div>
      <?php endfor; ?>
    </div>
  </div>
</div>
