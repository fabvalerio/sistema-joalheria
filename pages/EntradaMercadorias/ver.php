<?php

use App\Models\EntradaMercadorias\Controller;

$id = $link['3']; // Captura o ID da entrada de mercadoria

// Buscar os dados da entrada de mercadoria
$controller = new Controller();
$entrada = $controller->ver($id);

// Verificar se a entrada foi encontrada
if (!$entrada) {
    echo notify('danger', "Entrada de mercadoria não encontrada.");
    exit;
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Detalhes da Entrada de Mercadoria</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Nota Fiscal</label>
                <?php echo htmlspecialchars($entrada['nf_fiscal']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Data do Pedido</label>
                <?php echo $entrada['data_pedido']; ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Fornecedor</label>
                <?php echo htmlspecialchars($entrada['fornecedor_nome']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Data Prevista de Entrega</label>
                <?php echo $entrada['data_prevista_entrega']; ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Data de Entrega</label>
                <?php echo $entrada['data_entregue'] ? $entrada['data_entregue'] : 'Não entregue'; ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Transportadora</label>
                <?php echo htmlspecialchars($entrada['transportadora']); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Valor</label>
                R$ <?php echo number_format($entrada['valor'], 2, ',', '.'); ?>
            </div>
            <div class="col-lg-6">
                <label for="" class="form-label d-block fw-bold">Observações</label>
                <?php echo htmlspecialchars($entrada['observacoes']); ?>
            </div>
        </div>

        <!-- Produtos da entrada -->
        <div class="mt-4">
            <h4 class="text-primary">Produtos</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nome do Produto</th>
                        <th>Quantidade</th>
                        <th>Estoque Atual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($entrada['produtos'])): ?>
                        <?php foreach ($entrada['produtos'] as $produto): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($produto['nome_produto']); ?></td>
                                <td><?php echo $produto['quantidade']; ?></td>
                                <td><?php echo $produto['estoque']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Nenhum produto registrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
