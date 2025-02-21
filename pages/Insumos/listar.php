<?php

use App\Models\Insumos\Controller;

$controller = new Controller();
$produtos = $controller->listar();

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Produtos</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/cadastro"; ?>" class="btn btn-white text-primary">Adicionar</a>
    </div>

    <div class="card-body">
        <table id="example1" class="table table-striped table-hover">
            <thead class="bg-light">
                <tr>
                    <th>ID</th>
                    <th>Capa</th>
                    <th>Descrição</th>
                    <th>Fornecedor</th>
                    <th>Grupo</th>
                    <th>Subgrupo</th>
                    <th>Modelo</th>
                    <th>Preço (R$)</th>
                    <th>Estoque</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr class="align-middle">
                        <td><?= htmlspecialchars($produto['id']) ?></td>
                        <td>
                            <img
                                src="<?= isset($produto['capa']) && !empty($produto['capa']) ? htmlspecialchars($produto['capa']) : $url . '/assets/img_padrao.webp'; ?>"
                                alt="Capa do Produto"
                                width="100"
                                style="height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 5px;">
                        </td>
                        <td><?= htmlspecialchars($produto['descricao_etiqueta']) ?></td>
                        <td><?= htmlspecialchars($produto['fornecedor'] ?? 'Não informado') ?></td>
                        <td><?= htmlspecialchars($produto['grupo'] ?? 'Não informado') ?></td>
                        <td><?= htmlspecialchars($produto['subgrupo'] ?? 'Não informado') ?></td>
                        <td><?= htmlspecialchars($produto['modelo'] ?? 'Não informado') ?></td>
                        <td>
                            R$<?= isset($produto['em_reais']) && $produto['em_reais'] !== null
                                    ? number_format($produto['em_reais'], 2, ',', '.')
                                    : '0,00'; ?>
                        </td>
                        <td><span class="badge bg-<?= $produto['estoque_princ'] > $produto['estoque_min'] ? 'success' : 'danger' ?>" style="font-size: medium;"><?= isset($produto['estoque_princ']) && $produto['estoque_princ'] !== null
                                ? $produto['estoque_princ']
                                : 0; ?></span></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ação
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="<?= "{$url}!/{$link[1]}/ver/{$produto['id']}" ?>" class="dropdown-item">Ver</a></li>
                                    <li>
                                        <a href="<?= "{$url}!/{$link[1]}/editar/{$produto['id']}" ?>" class="dropdown-item">Editar</a>
                                    </li>
                                    <li>
                                        <a href="<?= "{$url}!/{$link[1]}/deletar/{$produto['id']}" ?>" class="dropdown-item text-danger">Excluir</a>
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