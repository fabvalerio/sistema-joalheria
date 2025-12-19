<?php

use App\Models\ImpressaoEtiquetas\Controller;

$controller = new Controller();

// Pegar parâmetros da URL
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : '';
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

// Listar produtos com paginação
$resultado = $controller->listar($filtro, $pagina, 100);
$produtos = $resultado['registros'];
$paginaAtual = $resultado['paginaAtual'];
$totalPaginas = $resultado['totalPaginas'];
$totalRegistros = $resultado['totalRegistros'];

?>

<style>
    .table-checkbox-col {
        width: 50px;
        text-align: center;
    }
    .btn-imprimir-etiquetas {
        position: sticky;
        bottom: 20px;
        z-index: 1000;
    }
    
    /* Estilos da paginação */
    .pagination {
        margin-top: 20px;
        margin-bottom: 20px;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
        font-weight: bold;
    }
    
    .pagination .page-item.disabled .page-link {
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .pagination .page-link {
        padding: 8px 12px;
        margin: 0 2px;
        border-radius: 4px;
        color: #007bff;
        transition: all 0.3s;
    }
    
    .pagination .page-link:hover:not(.disabled) {
        background-color: #e9ecef;
        color: #0056b3;
    }
    
    .pagination .page-link i {
        font-size: 12px;
    }
</style>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Lista de Produtos para Impressão de Etiquetas</h3>
        <span class="badge bg-light text-primary">Total: <?= $totalRegistros ?> produtos</span>
    </div>

    <div class="card-body">
        <!-- Filtro de produtos -->
        <form method="GET" action="" class="mb-3" id="form-filtro">
            <div class="row">
                <div class="col-md-10">
                    <input type="text" name="filtro" id="input-filtro" class="form-control" 
                           placeholder="Pesquisar por descrição do produto..." 
                           value="<?= htmlspecialchars($filtro) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
            <?php if (!empty($filtro)): ?>
                <div class="mt-2">
                    <a href="<?= $url ?>!/ImpressaoEtiquetas/listar" class="btn btn-sm btn-secondary">
                        <i class="fas fa-times"></i> Limpar Filtro
                    </a>
                </div>
            <?php endif; ?>
        </form>

        <!-- Informações e botões de ação -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <span class="text-muted">Página <?= $paginaAtual ?> de <?= $totalPaginas ?></span>
                <span class="ms-3 badge bg-info" id="contador-selecionados">0 selecionado(s)</span>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-secondary" id="btn-selecionar-todos">
                    <i class="fas fa-check-square"></i> Selecionar Todos desta Página
                </button>
                <button type="button" class="btn btn-sm btn-warning" id="btn-limpar-selecao">
                    <i class="fas fa-eraser"></i> Limpar Seleção
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-primary">
                    <tr>
                        <th class="table-checkbox-col">
                            <input type="checkbox" id="checkbox-master" title="Selecionar todos desta página">
                        </th>
                        <th>Código</th>
                        <th>Produto</th>
                        <th style="width: 100px; text-align: center;">Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produtos)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>Nenhum produto encontrado</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td class="table-checkbox-col">
                                    <input type="checkbox" 
                                           class="checkbox-produto" 
                                           name="produto_id[]" 
                                           value="<?= $produto['id'] ?>"
                                           data-descricao="<?= htmlspecialchars($produto['descricao_etiqueta']) ?>">
                                </td>
                                <td><?= htmlspecialchars($produto['id']) ?></td>
                                <td><?= htmlspecialchars($produto['descricao_etiqueta']) ?></td>
                                <td style="text-align: center;">
                                    <input type="number" 
                                           class="form-control form-control-sm quantidade-input" 
                                           data-id="<?= $produto['id'] ?>" 
                                           value="1" 
                                           min="1" 
                                           max="999" 
                                           style="width: 70px; display: inline-block;"
                                           disabled>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <?php if ($totalPaginas > 1): ?>
            <nav aria-label="Paginação" class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Primeira página -->
                    <li class="page-item <?= $paginaAtual == 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $url ?>!/ImpressaoEtiquetas/listar&filtro=<?= urlencode($filtro) ?>&pagina=1" tabindex="-1">
                            <i class="fas fa-angle-double-left"></i> Primeira
                        </a>
                    </li>
                    
                    <!-- Página anterior -->
                    <li class="page-item <?= $paginaAtual == 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $url ?>!/ImpressaoEtiquetas/listar&filtro=<?= urlencode($filtro) ?>&pagina=<?= max(1, $paginaAtual - 1) ?>" tabindex="-1">
                            <i class="fas fa-angle-left"></i> Anterior
                        </a>
                    </li>
                    
                    <!-- Páginas numeradas -->
                    <?php
                    $intervalo = 2;
                    $inicio = max(1, $paginaAtual - $intervalo);
                    $fim = min($totalPaginas, $paginaAtual + $intervalo);
                    
                    // Mostrar "..." se houver páginas antes
                    if ($inicio > 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif;
                    
                    for ($i = $inicio; $i <= $fim; $i++):
                    ?>
                        <li class="page-item <?= $i == $paginaAtual ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $url ?>!/ImpressaoEtiquetas/listar&filtro=<?= urlencode($filtro) ?>&pagina=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor;
                    
                    // Mostrar "..." se houver páginas depois
                    if ($fim < $totalPaginas): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Próxima página -->
                    <li class="page-item <?= $paginaAtual >= $totalPaginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $url ?>!/ImpressaoEtiquetas/listar&filtro=<?= urlencode($filtro) ?>&pagina=<?= min($totalPaginas, $paginaAtual + 1) ?>">
                            Próxima <i class="fas fa-angle-right"></i>
                        </a>
                    </li>
                    
                    <!-- Última página -->
                    <li class="page-item <?= $paginaAtual >= $totalPaginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $url ?>!/ImpressaoEtiquetas/listar&filtro=<?= urlencode($filtro) ?>&pagina=<?= $totalPaginas ?>">
                            Última <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php elseif ($totalRegistros > 0): ?>
            <div class="text-center text-muted mt-3">
                <small>Mostrando todos os <?= $totalRegistros ?> produto(s) em uma única página</small>
            </div>
        <?php endif; ?>
    </div>

    <!-- Botão flutuante para imprimir -->
    <div class="card-footer text-center btn-imprimir-etiquetas">
        <button type="button" class="btn btn-success btn-lg" id="btn-imprimir" disabled>
            <i class="fas fa-print"></i> Visualizar e Imprimir Etiquetas (<span id="contador-btn">0</span>)
        </button>
    </div>
</div>

<script>
// Interceptar submit do formulário de filtro
$('#form-filtro').on('submit', function(e) {
    e.preventDefault();
    const filtro = $('#input-filtro').val();
    const paginaAtual = <?= $pagina ?>;
    
    // Construir URL no formato correto
    let url = '<?= $url ?>!/ImpressaoEtiquetas/listar';
    
    // Adicionar filtro se houver valor
    if (filtro.trim() !== '') {
        url += '&filtro=' + encodeURIComponent(filtro);
    }
    
    // Adicionar página se não for a primeira
    if (paginaAtual > 1) {
        url += '&pagina=' + paginaAtual;
    }
    
    window.location.href = url;
});

// Sistema de persistência de seleção usando localStorage
const STORAGE_KEY = 'etiquetas_selecionadas';

// Carregar seleções do localStorage
function carregarSelecoes() {
    const selecoes = localStorage.getItem(STORAGE_KEY);
    return selecoes ? JSON.parse(selecoes) : {};
}

// Salvar seleções no localStorage
function salvarSelecoes(selecoes) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(selecoes));
}

