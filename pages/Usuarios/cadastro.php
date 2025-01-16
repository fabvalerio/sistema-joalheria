<?php

use App\Models\Usuarios\Controller;
// Obter os cargos
// Instanciar o Controller de Usuários
$controller = new Controller();
// Obter os cargos
$cargos = $controller->cargos();

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'nome_completo' => $_POST['nome_completo'],
        'email' => $_POST['email'],
        'cargo' => $_POST['cargo'],
        'telefone' => $_POST['telefone'],
        'rg' => $_POST['rg'],
        'emissao_rg' => $_POST['emissao_rg'],
        'cpf' => $_POST['cpf'],
        'data_nascimento' => $_POST['data_nascimento'],
        'cep' => $_POST['cep'],
        'endereco' => $_POST['endereco'],
        'cidade' => $_POST['cidade'],
        'estado' => $_POST['estado'],
        'login' => $_POST['login'],
        'senha' => password_hash($_POST['senha'], PASSWORD_DEFAULT),
        'nivel_acesso' => $_POST['nivel_acesso'],
        'bairro' => $_POST['bairro'],
        'numero' => $_POST['numero']
    ];

    $controller = new Controller();
    $return = $controller->cadastro($dados);

    if ($return) {
        echo notify('success', "Cadastrado com sucesso!");
    } else {
        echo notify('danger', "Erro ao cadastrar.");
    }
}

?>

<div class="card">

    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro de Usuário</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>

            <div class="row g-3">
                <div class="col-lg-4">
                    <label for="" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" name="nome_completo" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">E-mail</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Cargo</label>
                    <!-- select com lista de cargos -->
                    <select class="form-select" name="cargo" required>
                        <option value="">Selecione</option>
                        <?php foreach ($cargos as $cargo) : ?>
                            <option value="<?= $cargo['cargo'] ?>"><?= $cargo['cargo'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Telefone</label>
                    <input type="text" class="form-control" name="telefone" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">RG</label>
                    <input type="text" class="form-control" name="rg" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Emissão do RG</label>
                    <input type="date" class="form-control" name="emissao_rg" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">CPF</label>
                    <input type="text" class="form-control" id="cpf" name="cpf" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Data de Nascimento</label>
                    <input type="date" class="form-control" name="data_nascimento" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep" required>
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" required>
                </div>
                <div class="col-lg-2">
                    <label for="" class="form-label">Numero</label>
                    <input type="text" class="form-control" id="numero" name="numero" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade" required>
                </div>
                 <div class="col-lg-4">
                    <label for="" class="form-label">Bairro</label>
                    <input type="text" class="form-control" id="bairro" name="bairro" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Estado</label>
                    <input type="text" class="form-control" id="estado" name="estado" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Login</label>
                    <input type="text" class="form-control" name="login" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Senha</label>
                    <input type="password" class="form-control" name="senha" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Nível de Acesso</label>
                    <select class="form-select" name="nivel_acesso" required>
                        <option value="">Selecione o Nível de Acesso</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Operador">Operador</option>
                        <option value="Consulta">Consulta</option>
                    </select>

                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar</button>
                </div>
            </div>

        </form>
    </div>

</div>

<script>
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
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


