<?php

use App\Models\Clientes\Controller;

$id = $link['3']; // ID do cliente a ser visualizado

// Buscar os dados do cliente
$controller = new Controller();
$cliente = $controller->ver($id);

// Obter os grupos de clientes
$grupos = $controller->listarGrupos();
$grupoNome = null;

// Verificar se o cliente foi encontrado
if (!$cliente) {
    echo notify('danger', "Cliente não encontrado.");
    exit;
}

// Identificar o nome do grupo
foreach ($grupos as $grupo) {
    if ($grupo['id'] == $cliente['grupo']) {
        $grupoNome = $grupo['nome_grupo'];
        break;
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Detalhes do Cliente</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Tipo de Cliente</label>
                <?php echo $cliente['tipo_cliente'] == 'PF' ? 'Pessoa Física' : 'Pessoa Jurídica'; ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Nome</label>
                <?php echo htmlspecialchars($cliente['nome_pf']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Razão Social PJ</label>
                <?php echo htmlspecialchars($cliente['razao_social_pj']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Nome Fantasia PJ</label>
                <?php echo htmlspecialchars($cliente['nome_fantasia_pj']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Perfil</label>
                <?php echo htmlspecialchars($cliente['perfil']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Telefone</label>
                <?php echo htmlspecialchars($cliente['telefone']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">WhatsApp</label>
                <?php echo htmlspecialchars($cliente['whatsapp']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">E-mail</label>
                <?php echo htmlspecialchars($cliente['email']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">RG</label>
                <?php echo htmlspecialchars($cliente['rg']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">CPF</label>
                <?php echo htmlspecialchars($cliente['cpf']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">IE PJ</label>
                <?php echo htmlspecialchars($cliente['ie_pj']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">CNPJ PJ</label>
                <?php echo htmlspecialchars($cliente['cnpj_pj']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">CEP</label>
                <?php echo htmlspecialchars($cliente['cep']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Endereço</label>
                <?php echo htmlspecialchars($cliente['endereco']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Bairro</label>
                <?php echo htmlspecialchars($cliente['bairro']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Cidade</label>
                <?php echo htmlspecialchars($cliente['cidade']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Estado</label>
                <?php echo htmlspecialchars($cliente['estado']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Data de Nascimento</label>
                <?php echo $cliente['data_nascimento']; ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Tags</label>
                <?php echo htmlspecialchars($cliente['tags']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Origem do Contato</label>
                <?php echo htmlspecialchars($cliente['origem_contato']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Estado Civil</label>
                <?php echo htmlspecialchars($cliente['estado_civil']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Corporativo</label>
                <?php echo $cliente['corporativo'] == 'S' ? 'Sim' : 'Não'; ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Grupo</label>
                <?php echo htmlspecialchars($grupoNome); ?>
            </div>
        </div>
    </div>
</div>