// Atualizar contador
function atualizarContador() {
    const selecoes = carregarSelecoes();
    const totalProdutos = Object.keys(selecoes).length;
    let totalEtiquetas = 0;
    
    // Calcular total de etiquetas
    Object.values(selecoes).forEach(item => {
        totalEtiquetas += item.quantidade || 1;
    });
    
    $('#contador-selecionados').text(totalProdutos + ' produto(s), ' + totalEtiquetas + ' etiqueta(s)');
    $('#contador-btn').text(totalEtiquetas);
    
    // Habilitar/desabilitar botão de impressão
    $('#btn-imprimir').prop('disabled', totalProdutos === 0);
}

// Restaurar estado dos checkboxes ao carregar página
function restaurarEstado() {
    const selecoes = carregarSelecoes();
    
    $('.checkbox-produto').each(function() {
        const id = $(this).val();
        if (selecoes[id]) {
            $(this).prop('checked', true);
            const $quantInput = $('.quantidade-input[data-id="' + id + '"]');
            $quantInput.prop('disabled', false);
            $quantInput.val(selecoes[id].quantidade || 1);
        }
    });
    
    atualizarContador();
}

// Ao marcar/desmarcar checkbox
$(document).on('change', '.checkbox-produto', function() {
    const selecoes = carregarSelecoes();
    const id = $(this).val();
    const descricao = $(this).data('descricao');
    const $quantInput = $('.quantidade-input[data-id="' + id + '"]');
    
    if ($(this).is(':checked')) {
        const quantidade = parseInt($quantInput.val()) || 1;
        selecoes[id] = {
            descricao: descricao,
            quantidade: quantidade
        };
        $quantInput.prop('disabled', false);
    } else {
        delete selecoes[id];
        $quantInput.prop('disabled', true);
    }
    
    salvarSelecoes(selecoes);
    atualizarContador();
});

// Ao alterar quantidade
$(document).on('change', '.quantidade-input', function() {
    const id = $(this).data('id');
    const selecoes = carregarSelecoes();
    
    if (selecoes[id]) {
        selecoes[id].quantidade = parseInt($(this).val()) || 1;
        salvarSelecoes(selecoes);
        atualizarContador();
    }
});

// Checkbox master (selecionar todos da página)
$('#checkbox-master').on('change', function() {
    const isChecked = $(this).is(':checked');
    $('.checkbox-produto').prop('checked', isChecked).trigger('change');
});

// Botão selecionar todos da página
$('#btn-selecionar-todos').on('click', function() {
    $('.checkbox-produto').prop('checked', true).trigger('change');
    $('#checkbox-master').prop('checked', true);
});

// Botão limpar seleção (todas as páginas)
$('#btn-limpar-selecao').on('click', function() {
    if (confirm('Deseja limpar TODAS as seleções (de todas as páginas)?')) {
        localStorage.removeItem(STORAGE_KEY);
        $('.checkbox-produto').prop('checked', false);
        $('#checkbox-master').prop('checked', false);
        atualizarContador();
        location.reload();
    }
});

// Botão para visualizar e imprimir
$('#btn-imprimir').on('click', function() {
    const selecoes = carregarSelecoes();
    const ids = Object.keys(selecoes);
    
    if (ids.length === 0) {
        alert('Nenhum produto selecionado!');
        return;
    }
    
    // Construir array com formato id:quantidade
    const idsComQuantidade = ids.map(id => {
        const quantidade = selecoes[id].quantidade || 1;
        return id + ':' + quantidade;
    });
    
    // Redirecionar para página de visualização
    window.location.href = '<?= $url ?>!/<?= $link[1] ?>/visualizar&ids=' + idsComQuantidade.join(',');
});

// Restaurar estado ao carregar página
$(document).ready(function() {
    restaurarEstado();
});
</script>
