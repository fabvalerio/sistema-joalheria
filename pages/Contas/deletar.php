<?php

use App\Models\FinanceiroContas\Controller;

// ID da conta para deletar
$id = $link['3'];

// Exibir os detalhes do registro para confirmar a exclusão
$controller = new Controller();
$return = $controller->ver($id);

// Verificar se o registro foi encontrado
if (!$return) {
  echo notify('danger', "Conta não encontrada.");
  exit;
}

// Verificar se o comando para deletar foi enviado
if (isset($link['4']) && $link['4'] == 'deletar') {
  // Deletar o registro com o valor de $id
  $deletar = new Controller();
  $resultado = $deletar->deletar($id);

  // Verificar se a exclusão foi realizada
  if ($resultado) {
    echo notify('success', "Conta deletada com sucesso!");
    echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar/' . $link[5] . '">';
  } else {
    echo notify('danger', "Erro ao deletar a conta.");
  }
}

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Deletar Conta</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/listar/{$link[4]}" ?>" class="btn btn-warning text-primary">Voltar</a>
  </div>


  <div class="card-body">
    <p class="text-danger fw-bold">
      Tem certeza de que deseja excluir a conta abaixo? Esta ação não pode ser desfeita.
    </p>
    <div class="row g-3">
      <div class="col-lg-4">
        <label for="" class="form-label d-block fw-bold">Tipo da Conta</label>
        <?php echo $return['tipo'] === 'R' ? 'Contas a Receber' : 'Contas a Pagar'; ?>
      </div>
      <div class="col-lg-4">
        <label for="" class="form-label d-block fw-bold">Data de Vencimento</label>
        <?php echo date('d/m/Y', strtotime($return['data_vencimento'])); ?>
      </div>
      <div class="col-lg-4">
        <label for="" class="form-label d-block fw-bold">Valor Total (R$)</label>
        <?php echo number_format($return['valor'], 2, ',', '.'); ?>
      </div>
      <div class="col-lg-4">
        <label for="" class="form-label d-block fw-bold">Status</label>
        <?php echo htmlspecialchars($return['status']); ?>
      </div>
      <div class="col-lg-4">
        <label for="" class="form-label d-block fw-bold">Recorrente</label>
        <?php echo $return['recorrente'] === 'S' ? 'Sim' : 'Não'; ?>
      </div>
      <div class="col-lg-12">
        <label for="" class="form-label d-block fw-bold">Observação</label>
        <?php echo htmlspecialchars($return['observacao']); ?>
      </div>
    </div>
    <div class="mt-3">
      <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar/{$link[4]}"; ?>">Deletar</a>
    </div>
  </div>
</div>