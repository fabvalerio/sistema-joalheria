<?php

use App\Models\Modelo\Controller; // Altere para o nome "Modelo" para nome da Tabela do SQL

//Carregar daddos do Banco dea deos
$controller = new Controller();
$return = $controller->ver($link['3']);

if (!$return) {
    echo notify('danger', "Agente não encontrado.");
    exit;
}

// Verificar se o formulário foi enviado
if(  $_SERVER['REQUEST_METHOD'] == 'POST' ){

    $dados = [
        'nome' => $_POST['nome'] ?? 0,
        'taxa_comissao' => $_POST['taxa_comissao'] ?? 0,
        'telefone' => $_POST['telefone'] ?? 0,
        'email' => $_POST['email'] ?? 0,
        'senha' => !empty($_POST['senha'] ) ? $_POST['senha'] : $return['senha']
    ];

    // ID para editar
    $id = $_POST['id'];

    // Instanciar o Controller e executar a edição
    $controller = new Controller();
    $editar = $controller->editar($id, $dados);

    // Verificar se a edição foi realizada
    if ($editar) {
        echo notify('success',"Editado com sucesso!");
    } else {
        echo notify('danger', "Erro ao editar.");
    }

}

?>

<div class="card">

    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Editar Usuário</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar"?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">

    <!-- ACTION já esta programado para rota com salvamento via POST -->
    <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}"?>" class="needs-validation" novalidate>

        <div class="row g-3">
                <div class="col-lg-4">
                    <label for="" class="form-label">Nome</label>
                    <input type="text" class="form-control" name="nome" value="<?php echo $return['nome'] ?? ''; ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Celular</label>
                    <input type="text" class="form-control" name="telefone" value="<?php echo $return['telefone'] ?? ''; ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">E-mail</label>
                    <input type="text" class="form-control" name="email" value="<?php echo $return['email'] ?? ''; ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Senha (vazio, mantem o mesmo)</label>
                    <input type="password" class="form-control" name="senha" value="">
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Comissão (%)</label>
                    <input type="number" class="form-control" name="taxa_comissao" value="<?php echo $return['taxa_comissao'] ?? ''; ?>" required>
                </div>
                <div class="col-lg-12">

                    <!-- ID para editar -->
                    <input type="hidden" name="id" value="<?php echo $return['id'] ?? ''; ?>"> 

                    <button type="submit" class="btn btn-primary float-end">Salvar</button>

                </div>
        </div>

    </form>
    
    </div>

</div>

<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
(() => {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }

      form.classList.add('was-validated')
    }, false)
  })
})()
</script>