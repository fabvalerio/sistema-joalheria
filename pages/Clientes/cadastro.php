<?php

use App\Models\Clientes\Controller;

$controller = new Controller();
$grupos = $controller->listarGrupos();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'tipo_cliente' => $_POST['tipo_cliente'], // Adicionado para identificar se é PF ou PJ
        'nome_pf' => $_POST['nome_pf'],
        'razao_social_pj' => $_POST['razao_social_pj'],
        'nome_fantasia_pj' => $_POST['nome_fantasia_pj'],
        'perfil' => $_POST['perfil'],
        'telefone' => $_POST['telefone'],
        'whatsapp' => $_POST['whatsapp'],
        'email' => $_POST['email'],
        'rg' => $_POST['rg'],
        'cpf' => $_POST['cpf'],
        'ie_pj' => $_POST['ie_pj'],
        'cnpj_pj' => $_POST['cnpj_pj'],
        'cep' => $_POST['cep'],
        'endereco' => $_POST['endereco'],
        'bairro' => $_POST['bairro'],
        'cidade' => $_POST['cidade'],
        'estado' => $_POST['estado'],
        'data_nascimento' => $_POST['data_nascimento'],
        'tags' => $_POST['tags'],
        'origem_contato' => $_POST['origem_contato'],
        'estado_civil' => $_POST['estado_civil'],
        'corporativo' => $_POST['corporativo'],
        'grupo' => $_POST['grupo'],
    ];

    $return = $controller->cadastro($dados);

    if ($return) {
        echo notify('success', "Cliente cadastrado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao cadastrar o cliente.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro de Cliente</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <!-- Seleção de Pessoa Física ou Jurídica -->
                <div class="col-lg-6">
                    <label for="" class="form-label">Tipo de Cliente</label>
                    <select class="form-select" id="tipo_cliente" name="tipo_cliente" required>
                        <option value="">Selecione</option>
                        <option value="PF">Pessoa Física</option>
                        <option value="PJ">Pessoa Jurídica</option>
                    </select>
                </div>

                <!-- Campos para Pessoa Física -->
                <div class="col-lg-6 pessoa-fisica">
                    <label for="" class="form-label">Nome</label>
                    <input type="text" class="form-control" name="nome_pf">
                </div>
                <div class="col-lg-6 pessoa-fisica">
                    <label for="" class="form-label">RG</label>
                    <input type="text" class="form-control" name="rg">
                </div>
                <div class="col-lg-6 pessoa-fisica">
                    <label for="" class="form-label">CPF</label>
                    <input type="text" class="form-control" name="cpf">
                </div>
                <div class="col-lg-6 pessoa-fisica">
                    <label for="" class="form-label">Data de Nascimento</label>
                    <input type="date" class="form-control" name="data_nascimento">
                </div>
                <div class="col-lg-6 pessoa-fisica">
                    <label for="" class="form-label">Estado Civil</label>
                    <input type="text" class="form-control" name="estado_civil">
                </div>

                <!-- Campos para Pessoa Jurídica -->
                <div class="col-lg-6 pessoa-juridica">
                    <label for="" class="form-label">Razão Social PJ</label>
                    <input type="text" class="form-control" name="razao_social_pj">
                </div>
                <div class="col-lg-6 pessoa-juridica">
                    <label for="" class="form-label">Nome Fantasia PJ</label>
                    <input type="text" class="form-control" name="nome_fantasia_pj">
                </div>
                <div class="col-lg-6 pessoa-juridica">
                    <label for="" class="form-label">CNPJ PJ</label>
                    <input type="text" class="form-control" name="cnpj_pj">
                </div>
                <div class="col-lg-6 pessoa-juridica">
                    <label for="" class="form-label">IE PJ</label>
                    <input type="text" class="form-control" name="ie_pj">
                </div>

                <!-- Campos comuns -->
                <div class="col-lg-6">
                    <label for="" class="form-label">Perfil</label>
                    <input type="text" class="form-control" name="perfil">
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Telefone</label>
                    <input type="text" class="form-control" name="telefone">
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">WhatsApp</label>
                    <input type="text" class="form-control" name="whatsapp">
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">E-mail</label>
                    <input type="email" class="form-control" name="email">
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">CEP</label>
                    <input type="text" class="form-control" name="cep">
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Endereço</label>
                    <input type="text" class="form-control" name="endereco">
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Bairro</label>
                    <input type="text" class="form-control" name="bairro">
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Cidade</label>
                    <input type="text" class="form-control" name="cidade">
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Estado</label>
                    <input type="text" class="form-control" name="estado">
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Tags</label>
                    <input type="text" class="form-control" name="tags">
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Origem do Contato</label>
                    <input type="text" class="form-control" name="origem_contato">
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Corporativo</label>
                    <select class="form-select" name="corporativo">
                        <option value="">Selecione</option>
                        <option value="S">Sim</option>
                        <option value="N">Não</option>
                    </select>
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Grupo</label>
                    <select class="form-select" name="grupo">
                        <option value="">Selecione o Grupo</option>
                        <?php foreach ($grupos as $grupo) : ?>
                            <option value="<?= $grupo['id'] ?>"><?= htmlspecialchars($grupo['nome_grupo']) ?></option>
                        <?php endforeach; ?>
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
    document.addEventListener('DOMContentLoaded', () => {
        const tipoCliente = document.getElementById('tipo_cliente');
        const camposPF = document.querySelectorAll('.pessoa-fisica');
        const camposPJ = document.querySelectorAll('.pessoa-juridica');

        function toggleCampos() {
            const tipo = tipoCliente.value;
            camposPF.forEach(campo => campo.style.display = tipo === 'PF' ? '' : 'none');
            camposPJ.forEach(campo => campo.style.display = tipo === 'PJ' ? '' : 'none');
        }

        tipoCliente.addEventListener('change', toggleCampos);
        toggleCampos(); // Chamada inicial
    });
</script>
