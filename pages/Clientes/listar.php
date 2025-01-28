<?php

use App\Models\Clientes\Controller;

$controller = new Controller();
$clientes = $controller->listar();

?>

<div class="card">
<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Clientes</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/cadastro" ?>" class="btn btn-white text-primary">Adicionar</a>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Grupo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= $cliente['id'] ?></td>
                        <td><?= htmlspecialchars($cliente['nome_pf']) ?: htmlspecialchars($cliente['razao_social_pj']) ?></td>
                        <td><?= htmlspecialchars($cliente['telefone']) ?></td>
                        <td><?= htmlspecialchars($cliente['grupo']) ?></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ação
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="<?= "{$url}!/{$link[1]}/ver/{$cliente['id']}" ?>" class="dropdown-item">Ver</a></li>
                                    <li>
                                        <a href="<?= "{$url}!/{$link[1]}/editar/{$cliente['id']}" ?>" class="dropdown-item">Editar</a>
                                    </li>
                                    <li>
                                        <a href="<?= "{$url}!/{$link[1]}/deletar/{$cliente['id']}" ?>" class="dropdown-item text-danger">Excluir</a>
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
