<?php

use App\Models\GrupoClientes\Controller;

$controller = new Controller();
$grupoClientes = $controller->listar();

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Grupos de Clientes</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/cadastro" ?>" class="btn btn-white text-primary">Adicionar</a>
    </div>

    <div class="card-body">
        <table id="example1" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Grupo</th>
                    <th>Comissão dos Vendedores (%)</th>
                    <th width="220">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grupoClientes as $grupo): ?>
                    <tr>
                        <td><?= $grupo['id'] ?></td>
                        <td><?= htmlspecialchars($grupo['nome_grupo']) ?></td>
                        <td><?= htmlspecialchars(number_format($grupo['comissao_vendedores'], 2, ',', '.')) ?></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ação
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="<?= "{$url}!/{$link[1]}/ver/{$grupo['id']}" ?>" class="dropdown-item">Ver</a></li>
                                    <li><a href="<?= "{$url}!/{$link[1]}/editar/{$grupo['id']}" ?>" class="dropdown-item">Editar</a></li>
                                    <li><a href="<?= "{$url}!/{$link[1]}/deletar/{$grupo['id']}" ?>" class="dropdown-item text-danger">Excluir</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
