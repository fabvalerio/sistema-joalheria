<?php

use App\Models\Consignacao\Controller;

$controller = new Controller();
$id = $link[3] ?? null;

if (!$id) {
    echo notify('danger', 'ID da consignação não informado.');
    echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    exit;
}

$dados = $controller->ver($id);
$consignacao = $dados['consignacao'];
$itens = $dados['itens'];

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Detalhes da Consignação</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar"; ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <h4 class="card-title">Dados da Consignação</h4>
        <div class="row g-3">
            <div class="col-lg-6">
                <strong>Cliente:</strong> 
                <?= htmlspecialchars(
                    !empty($consignacao['nome_pf']) 
                    ? $consignacao['nome_pf'] 
                    : ($consignacao['nome_fantasia_pj'] ?? 'Não informado')
                ) ?>
            </div>
            <div class="col-lg-6">
                <strong>Data da Consignação:</strong> 
                <?= htmlspecialchars(date('d/m/Y', strtotime($consignacao['data_consignacao']))) ?>
            </div>
            <div class="col-lg-6">
                <strong>Status:</strong> 
                <span class="badge bg-<?= $consignacao['status'] == 'Aberta' ? 'warning' : 'success' ?>"><?= htmlspecialchars($consignacao['status'] ?? 'Aberta') ?> </span>
            </div>
            <div class="col-lg-6">
                <strong>Valor Total:</strong> 
                R$<?= isset($consignacao['valor']) 
                    ? number_format($consignacao['valor'], 2, ',', '.') 
                    : '0,00'; ?>
            </div>
            <div class="col-12">
                <strong>Observações:</strong>
                <p><?= htmlspecialchars($consignacao['observacao'] ?? 'Não informado') ?></p>
            </div>
        </div>

        <hr>
        <h4 class="card-title">Itens da Consignação</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Valor Unitário (R$)</th>
                    <th>Subtotal (R$)</th>
                    <th>Quantidade Devolvida</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome_produto'] ?? 'Produto não encontrado') ?></td>
                        <td><?= htmlspecialchars($item['quantidade']) ?></td>
                        <td>
                            R$<?= isset($item['valor']) 
                                ? number_format($item['valor'], 2, ',', '.') 
                                : '0,00'; ?>
                        </td>
                        <td>
                            R$<?= number_format($item['quantidade'] * $item['valor'], 2, ',', '.'); ?>
                        </td>
                        <td><?= htmlspecialchars($item['qtd_devolvido'] ?? '0') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
