<?php

use App\Models\CategoriaDespesa\Controller;

$controller = new Controller();
$categorias = $controller->listar();

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Categorias de Despesa</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/cadastro" ?>" class="btn btn-white text-primary">Adicionar</a>
    </div>

    <div class="card-body">
        <table id="example1" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descrição</th>
                    <th width="220">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><?= $categoria['id'] ?></td>
                        <td><?= htmlspecialchars($categoria['descricao']) ?></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ação
                                </button>
                                <ul class="dropdown-menu">
                                    <!-- <li><a href="<?= "{$url}!/{$link[1]}/ver/{$categoria['id']}" ?>" class="dropdown-item">Ver</a></li> -->
                                    <li><a href="<?= "{$url}!/{$link[1]}/editar/{$categoria['id']}" ?>" class="dropdown-item">Editar</a></li>
                                    <li><a href="<?= "{$url}!/{$link[1]}/deletar/{$categoria['id']}" ?>" class="dropdown-item text-danger">Excluir</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
