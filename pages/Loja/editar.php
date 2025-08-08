<?php

use App\Models\Loja\Controller; // Altere para o namespace correto

// ID do registro a ser editado
$id = $link['3'];

// Buscar os dados do registro para preencher o formulário
$controller = new Controller();
$return = $controller->ver($id);


// Verificar se o registro foi encontrado
if (!$return) {
    echo notify('danger', "Loja não encontrada.");
    exit;
}

// Atualizar o registro se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'nome' => $_POST['nome'],
        'cnpj' => $_POST['cnpj'],
        'responsavel' => $_POST['responsavel'],
        'cpf' => $_POST['cpf'],
        'cep' => $_POST['cep'],
        'endereco' => $_POST['endereco'],
        'numero' => $_POST['numero'],
        'bairro' => $_POST['bairro'],
        'cidade' => $_POST['cidade'],
        'estado' => $_POST['estado'],
        'status' => $_POST['status']
    ];



    $returnUpdate = $controller->editar($id, $dados);

    if ($returnUpdate) {
        echo notify('success', "Loja atualizada com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao atualizar a loja.");
    }
}

?>

<div class="card">

    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Editar Loja</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$id}" ?>" class="needs-validation" novalidate>

            <div class="row g-3">
                <div class="col-lg-4">
                    <label for="" class="form-label">Loja</label>
                    <input type="text" class="form-control" name="nome" value="<?= $return['nome'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">CNPJ</label>
                    <input type="text" class="form-control" name="cnpj" value="<?= $return['cnpj'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Responsavel</label>
                    <input type="text" class="form-control" name="responsavel" value="<?= $return['responsavel'] ?>" required>
                </div>


                <div class="col-lg-4">
                    <label for="" class="form-label">CPF</label>
                    <input type="text" class="form-control" name="cpf" value="<?= $return['cpf'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep" value="<?= $return['cep'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" value="<?= $return['endereco'] ?>" required>
                </div>
                <div class="col-lg-2">
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
                    <label for="" class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        <option value="1" <?= $return['status'] == 'Administrador' ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= $return['status'] == 'Administrador' ? 'selected' : '' ?>>Inativo</option>
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