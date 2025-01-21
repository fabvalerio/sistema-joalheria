<?php

use App\Models\ComissaoVendedor\Controller;

// Instanciar o Controller
$controller = new Controller();

// Obter a lista de comissões
$comissoes = $controller->listar();

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Lista de Comissões de Vendedores</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/cadastro" ?>" class="btn btn-white text-primary">Adicionar</a>
    </div>

    <div class="card-body">
        <table id="example1" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vendedor</th>
                    <th>Grupo de Produtos</th>
                    <th>Comissão A</th>
                    <th>Comissão B</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comissoes as $comissao): ?>
                    <tr>
                        <td><?= $comissao['id'] ?></td>
                        <td><?= htmlspecialchars($comissao['vendedor']) ?></td>
                        <td><?= htmlspecialchars($comissao['grupo_produto']) ?></td>
                        <td><?= htmlspecialchars(number_format($comissao['comissao_v'], 2, ',', '.')) ?>%</td>
                        <td><?= htmlspecialchars(number_format($comissao['comissao_a'], 2, ',', '.')) ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
