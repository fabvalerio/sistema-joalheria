<?php

use App\Models\Cartoes\Controller;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $dados = [
    'nome_cartao' => $_POST['nome_cartao'],
    'taxa_administradora' => $_POST['taxa_administradora'],
    'tipo' => $_POST['tipo'],
    'bandeira' => $_POST['bandeira'],
    'max_parcelas' => $_POST['max_parcelas'],
    'juros_parcela_1' => $_POST['juros_parcela_1'],
    'juros_parcela_2' => $_POST['juros_parcela_2'],
    'juros_parcela_3' => $_POST['juros_parcela_3'],
    'juros_parcela_4' => $_POST['juros_parcela_4'],
    'juros_parcela_5' => $_POST['juros_parcela_5'],
    'juros_parcela_6' => $_POST['juros_parcela_6'],
    'juros_parcela_7' => $_POST['juros_parcela_7'],
    'juros_parcela_8' => $_POST['juros_parcela_8'],
    'juros_parcela_9' => $_POST['juros_parcela_9'],
    'juros_parcela_10' => $_POST['juros_parcela_10'],
    'juros_parcela_11' => $_POST['juros_parcela_11'],
    'juros_parcela_12' => $_POST['juros_parcela_12']
  ];

  $controller = new Controller();
  $return = $controller->cadastro($dados);

  if ($return) {
    echo notify('success', "Cartão cadastrado com sucesso!");
    echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
  } else {
    echo notify('danger', "Erro ao cadastrar o cartão.");
  }
}

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Cadastro de Cartão</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
  </div>

  <div class="card-body">
    <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>
      <div class="row g-3">
        <div class="col-lg-6">
          <label class="form-label">Nome do Cartão</label>
          <input type="text" class="form-control" name="nome_cartao" required>
        </div>
        <div class="col-lg-6">
          <label class="form-label">Taxa Administradora (%)</label>
          <input type="number" step="0.01" class="form-control" name="taxa_administradora" required>
        </div>
        <div class="col-lg-6">
          <label class="form-label">Tipo</label>
          <select class="form-select" name="tipo" required>
            <option value="">Selecione o Tipo</option>
            <option value="Crédito">Crédito</option>
            <option value="Débito">Débito</option>
          </select>
        </div>
        <div class="col-lg-6">
          <label class="form-label">Bandeira</label>
          <select class="form-select" name="bandeira" required>
            <option value="">Selecione a Bandeira</option>
            <option value="Visa">Visa</option>
            <option value="Mastercard">Mastercard</option>
          </select>
        </div>
        <div class="col-lg-6">
          <label class="form-label">Máximo de Parcelas</label>
          <select class="form-select" name="max_parcelas" required>
            <option value="">Selecione o Máximo de Parcelas</option>
            <?php for ($i = 1; $i <= 12; $i++): ?>
              <option value="<?= $i ?>"><?= $i ?>x</option>
            <?php endfor; ?>
          </select>
        </div>

        <?php for ($i = 1; $i <= 12; $i++): ?>
          <div class="col-lg-3">
            <label class="form-label">Juros Parcela <?= $i ?> (%)</label>
            <input type="number" step="0.01" class="form-control" name="juros_parcela_<?= $i ?>">
          </div>
        <?php endfor; ?>
        <div class="col-lg-12">
          <button type="submit" class="btn btn-primary float-end">Salvar</button>
        </div>
      </div>
    </form>
  </div>
</div>