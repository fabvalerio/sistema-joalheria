<?php

use App\Models\TransferenciaEstoque\Controller;

$controller = new Controller();
$lojas = $controller->listarLojas();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loja_origem_id = $_POST['loja_origem_id'] ?? null;
    $loja_destino_id = $_POST['loja_destino_id'] ?? null;

    if ($loja_origem_id == $loja_destino_id) {
        echo notify('danger', "Origem e destino não podem ser iguais.");
    } else {
        $sucesso = true;
        if (!empty($_POST['produtos'])) {
            foreach ($_POST['produtos'] as $produto) {
                if (empty($produto['id']) || empty($produto['quantidade']) || $produto['quantidade'] <= 0) {
                    continue;
                }

                $dados = [
                    'produto_id' => (int)$produto['id'],
                    'loja_origem_id' => (int)$loja_origem_id,
                    'loja_destino_id' => (int)$loja_destino_id,
                    'quantidade' => (float)$produto['quantidade'],
                    'usuario_id' => $_COOKIE['id'] ?? null,
                    'observacao' => $_POST['observacao'] ?? null,
                    'descricao_produto' => $produto['nome_produto'] ?? '',
                    'loja_origem_nome' => $_POST['loja_origem_nome'] ?? '',
                    'loja_destino_nome' => $_POST['loja_destino_nome'] ?? ''
                ];

                $result = $controller->cadastro($dados);
                if (!$result) {
                    $sucesso = false;
                    break;
                }
            }
        }

        if ($sucesso) {
            echo notify('success', "Transferência realizada com sucesso!");
            echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/TransferenciaEstoque/listar">';
        } else {
            echo notify('danger', "Erro ao realizar a transferência.");
        }
    }
}

?>

