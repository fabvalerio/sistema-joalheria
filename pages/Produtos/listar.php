<?php

use App\Models\Produtos\Controller;
use App\Models\Material\Controller as MaterialController;

$controller = new Controller();
$produtos = $controller->listar();

$materialController = new MaterialController();

?>

<!-- Custom styles for this page -->
<link href="<?php echo $url?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

<!-- Page level plugins -->
<script src="<?php echo $url?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo $url?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
    $('#example1').DataTable({
        "order": [[0, "desc"]], // Ordenar pela coluna ID em ordem decrescente
        "stateSave": true, // Salvar estado da tabela (página, pesquisa, ordenação)
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

<style>
    /* Container para o zoom da imagem */
    #image-zoom-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }

    #image-zoom-overlay.active {
        display: flex;
    }

    #image-zoom-container {
        position: relative;
        max-width: 90%;
        max-height: 90%;
    }

    #image-zoom-overlay img {
        max-width: 100%;
        max-height: 90vh;
        object-fit: contain;
        border-radius: 10px;
        box-shadow: 0 0 30px rgba(255, 255, 255, 0.3);
        animation: zoomIn 0.3s ease-out;
    }

    #close-zoom-btn {
        position: absolute;
        top: -15px;
        right: -15px;
        background-color: #fff;
        color: #333;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        transition: all 0.2s ease;
        z-index: 10000;
    }

    #close-zoom-btn:hover {
        background-color: #f44336;
        color: #fff;
        transform: scale(1.1);
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.5);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .image-capa {
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .image-capa:hover {
        transform: scale(1.05);
    }
</style>

<script>
    $(document).ready(function() {
        // Criar o overlay para o zoom se não existir
        if ($('#image-zoom-overlay').length === 0) {
            $('body').append(`
                <div id="image-zoom-overlay">
                    <div id="image-zoom-container">
                        <button id="close-zoom-btn" title="Fechar">&times;</button>
                        <img src="" alt="Zoom da Imagem">
                    </div>
                </div>
            `);
        }

        // Abrir zoom ao clicar na imagem
        $(document).on('click', '.image-capa', function() {
            const imgSrc = $(this).attr('src');
            $('#image-zoom-overlay img').attr('src', imgSrc);
            $('#image-zoom-overlay').addClass('active');
        });

        // Fechar zoom ao clicar no botão X
        $(document).on('click', '#close-zoom-btn', function(e) {
            e.stopPropagation();
            $('#image-zoom-overlay').removeClass('active');
        });

        // Fechar zoom ao clicar fora da imagem (no fundo escuro)
        $(document).on('click', '#image-zoom-overlay', function(e) {
            if (e.target === this) {
                $(this).removeClass('active');
            }
        });

        // Fechar zoom ao pressionar ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#image-zoom-overlay').hasClass('active')) {
                $('#image-zoom-overlay').removeClass('active');
            }
        });
    });
</script>


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
                    <th>Material</th>
                    <th>Modelo</th>
                    <th>Preço (R$)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr class="align-middle">
                        <td><?= str_pad($produto['id'], 5, '0', STR_PAD_LEFT) ?></td>
                        <td>
                            <img
                                src="<?= isset($produto['capa']) && !empty($produto['capa']) ? htmlspecialchars($produto['capa']) : $url . '/assets/img_padrao.webp'; ?>"
                                alt="Capa do Produto"
                                class="image-capa"
                                width="100"
                                style="height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 5px;">
                        </td>
                        <td><?= htmlspecialchars($produto['descricao_etiqueta']) ?></td>
                        <td><?= htmlspecialchars($produto['fornecedor'] ?? 'Não informado') ?></td>
                        <td><?= htmlspecialchars($produto['grupo'] ?? 'Não informado') ?></td>
                        <td><?= htmlspecialchars($produto['subgrupo'] ?? 'Não informado') ?></td>
                        <td>
                            <?php
                            $materiais = $materialController->ver($produto['material_id']);
                            echo htmlspecialchars($materiais['nome'] ?? 'Não informado'); 
                            ?>
                        </td>
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
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ação
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="<?= "{$url}!/{$link[1]}/ver/{$produto['id']}" ?>" class="dropdown-item">Ver</a></li>
                                    <li><a href="<?= "{$url}!/{$link[1]}/clone/{$produto['id']}" ?>" class="dropdown-item">Clonar</a></li>
                                    <li><a href="<?= "{$url}!/{$link[1]}/etiqueta/{$produto['id']}" ?>" class="dropdown-item">Imprimir Etiqueta Código</a></li>
                                    <li><a href="<?= "{$url}!/{$link[1]}/editar/{$produto['id']}" ?>" class="dropdown-item">Editar</a></li>
                                    <li><a href="<?= "{$url}!/{$link[1]}/deletar/{$produto['id']}" ?>" class="dropdown-item text-danger">Excluir</a></li>

                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>