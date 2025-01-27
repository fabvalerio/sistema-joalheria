<?php

use App\Models\Consignacao\Controller;

$id = $link['3']; // ID da consignação a ser deletada

// Buscar os dados da consignação para exibição
$controller = new Controller();
$return = $controller->ver($id);

// Verificar se a consignação foi encontrada
if (!$return || empty($return['consignacao'])) {
    echo notify('danger', "Consignação não encontrada ou inválida.");
    exit;
}

// Deletar o registro se o comando for confirmado
if (isset($link['4']) && $link['4'] == 'deletar') {
    $deletar = new Controller();
    $result = $deletar->deletar($id);

    if ($result) {
        echo notify('success', "Consignação deletada com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao deletar a consignação. Verifique as dependências.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Consignação</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <h4 class="card-title">Dados da Consignação</h4>
        <div class="row g-3">
            <div class="col-lg-6">
                <label class="form-label d-block fw-bold">Cliente:</label>
                <?= htmlspecialchars(
                    !empty($return['consignacao']['nome_pf'])
                        ? $return['consignacao']['nome_pf']
                        : ($return['consignacao']['nome_fantasia_pj'] ?? 'Não informado')
                ) ?>
            </div>
            <div class="col-lg-6">
                <label class="form-label d-block fw-bold">Data da Consignação:</label>
                <?= htmlspecialchars(date('d/m/Y', strtotime($return['consignacao']['data_consignacao']))) ?>
            </div>
            <div class="col-lg-6">
                <label class="form-label d-block fw-bold">Valor Total:</label>
                R$<?= number_format($return['consignacao']['valor'], 2, ',', '.') ?>
            </div>
            <div class="col-lg-6">
                <label class="form-label d-block fw-bold">Status:</label>
                <?= htmlspecialchars($return['consignacao']['status']) ?>
            </div>
        </div>
        
        <hr>
        <h4 class="card-title">Itens da Consignação</h4>
        <?php if (!empty($return['itens'])): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Quantidade Devolvida</th>
                        <th>Valor Unitário (R$)</th>
                        <th>Subtotal (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($return['itens'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['nome_produto'] ?? 'Produto não encontrado') ?></td>
                            <td><?= htmlspecialchars($item['quantidade'] ?? '0') ?></td>
                            <td><?= htmlspecialchars($item['qtd_devolvido'] ?? '0') ?></td>
                            <td>R$<?= number_format($item['valor'], 2, ',', '.') ?></td>
                            <td>R$<?= number_format(($item['quantidade'] ?? 0) * ($item['valor'] ?? 0), 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-danger">Nenhum item encontrado para esta consignação.</p>
        <?php endif; ?>

        <div class="mt-3">
            <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar"; ?>">Deletar</a>
        </div>
    </div>
</div>