<link href="<?= $url ?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="<?= $url ?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $url ?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Nova Transferência de Estoque</h3>
        <a href="<?php echo "{$url}!/TransferenciaEstoque/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/TransferenciaEstoque/cadastro" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-lg-5">
                    <label class="form-label fw-bold">Loja de Origem</label>
                    <select class="form-select" name="loja_origem_id" id="loja_origem_id" required>
                        <option value="">Selecione a origem</option>
                        <?php foreach ($lojas as $loja): ?>
                            <option value="<?= $loja['id'] ?>"><?= htmlspecialchars($loja['nome']) ?> (<?= $loja['tipo'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="loja_origem_nome" id="loja_origem_nome">
                </div>
                <div class="col-lg-2 d-flex align-items-end justify-content-center">
                    <i class="fas fa-arrow-right fa-2x text-primary"></i>
                </div>
                <div class="col-lg-5">
                    <label class="form-label fw-bold">Loja de Destino</label>
                    <select class="form-select" name="loja_destino_id" id="loja_destino_id" required>
                        <option value="">Selecione o destino</option>
                        <?php foreach ($lojas as $loja): ?>
                            <option value="<?= $loja['id'] ?>"><?= htmlspecialchars($loja['nome']) ?> (<?= $loja['tipo'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="loja_destino_nome" id="loja_destino_nome">
                </div>

                <!-- Card de Produtos Selecionados -->
                <div class="col-12">
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success text-white py-2">
                            <h6 class="mb-0"><i class="fas fa-shopping-cart me-1"></i>Produtos Selecionados</h6>
                        </div>
                        <div class="card-body p-2" style="max-height: 400px; overflow-y: auto;">
                            <div id="produtos-selecionados-lista">
                                <p class="text-muted small mb-0 py-3 text-center">Nenhum produto selecionado. Adicione produtos abaixo.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <hr>
                    <h5>Produtos para Transferir</h5>
                    <p class="text-muted small">Digite para pesquisar e clique em Adicionar para incluir na transferência.</p>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="buscaProduto" placeholder="Pesquisar por código ou nome do produto..." autocomplete="off" disabled>
                    </div>
                    <div id="produtos-container">
                        <div id="produtos-placeholder" class="alert alert-info">Selecione a loja de origem para carregar os produtos disponíveis.</div>
                        <div id="produtos-datatable-wrapper" style="display:none;">
                            <div class="table-responsive">
                                <table id="tabelaProdutos" class="table table-sm table-striped table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Produto</th>
                                            <th class="text-center">Estoque</th>
                                            <th class="text-center" style="width:110px">Qtd</th>
                                            <th class="text-center" style="width:80px">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Observação</label>
                    <textarea class="form-control" name="observacao" rows="2"></textarea>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary float-end" id="btnTransferir" disabled>Realizar Transferência</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    const lojaOrigemSelect = document.getElementById('loja_origem_id');
    const lojaDestinoSelect = document.getElementById('loja_destino_id');
    const produtosPlaceholder = document.getElementById('produtos-placeholder');
    const produtosDatatableWrapper = document.getElementById('produtos-datatable-wrapper');
    const produtosSelecionadosLista = document.getElementById('produtos-selecionados-lista');
    const buscaProdutoInput = document.getElementById('buscaProduto');
    const btnTransferir = document.getElementById('btnTransferir');
    const form = document.querySelector('form');
    const endpointUrl = '<?= $url ?>pages/TransferenciaEstoque/buscar_produtos.php';
    let searchTimeout = null;
    let tableProdutos = null;
    let selectedProducts = [];

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    function initDataTable() {
        if (tableProdutos) {
            tableProdutos.destroy();
            tableProdutos = null;
        }
        tableProdutos = $('#tabelaProdutos').DataTable({
            ajax: {
                url: endpointUrl,
                dataSrc: 'data',
                data: function() {
                    return {
                        loja_id: lojaOrigemSelect.value,
                        busca: buscaProdutoInput.value.trim()
                    };
                }
            },
            columns: [
                { data: 'id', render: function(v) { return '<code>' + v + '</code>'; } },
                { data: 'nome_produto' },
                { data: 'estoque', className: 'text-center', render: function(v) {
                    return '<span class="badge bg-info">' + (parseFloat(v) || 0) + '</span>';
                }},
                { data: null, className: 'text-center', orderable: false, render: function(row) {
                    const est = parseFloat(row.estoque) || 0;
                    return '<input type="number" step="0.01" min="0.01" max="' + est + '" class="form-control form-control-sm qtd-add" data-id="' + row.id + '" value="1" style="width:70px;display:inline-block;">';
                }},
                { data: null, className: 'text-center', orderable: false, render: function(row) {
                    const nome = escapeHtml(row.nome_produto);
                    const est = parseFloat(row.estoque) || 0;
                    return '<button type="button" class="btn btn-sm btn-success btn-adicionar-produto" data-id="' + row.id + '" data-nome="' + nome + '" data-estoque="' + est + '"><i class="fas fa-plus"></i></button>';
                }}
            ],
            order: [[1, 'asc']],
            language: {
                sEmptyTable: "Nenhum produto encontrado",
                sInfo: "Mostrando de _START_ até _END_ de _TOTAL_ entradas",
                sInfoEmpty: "Nenhum produto encontrado",
                sInfoFiltered: "(filtrado de _MAX_ entradas)",
                sLengthMenu: "Mostrar _MENU_",
                sLoadingRecords: "Carregando...",
                sProcessing: "Processando...",
                sSearch: "Pesquisar:",
                sZeroRecords: "Nenhum registro encontrado",
                oPaginate: { sFirst: "Primeiro", sPrevious: "Anterior", sNext: "Próximo", sLast: "Último" }
            }
        });
    }

    $(document).on('click', '.btn-adicionar-produto', function() {
        const id = $(this).data('id');
        const nome = $(this).data('nome');
        const estoque = parseFloat($(this).data('estoque')) || 0;
        let qtd = parseFloat($(this).closest('tr').find('.qtd-add').val()) || 1;
        if (qtd < 0.01) qtd = 1;
        if (qtd > estoque) qtd = estoque;
        addProduto(id, nome, estoque, qtd);
    });

    lojaOrigemSelect.addEventListener('change', function() {
        document.getElementById('loja_origem_nome').value = this.options[this.selectedIndex].text;
        selectedProducts = [];
        renderProdutosSelecionados();
        updateBtnTransferir();

        if (!this.value) {
            produtosPlaceholder.style.display = 'block';
            produtosPlaceholder.textContent = 'Selecione a loja de origem para carregar os produtos disponíveis.';
            produtosDatatableWrapper.style.display = 'none';
            buscaProdutoInput.disabled = true;
            if (tableProdutos) { tableProdutos.destroy(); tableProdutos = null; }
            return;
        }
        buscaProdutoInput.disabled = false;
        produtosPlaceholder.style.display = 'none';
        produtosDatatableWrapper.style.display = 'block';
        initDataTable();
    });

    lojaDestinoSelect.addEventListener('change', function() {
        document.getElementById('loja_destino_nome').value = this.options[this.selectedIndex].text;
    });

    buscaProdutoInput.addEventListener('input', function() {
        if (!lojaOrigemSelect.value) return;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            if (tableProdutos) tableProdutos.ajax.reload();
        }, 350);
    });

    function addProduto(id, nome, estoque, quantidade) {
        const idx = selectedProducts.findIndex(p => String(p.id) === String(id));
        if (idx >= 0) {
            const novaQtd = selectedProducts[idx].quantidade + quantidade;
            selectedProducts[idx].quantidade = Math.min(novaQtd, estoque);
        } else {
            selectedProducts.push({ id, nome_produto: nome, estoque, quantidade });
        }
        renderProdutosSelecionados();
        updateBtnTransferir();
    }

    function removeProduto(id) {
        selectedProducts = selectedProducts.filter(p => String(p.id) !== String(id));
        renderProdutosSelecionados();
        updateBtnTransferir();
    }

    function updateQtdProduto(id, novaQtd) {
        const p = selectedProducts.find(x => String(x.id) === String(id));
        if (!p) return;
        const estoque = parseFloat(p.estoque) || 0;
        p.quantidade = Math.max(0.01, Math.min(parseFloat(novaQtd) || 0, estoque));
        renderProdutosSelecionados();
    }

    function renderProdutosSelecionados() {
        if (selectedProducts.length === 0) {
            produtosSelecionadosLista.innerHTML = '<p class="text-muted small mb-0 py-3 text-center">Nenhum produto selecionado. Adicione produtos abaixo.</p>';
            return;
        }

        let html = '';
        selectedProducts.forEach((p, i) => {
            const estoque = parseFloat(p.estoque) || 0;
            html += `<div class="d-flex align-items-center justify-content-between border-bottom py-2 px-2" data-id="${p.id}">
                <div class="flex-grow-1 me-2 text-truncate" title="${escapeHtml(p.nome_produto)}">
                    <strong><code>${p.id}</code></strong> – ${escapeHtml(p.nome_produto)}
                </div>
                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                    <input type="number" step="0.01" min="0.01" max="${estoque}" class="form-control form-control-sm qtd-selecionado" 
                        data-id="${p.id}" value="${p.quantidade}" style="width:75px" title="Quantidade">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remover" data-id="${p.id}" title="Remover">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>`;
        });
        produtosSelecionadosLista.innerHTML = html;

        produtosSelecionadosLista.querySelectorAll('.qtd-selecionado').forEach(input => {
            input.addEventListener('change', function() {
                updateQtdProduto(this.dataset.id, this.value);
            });
        });

        produtosSelecionadosLista.querySelectorAll('.btn-remover').forEach(btn => {
            btn.addEventListener('click', function() {
                removeProduto(this.dataset.id);
            });
        });
    }

    function updateBtnTransferir() {
        btnTransferir.disabled = selectedProducts.length === 0;
    }

    form.addEventListener('submit', function(e) {
        document.querySelectorAll('input[name^="produtos["]').forEach(el => el.remove());
        let idx = 0;
        selectedProducts.forEach((p) => {
            if (!p.id || !p.quantidade || p.quantidade <= 0) return;
            ['id', 'nome_produto', 'quantidade'].forEach((key, k) => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'produtos[' + idx + '][' + key + ']';
                inp.value = key === 'quantidade' ? p.quantidade : (key === 'id' ? p.id : p.nome_produto);
                form.appendChild(inp);
            });
            idx++;
        });
    });
});
</script>
