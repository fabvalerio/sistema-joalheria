<?php

use App\Models\Fornecedores\Controller;

$id = $link['3'];

$controller = new Controller();
$fornecedor = $controller->ver($id);

if (!$fornecedor) {
    echo notify('danger', "Fornecedor não encontrado.");
    exit;
}

if (isset($link['4']) && $link['4'] == 'deletar') {
    $return = $controller->deletar($id);

    if ($return) {
        echo notify('success', "Fornecedor deletado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao deletar o fornecedor.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Fornecedor</h3>
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
        </div>
        <div class="mt-3">
            <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$id}/deletar" ?>">Deletar</a>
        </div>
    </div>
</div>
