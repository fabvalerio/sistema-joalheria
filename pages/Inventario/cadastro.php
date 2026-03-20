<?php

//erro de php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//fim erro de php

use App\Models\Inventario\Controller as InventarioController;
use App\Models\Estoque\Controller as EstoqueController;

$controller = new InventarioController();
$estoqueCtrl = new EstoqueController();

$isAdmin = isset($_COOKIE['nivel_acesso']) && $_COOKIE['nivel_acesso'] === 'Administrador';
$usuario_loja_id = $_COOKIE['loja_id'] ?? '';
$lojas = $estoqueCtrl->listarLojas($isAdmin ? '' : $usuario_loja_id);

$motivos = [
    'Produto com defeito' => 'Produto com defeito',
    'Cliente não gostou' => 'Cliente não gostou',
    'Outro' => 'Outro'
];
?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="fas fa-undo me-2"></i>Registro de Inventário</h3>
        <a href="<?= $url ?>!/Inventario/listar" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <p class="text-muted mb-4">
            Registre entrada ou saída de produto no estoque. <strong>Entrada</strong> = produto retorna ao estoque; <strong>Saída</strong> = produto sai do estoque.
        </p>

        <form method="POST" action="<?= $url ?>!/Inventario/devolucao_salvar" class="needs-validation" novalidate id="formDevolucao">
            <div class="row g-3">
                <div class="col-lg-4">
                    <label class="form-label fw-bold">Tipo de Movimentação <span class="text-danger">*</span></label>
                    <select class="form-select" name="tipo" id="tipo" required>
                        <option value="Entrada">Entrada (produto retorna ao estoque)</option>
                        <option value="Saida">Saída (produto sai do estoque)</option>
                    </select>
                </div>
                <div class="col-lg-6">
                    <label class="form-label fw-bold">Produto <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="text" class="form-control" id="buscaProduto" placeholder="Digite código ou nome do produto..." autocomplete="off" required>
                        <input type="hidden" name="produto_id" id="produto_id" required>
                        <div id="produtos-dropdown" class="list-group position-absolute w-100 shadow" style="z-index: 1050; display: none; max-height: 250px; overflow-y: auto;"></div>
                    </div>
                    <div id="produto-selecionado" class="mt-2 text-success fw-bold" style="display: none;"></div>
                </div>

                <div class="col-lg-3">
                    <label class="form-label fw-bold">Quantidade <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="quantidade" id="quantidade" min="0.01" step="0.01" value="1" required>
                </div>

                <div class="col-lg-3">
                    <label class="form-label fw-bold">Número da Venda (opcional)</label>
                    <input type="number" class="form-control" name="pedido_id" id="pedido_id" placeholder="Ex: 12345" min="0">
                </div>

                <div class="col-lg-6">
                    <label class="form-label fw-bold">Motivo <span class="text-danger">*</span></label>
                    <select class="form-select" name="motivo_select" id="motivo_select" required>
                        <option value="">Selecione o motivo</option>
                        <?php foreach ($motivos as $k => $v): ?>
                            <option value="<?= htmlspecialchars($v) ?>"><?= htmlspecialchars($v) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div id="motivo_outro_wrap" class="mt-2" style="display: none;">
                        <input type="text" class="form-control" id="motivo_outro" placeholder="Descreva o motivo...">
                    </div>
                    <input type="hidden" name="motivo" id="motivo" required>
                </div>

                <div class="col-lg-6">
                    <label class="form-label fw-bold" id="labelEstoque">Estoque <span class="text-danger">*</span></label>
                    <select class="form-select" name="loja_id" id="loja_id" required>
                        <option value="">Selecione o estoque (CD ou Loja)</option>
                        <?php foreach ($lojas as $loja): ?>
                            <option value="<?= $loja['id'] ?>"><?= htmlspecialchars($loja['nome']) ?> (<?= $loja['tipo'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <hr>
                    <button type="submit" class="btn btn-primary btn-lg" id="btnSalvar">
                        <i class="fas fa-save me-2"></i>Registrar
                    </button>
                    <a href="<?= $url ?>!/Inventario/listar" class="btn btn-outline-secondary btn-lg ms-2">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    const buscaProduto = $('#buscaProduto');
    const produtoId = $('#produto_id');
    const produtoSelecionado = $('#produto-selecionado');
    const produtosDropdown = $('#produtos-dropdown');
    const endpointBusca = '<?= $url ?>pages/Inventario/buscar_produtos.php';
    const motivoSelect = $('#motivo_select');
    const motivoOutroWrap = $('#motivo_outro_wrap');
    const motivoOutro = $('#motivo_outro');
    const motivoHidden = $('#motivo');
    const form = $('#formDevolucao');
    let searchTimeout = null;

    motivoSelect.on('change', function() {
        const val = $(this).val();
        motivoOutroWrap.toggle(val === 'Outro');
        if (val !== 'Outro') {
            motivoHidden.val(val);
            motivoOutro.val('');
        } else {
            motivoHidden.val(motivoOutro.val());
        }
    });

    motivoOutro.on('input', function() {
        if (motivoSelect.val() === 'Outro') {
            motivoHidden.val($(this).val());
        }
    });

    form.on('submit', function() {
        if (motivoSelect.val() === 'Outro' && !motivoOutro.val().trim()) {
            alert('Informe o motivo quando selecionar "Outro".');
            motivoOutro.focus();
            return false;
        }
        if (motivoSelect.val() !== 'Outro') {
            motivoHidden.val(motivoSelect.val());
        }
        if (!produtoId.val()) {
            alert('Selecione um produto.');
            buscaProduto.focus();
            return false;
        }
        return true;
    });

    buscaProduto.on('focus', function() {
        if ($(this).val().trim().length >= 1) {
            buscarProdutos($(this).val().trim());
        }
    }).on('input', function() {
        if (!produtoId.val()) {
            produtoSelecionado.hide();
        }
        const q = $(this).val().trim();
        clearTimeout(searchTimeout);
        if (q.length < 1) {
            produtosDropdown.hide().empty();
            return;
        }
        searchTimeout = setTimeout(function() {
            buscarProdutos(q);
        }, 300);
    }).on('blur', function() {
        setTimeout(function() {
            produtosDropdown.hide();
        }, 200);
    });

    function buscarProdutos(termo) {
        $.get(endpointBusca, { busca: termo }, function(res) {
            const data = res.data || [];
            produtosDropdown.empty();
            if (data.length === 0) {
                produtosDropdown.append('<div class="list-group-item text-muted">Nenhum produto encontrado</div>');
            } else {
                data.forEach(function(p) {
                    const item = $('<a href="#" class="list-group-item list-group-item-action">' +
                        '<strong>' + (p.codigo_formatado || p.id) + '</strong> - ' + (p.nome_produto || '-') + '</a>');
                    item.on('click', function(e) {
                        e.preventDefault();
                        produtoId.val(p.id);
                        buscaProduto.val(p.codigo_formatado + ' - ' + (p.nome_produto || ''));
                        produtoSelecionado.text('Produto selecionado: ' + (p.nome_produto || p.id)).show();
                        produtosDropdown.hide().empty();
                    });
                    produtosDropdown.append(item);
                });
            }
            produtosDropdown.show();
        }, 'json');
    }

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#buscaProduto, #produtos-dropdown').length) {
            produtosDropdown.hide();
        }
    });
});
</script>
