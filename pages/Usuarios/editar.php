<?php

use App\Models\Usuarios\Controller; // Altere para o namespace correto

// ID do registro a ser editado
$id = $link['3'];

// Buscar os dados do registro para preencher o formulário
$controller = new Controller();
$return = $controller->ver($id);


// Verificar se o registro foi encontrado
if (!$return) {
    echo notify('danger', "Usuário não encontrado.");
    exit;
}

// Obter os cargos para o select
$cargos = $controller->cargos();

// Atualizar o registro se o formulário foi enviado
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
        'nivel_acesso' => $_POST['nivel_acesso'],
        'bairro' => $_POST['bairro'],
        'numero' => $_POST['numero']
    ];

    // Atualizar a senha apenas se for informada no formulário
    if (!empty($_POST['senha'])) {
        $dados['senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    }

    $returnUpdate = $controller->editar($id, $dados);

    if ($returnUpdate) {
        echo notify('success', "Usuário atualizado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao atualizar o usuário.");
    }
}

?>

<div class="card">

    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Editar Usuário</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$id}" ?>" class="needs-validation" novalidate>

            <div class="row g-3">
                <div class="col-lg-4">
                    <label for="" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" name="nome_completo" value="<?= $return['nome_completo'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">E-mail</label>
                    <input type="email" class="form-control" name="email" value="<?= $return['email'] ?>" required>
                </div>
                <div class="col-lg-4">
    <label for="" class="form-label">Cargo</label>
    <select class="form-select" name="cargo" required>
        <option value="">Selecione</option>
        <?php foreach ($cargos as $cargo) : ?>
            <option 
                value="<?= htmlspecialchars($cargo['id']) ?>" 
                <?= ($return['cargo'] == $cargo['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cargo['cargo']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>


                <div class="col-lg-4">
                    <label for="" class="form-label">Telefone</label>
                    <input type="text" class="form-control" name="telefone" value="<?= $return['telefone'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">RG</label>
                    <input type="text" class="form-control" name="rg" value="<?= $return['rg'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Data de Emissão do RG</label>
                    <input type="date" class="form-control" name="emissao_rg" value="<?= $return['emissao_rg'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">CPF</label>
                    <input type="text" class="form-control" name="cpf" value="<?= $return['cpf'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Data de Nascimento</label>
                    <input type="date" class="form-control" name="data_nascimento" value="<?= $return['data_nascimento'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep" value="<?= $return['cep'] ?>" required>
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" value="<?= $return['endereco'] ?>" required>
                </div>
                <div class="col-lg-2">
                    <label for="" class="form-label">N°</label>
                    <input type="text" class="form-control" name="numero" value="<?= $return['numero'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade" value="<?= $return['cidade'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Bairro</label>
                    <input type="text" class="form-control" id="bairro" name="bairro" value="<?= $return['bairro'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Estado</label>
                    <input type="text" class="form-control" id="estado" name="estado" value="<?= $return['estado'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Login</label>
                    <input type="text" class="form-control" name="login" value="<?= $return['login'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Senha (Deixe em branco para não alterar)</label>
                    <input type="password" class="form-control" name="senha">
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Nível de Acesso</label>
                    <select class="form-select" name="nivel_acesso" required>
                        <option value="">Selecione o Nível de Acesso</option>
                        <option value="Administrador" <?= $return['nivel_acesso'] == 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                        <option value="Operador" <?= $return['nivel_acesso'] == 'Operador' ? 'selected' : '' ?>>Operador</option>
                        <option value="Consulta" <?= $return['nivel_acesso'] == 'Consulta' ? 'selected' : '' ?>>Consulta</option>
                    </select>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar Alterações</button>
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