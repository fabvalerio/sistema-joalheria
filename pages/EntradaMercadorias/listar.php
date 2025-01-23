<?php

use App\Models\EntradaMercadorias\Controller;

$controller = new Controller();
$entradas = $controller->listar();

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Entradas de Mercadorias</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/cadastro"; ?>" class="btn btn-white text-primary">Adicionar</a>
    </div>

    <div class="card-body">
        <table id="example1" class="table table-striped table-hover">
            <thead class="bg-light">
                <tr>
                    <th>ID</th>
                    <th>Nota Fiscal</th>
                    <th>Fornecedor</th>
                    <th>Data do Pedido</th>
                    <th>Data da Entrega</th>
                    <th>Valor (R$)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entradas as $entrada): ?>
                    <tr>
                        <td><?= htmlspecialchars($entrada['id']) ?></td>
                        <td><?= htmlspecialchars($entrada['nf_fiscal']) ?></td>
                        <td><?= htmlspecialchars($entrada['fornecedor_nome'] ?? 'Não informado') ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($entrada['data_pedido']))) ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($entrada['data_prevista_entrega']))) ?></td>
                        <td>
                            R$<?= isset($entrada['valor']) && $entrada['valor'] !== null
                                    ? number_format($entrada['valor'], 2, ',', '.')
                                    : '0,00'; ?>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ação
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?= "{$url}!/{$link[1]}/ver/{$entrada['id']}" ?>" class="dropdown-item">Ver</a>
                                    </li>
                                    <li>
                                        <a href="<?= "{$url}!/{$link[1]}/editar/{$entrada['id']}" ?>" class="dropdown-item">Editar</a>
                                    </li>
                                    <li>
                                        <a href="<?= "{$url}!/{$link[1]}/deletar/{$entrada['id']}" ?>" class="dropdown-item text-danger">Excluir</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
