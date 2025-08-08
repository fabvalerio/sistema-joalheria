<?php

use App\Models\Loja\Controller;
// Obter os cargos
// Instanciar o Controller de Usuários
$controller = new Controller();


// Verificar se o formulário foi enviado
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
    // Verifica se há permissões enviadas
    if (isset($_POST['permissoes'])) {
        $permissoesUsuario = [];
        foreach ($_POST['permissoes'] as $dir => $perms) {
            $permissoesUsuario[$dir] = [
                "visualizar" => isset($perms['visualizar']),
                "manipular" => isset($perms['manipular'])
            ];
        }
        // Converte as permissões para JSON e adiciona no array de dados
        $dados['permissoes'] = json_encode($permissoesUsuario);
    }
    // Debug para verificar o conteúdo do array
    // echo '<pre>';
    // echo htmlspecialchars(json_encode($permissoesUsuario, JSON_PRETTY_PRINT));
    // echo '</pre>';
    // exit;

    $controller = new Controller();
    $return = $controller->cadastro($dados);

    if ($return) {
        echo notify('success', "Cadastrado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao cadastrar.");
    }
}

?>

<div class="card">

    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro de Loja</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>

            <div class="row g-3">
                <div class="col-lg-4">
                    <label for="" class="form-label">Loja</label>
                    <input type="text" class="form-control" name="nome" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">CNPJ</label>
                    <input type="text" class="form-control" name="cnpj" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Responsavel</label>
                    <input type="text" class="form-control" name="responsavel" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">CPF</label>
                    <input type="text" class="form-control" id="cpf" name="cpf" required>
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
                    <label for="" class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        <option select disabled>Selecione o Status</option>
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
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