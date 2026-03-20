<?php

use App\Models\Cheques\Controller;

$id = $link['3'];

$controller = new Controller();
$cheque = $controller->ver($id);

if (!$cheque) {
    echo notify('danger', "Cheque não encontrado.");
    exit;
}

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Detalhes do Cheque</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
  </div>

  <div class="card-body">
    <div class="row g-3">
      <div class="col-lg-6">
        <label class="form-label d-block fw-bold">Nome do Cheque</label>
        <?php echo htmlspecialchars($cheque['nome_cheque'] ?? 'Não informado'); ?>
      </div>
      <div class="col-lg-6">
        <label class="form-label d-block fw-bold">Máximo de Parcelas</label>
        <?php echo htmlspecialchars(($cheque['max_parcelas'] ?? 'Não informado') . 'x'); ?>
      </div>

      <?php for ($i = 1; $i <= 12; $i++): ?>
        <div class="col-lg-3">
          <label class="form-label d-block fw-bold">Juros Parcela <?= $i ?> (%)</label>
          <?php echo htmlspecialchars($cheque["juros_parcela_$i"] ?? '-'); ?>
        </div>
      <?php endfor; ?>
    </div>
  </div>
</div>
