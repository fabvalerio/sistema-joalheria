<?php

use App\Models\Cartoes\Controller;

$id = $link['3']; // ID do cartão a ser deletado

// Buscar os dados do cartão para exibição
$controller = new Controller();
$cartao = $controller->ver($id);

// Verificar se o cartão foi encontrado
if (!$cartao) {
    echo notify('danger', "Cartão não encontrado.");
    exit;
}

// Deletar o cartão se o comando for confirmado
if (isset($link['4']) && $link['4'] == 'deletar') {
    $return = $controller->deletar($id);

    if ($return) {
        echo notify('success', "Cartão deletado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao deletar o cartão.");
    }
}

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Deletar Cartão</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
  </div>

  <div class="card-body">
    <div class="row g-3">
      <div class="col-lg-6">
        <label class="form-label d-block fw-bold">Nome do Cartão</label>
        <?php echo htmlspecialchars($cartao['nome_cartao']); ?>
      </div>
      <div class="col-lg-6">
        <label class="form-label d-block fw-bold">Taxa Administradora (%)</label>
        <?php echo htmlspecialchars($cartao['taxa_administradora']); ?>
      </div>
      <div class="col-lg-6">
        <label class="form-label d-block fw-bold">Tipo</label>
        <?php echo htmlspecialchars($cartao['tipo']); ?>
      </div>
      <div class="col-lg-6">
        <label class="form-label d-block fw-bold">Bandeira</label>
        <?php echo htmlspecialchars($cartao['bandeira']); ?>
      </div>
      <div class="col-lg-6">
        <label class="form-label d-block fw-bold">Máximo de Parcelas</label>
        <?php echo htmlspecialchars($cartao['max_parcelas']) . 'x'; ?>
      </div>
    </div>
    <div class="mt-3">
      <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar"; ?>">Deletar</a>
    </div>
  </div>
</div>
