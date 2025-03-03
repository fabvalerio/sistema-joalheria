<?php

use App\Models\Fornecedores\Controller;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'razao_social' => $_POST['razao_social'] ?? '',
        'nome_fantasia' => $_POST['nome_fantasia'] ?? '',
        'cnpj' => $_POST['cnpj'] ?? '',
        'insc_estadual' => $_POST['insc_estadual'] ?? '',
        'insc_municipal' => $_POST['insc_municipal'] ?? '',
        'condicao_pagto' => $_POST['condicao_pagto'] ?? '',
        'vigencia_acordo' => $_POST['vigencia_acordo'] ?? '',
        'telefone' => $_POST['telefone'] ?? '',
        'email' => $_POST['email'] ?? '',
        'endereco' => $_POST['endereco'] ?? '',
        'cidade' => $_POST['cidade'] ?? '',
        'estado' => $_POST['estado'] ?? '',
        'contato' => $_POST['contato'] ?? '',
        'site' => $_POST['site'] ?? '',
        'banco' => $_POST['banco'] ?? '',
        'numero_banco' => $_POST['numero_banco'] ?? '',
        'agencia' => $_POST['agencia'] ?? '',
        'conta' => $_POST['conta'] ?? '',
        'pix' => $_POST['pix'] ?? '',
        'cep' => $_POST['cep'] ?? '',
        'whatsapp' => $_POST['whatsapp'] ?? '',
        'numero' => $_POST['numero'] ?? '',
        'complemento' => $_POST['complemento'] ?? '',
        'bairro' => $_POST['bairro'] ?? ''
    ];
    
    print_r($dados);

    $controller = new Controller();
    $return = $controller->cadastro($dados);

    if ($return) {
        echo notify('success', "Fornecedor cadastrado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao cadastrar o fornecedor.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro de Fornecedor</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-lg-6">
                    <label for="" class="form-label">Razão Social</label>
                    <input type="text" class="form-control" name="razao_social" required>
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Nome Fantasia</label>
                    <input type="text" class="form-control" name="nome_fantasia" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">CNPJ</label>
                    <input type="text" class="form-control" name="cnpj" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Inscrição Estadual</label>
                    <input type="text" class="form-control" name="insc_estadual">
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Inscrição Municipal</label>
                    <input type="text" class="form-control" name="insc_municipal">
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Condição de Pagamento</label>
                    <input type="text" class="form-control" name="condicao_pagto">
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Vigência do Acordo</label>
                    <input type="date" class="form-control" name="vigencia_acordo">
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Telefone</label>
                    <input type="text" class="form-control" name="telefone">
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">WhatsApp</label>
                    <input type="text" class="form-control" name="whatsapp">
                </div>

                <div class="col-lg-4">
                    <label for="" class="form-label">E-mail</label>
                    <input type="email" class="form-control" name="email">
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" required>
                </div>
                <div class="col-lg-2">
                    <label for="" class="form-label">Numero</label>
                    <input type="text" class="form-control" id="numero" name="numero" required>
                </div>
                <div class="col-lg-2">
                    <label for="" class="form-label">Complemento</label>
                    <input type="text" class="form-control" id="complemento" name="complemento" required>
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
                    <label for="" class="form-label">Contato</label>
                    <input type="text" class="form-control" name="contato">
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Site</label>
                    <input type="text" class="form-control" name="site">
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Banco</label>
                    <input type="text" class="form-control" name="banco">
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Número do Banco</label>
                    <input type="text" class="form-control" name="numero_banco">
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Agência</label>
                    <input type="text" class="form-control" name="agencia">
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Conta</label>
                    <input type="text" class="form-control" name="conta">
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Chave PIX</label>
                    <input type="text" class="form-control" name="pix">
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
