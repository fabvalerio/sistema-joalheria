
<?php

use App\Models\Feriados\ControllerFeriados;

$controller = new ControllerFeriados();
$feriados = $controller->listar();

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Feriados</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/cadastro" ?>" class="btn btn-white text-primary">Adicionar</a>
    </div>

    <div class="card-body">
        <table id="example1" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Tipo</th>
                    <th>Facultativo</th>
                    <th width="220">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feriados as $feriado): ?>
                    <tr>
                        <td><?= $feriado['id'] ?></td>
                        <td><?= date('d/m/Y', strtotime($feriado['data_feriado'])) ?></td>
                        <td><?= htmlspecialchars($feriado['descricao']) ?></td>
                        <td><?= htmlspecialchars($feriado['tipo']) ?></td>
                        <td><span class="badge bg-<?= $feriado['facultativo'] == 'S' ? 'success' : 'danger' ?>"><?= htmlspecialchars($feriado['facultativo'] == 'S' ? 'Sim' : 'Não') ?></span></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ação
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="<?= "{$url}!/{$link[1]}/ver/{$feriado['id']}" ?>" class="dropdown-item">Ver</a></li>
                                    <li><a href="<?= "{$url}!/{$link[1]}/editar/{$feriado['id']}" ?>" class="dropdown-item">Editar</a></li>
                                    <li><a href="<?= "{$url}!/{$link[1]}/deletar/{$feriado['id']}" ?>" class="dropdown-item text-danger">Excluir</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
