<?php

use App\Models\Cartoes\Controller;

$controller = new Controller();
$cartoes = $controller->listar();

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cartões</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/cadastro" ?>" class="btn btn-white text-primary">Adicionar</a>
    </div>

    <div class="card-body">
        <table id="example1" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Cartão</th>
                    <th>Bandeira</th>
                    <th>Tipo</th>
                    <th>Taxa Administradora (%)</th>
                    <th>Máximo de Parcelas</th>
                    <th width="220">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartoes as $cartao): ?>
                    <tr>
                        <td><?= htmlspecialchars($cartao['id']) ?></td>
                        <td><?= htmlspecialchars($cartao['nome_cartao']) ?></td>
                        <td><?= htmlspecialchars($cartao['bandeira']) ?></td>
                        <td><?= htmlspecialchars($cartao['tipo']) ?></td>
                        <td><?= htmlspecialchars($cartao['taxa_administradora']) ?></td>
                        <td><?= htmlspecialchars($cartao['max_parcelas']) ?></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ação
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="<?= "{$url}!/{$link[1]}/ver/{$cartao['id']}" ?>" class="dropdown-item">Ver</a></li>
                                    <li><a href="<?= "{$url}!/{$link[1]}/editar/{$cartao['id']}" ?>" class="dropdown-item">Editar</a></li>
                                    <li><a href="<?= "{$url}!/{$link[1]}/deletar/{$cartao['id']}" ?>" class="dropdown-item text-danger">Excluir</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
