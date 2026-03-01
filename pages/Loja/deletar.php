<?php

use App\Models\Loja\Controller;

// ID para deletar
$id = $link['3'];

// Exibir os detalhes do registro para confirmar a exclusão
$controller = new Controller();
$return = $controller->ver($id);

// Verificar se o registro foi encontrado
if (!$return) {
    echo notify('danger', "Loja não encontrada.");
    exit;
}

// CD id=2 não pode ser excluído
$podeExcluir = ((int)$id !== 2);

// Verificar se o comando para deletar foi enviado
if ($podeExcluir && isset($link['4']) && $link['4'] == 'deletar') {
    $deletar = new Controller();
    $resultado = $deletar->deletar($id);

    if ($resultado) {
        echo notify('success', "Loja deletada com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao deletar loja. Verifique se não há transferências ou outras dependências.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Loja</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <?php if (!$podeExcluir): ?>
            <div class="alert alert-warning">
                <strong>Atenção:</strong> O CD principal (id=2) não pode ser excluído.
            </div>
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Loja</label>
                <?php echo htmlspecialchars($return['nome'] ?? ''); ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">CNPJ</label>
                <?php echo htmlspecialchars($return['cnpj'] ?? ''); ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Responsável</label>
                <?php echo htmlspecialchars($return['responsavel'] ?? ''); ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Tipo</label>
                <?php echo htmlspecialchars($return['tipo'] ?? ''); ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Cidade</label>
                <?php echo htmlspecialchars($return['cidade'] ?? ''); ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Estado</label>
                <?php echo htmlspecialchars($return['estado'] ?? ''); ?>
            </div>
        </div>
        <div class="mt-3">
            <?php if ($podeExcluir): ?>
                <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar"; ?>">Deletar</a>
            <?php else: ?>
                <button class="btn btn-secondary" disabled>Exclusão não permitida</button>
            <?php endif; ?>
        </div>
    </div>
</div>
