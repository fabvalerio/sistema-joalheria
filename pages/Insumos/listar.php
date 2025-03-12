<?php

use App\Models\Insumos\Controller;

$controller = new Controller();
$produtos = $controller->listar();

?>


<!-- Custom styles for this page -->
<link href="<?php echo $url?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

<!-- Page level plugins -->
<script src="<?php echo $url?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo $url?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
    $('#example1').DataTable({
        "language": {
            "sEmptyTable": "Nenhum dado disponível na tabela",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ entradas",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 entradas",
            "sInfoFiltered": "(filtrado de _MAX_ entradas totais)",
            "sInfoPostFix": "",
            "sLengthMenu": "Mostrar _MENU_ entradas",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sSearch": "Pesquisar:",
            "sZeroRecords": "Nenhum registro encontrado",
            "oPaginate": {
                "sFirst": "Primeiro",
                "sPrevious": "Anterior",
                "sNext": "Próximo",
                "sLast": "Último"
            },
            "oAria": {
                "sSortAscending": ": ativar para ordenar a coluna de forma ascendente",
                "sSortDescending": ": ativar para ordenar a coluna de forma descendente"
            }
        }
    });
    });
</script>


<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Insumos</h3>
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
                        <?php
                            //conta de valor dinamica com cotação
                            $produto['em_reais'] =  cotacao($produto['preco_ql'], $produto['peso_gr'], $produto['cotacao_valor'], $produto['margem']);
                            ?>
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