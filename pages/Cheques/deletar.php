<?php

use App\Models\Cheques\Controller;

$id = $link['3'];

$controller = new Controller();
$cheque = $controller->ver($id);

if (!$cheque) {
    echo notify('danger', "Cheque não encontrado.");
    exit;
}

if (isset($link['4']) && $link['4'] == 'deletar') {
    $return = $controller->deletar($id);

    if ($return) {
        echo notify('success', "Cheque deletado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao deletar o cheque.");
    }
}

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Deletar Cheque</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
  </div>

  <div class="card-body">
    <div class="row g-3">
      <div class="col-lg-6">
        <label class="form-label d-block fw-bold">Nome do Cheque</label>
        <?php echo htmlspecialchars($cheque['nome_cheque']); ?>
      </div>
      <div class="col-lg-6">
        <label class="form-label d-block fw-bold">Máximo de Parcelas</label>
        <?php echo htmlspecialchars($cheque['max_parcelas'] ?? '') . 'x'; ?>
      </div>
    </div>
    <div class="mt-3">
      <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar"; ?>">Deletar</a>
    </div>
  </div>
</div>
