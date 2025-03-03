<?php

use App\Models\Fornecedores\Controller;

$id = $link['3'];

$controller = new Controller();
$fornecedor = $controller->ver($id);

if (!$fornecedor) {
    echo notify('danger', "Fornecedor não encontrado.");
    exit;
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Detalhes do Fornecedor</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-6">
                <label class="form-label fw-bold">Razão Social</label>
                <p><?= htmlspecialchars($fornecedor['razao_social']) ?></p>
            </div>
            <div class="col-lg-6">
                <label class="form-label fw-bold">Nome Fantasia</label>
                <p><?= htmlspecialchars($fornecedor['nome_fantasia']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">CNPJ</label>
                <p><?= htmlspecialchars($fornecedor['cnpj']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Inscrição Estadual</label>
                <p><?= htmlspecialchars($fornecedor['insc_estadual']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Inscrição Municipal</label>
                <p><?= htmlspecialchars($fornecedor['insc_municipal']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Condição de Pagamento</label>
                <p><?= htmlspecialchars($fornecedor['condicao_pagto']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Vigência do Acordo</label>
                <p><?= htmlspecialchars($fornecedor['vigencia_acordo']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Telefone</label>
                <p><?= htmlspecialchars($fornecedor['telefone']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">WhatsApp</label>
                <p><?= htmlspecialchars($fornecedor['whatsapp']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">E-mail</label>
                <p><?= htmlspecialchars($fornecedor['email']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Endereço</label>
                <p><?= htmlspecialchars($fornecedor['endereco']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Número</label>
                <p><?= htmlspecialchars($fornecedor['numero']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Complemento</label>
                <p><?= htmlspecialchars($fornecedor['complemento']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Cidade</label>
                <p><?= htmlspecialchars($fornecedor['cidade']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Estado</label>
                <p><?= htmlspecialchars($fornecedor['estado']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Contato</label>
                <p><?= htmlspecialchars($fornecedor['contato']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Site</label>
                <p><?= htmlspecialchars($fornecedor['site']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Banco</label>
                <p><?= htmlspecialchars($fornecedor['banco']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Número do Banco</label>
                <p><?= htmlspecialchars($fornecedor['numero_banco']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Agência</label>
                <p><?= htmlspecialchars($fornecedor['agencia']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Conta</label>
                <p><?= htmlspecialchars($fornecedor['conta']) ?></p>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Chave PIX</label>
                <p><?= htmlspecialchars($fornecedor['pix']) ?></p>
            </div>
        </div>
    </div>
</div>
