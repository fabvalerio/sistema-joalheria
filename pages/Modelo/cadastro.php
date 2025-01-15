<?php

use App\Models\Modelo\Controller; // Altere para o nome "Modelo" para nome da Tabela do SQL


// Verificar se o formulário foi enviado
if(  $_SERVER['REQUEST_METHOD'] == 'POST' ){

    // Obter os dados do formulário e que será executado o cadastro
    $dados = [
        'nome' => $_POST['nome'],
        'taxa_comissao' => $_POST['taxa_comissao'] ?? 0, // " ?? 0" Se não for informado, será 0
        'status' => '1',
        'telefone' => $_POST['telefone'] ?? 0,
        'email' => $_POST['email'] ?? 0,
        'senha' => md5($_POST['senha'])
    ];

    // Instanciar o Controller e executar o cadastro
    $controller = new Controller();
    $return = $controller->cadastro($dados);

    
    // Verificar se o cadastro foi realizado
    if ($return) {
        echo notify('success',"Cadastrado com sucesso!");
    } else {
        echo notify('danger', "Erro ao cadastrar.");
    }

}

?>

<div class="card">

    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Usuarios</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar"?>" class="btn btn-warning text-primary">Voltar</a> 
    </div>

    <div class="card-body">

    <!-- ACTION já esta programado para rota com salvamento via POST -->
    <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}"?>" class="needs-validation" novalidate>

        <div class="row g-3">
                <div class="col-lg-4">
                    <label for="" class="form-label">Nome</label>
                    <input type="text" class="form-control" name="nome" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Celular</label>
                    <input type="text" class="form-control" name="telefone" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">E-mail</label>
                    <input type="text" class="form-control" name="email" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Senha</label>
                    <input type="password" class="form-control" name="senha" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Comissão (%)</label>
                    <input type="number" class="form-control" name="taxa_comissao" required>
                </div>
                <div class="col-lg-12">
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