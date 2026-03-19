<?php

use App\Models\Estoque\Controller;
use App\Models\EntradaMercadorias\Controller as EntradaMercadoriasController;

$controller = new Controller();

$isAdmin = isset($_COOKIE['nivel_acesso']) && $_COOKIE['nivel_acesso'] === 'Administrador';
$usuario_loja_id = $_COOKIE['loja_id'] ?? null;

$lojas = $controller->listarLojas($isAdmin ? null : $usuario_loja_id);
// Remove a loja do tipo CD do filtro desta tela (a tela de CD fica em outra rota).
$lojas = array_values(array_filter($lojas, function ($l) {
    return ($l['tipo'] ?? null) !== 'CD';
}));

$notasEntradas = [];
if ($isAdmin) {
    $entradaMercadoriasController = new EntradaMercadoriasController();
    $notasEntradas = $entradaMercadoriasController->listar();
}

$loja_id_filtro = $_GET['loja_id'] ?? null;
if (!$isAdmin && $usuario_loja_id) {
    $loja_id_filtro = $usuario_loja_id;
}

// Para Admin: sem parametro de filtro => assume a primeira loja física (sem CD).
// Isso evita cair no modo "todas as lojas" do backend (quando loja_id fica vazio).
if ($isAdmin && ($loja_id_filtro === null || $loja_id_filtro === '')) {
    $loja_id_filtro = !empty($lojas) ? ($lojas[0]['id'] ?? null) : null;
}

// Se Admin passar manualmente um id que não está na lista (ex: CD), força um valor válido.
if ($isAdmin && ($loja_id_filtro !== null && $loja_id_filtro !== '')) {
    $idsValidos = array_map(static fn($l) => (string)($l['id'] ?? ''), $lojas);
    if (!in_array((string)$loja_id_filtro, $idsValidos, true)) {
        $loja_id_filtro = !empty($lojas) ? ($lojas[0]['id'] ?? null) : $loja_id_filtro;
    }
}

$mostrarColunaLoja = ($loja_id_filtro === null || $loja_id_filtro === '');
?>

<link href="<?= $url ?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="<?= $url ?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $url ?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <i class="fas fa-boxes me-2"></i>Estoque por Loja
        </h3>
        <?php if ($isAdmin && count($lojas) >= 2): ?>
        <div class="d-flex align-items-center gap-2">
            <label class="text-white mb-0">Filtrar loja:</label>
            <select id="filtroLoja" class="form-select form-select-sm" style="width:auto">
                <?php foreach ($lojas as $l): ?>
                    <option value="<?= $l['id'] ?>" <?= (string)$loja_id_filtro === (string)$l['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($l['nome']) ?> (<?= $l['tipo'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <?php if (!$isAdmin): ?>
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-1"></i>
            Você está visualizando o estoque da sua loja: <strong><?= htmlspecialchars($_COOKIE['loja_nome'] ?? 'Sua Loja') ?></strong>
        </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table id="tabelaEstoque" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Loja</th>
                        <th>Código</th>
                        <th>Produto</th>
                        <th class="text-center">Quantidade</th>
                        <th class="text-center">Qtd Mínima</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Editar / Adicionar -->
<div class="modal fade" id="modalEstoque" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEstoqueTitulo">Editar Estoque</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalProdutoId">
                <input type="hidden" id="modalLojaId">
                <input type="hidden" id="modalAcao">
                <p class="mb-2"><strong>Loja:</strong> <span id="modalLojaNome"></span></p>
                <p class="mb-2"><strong>Produto:</strong> <span id="modalProdutoNome"></span></p>
                <p class="mb-2"><strong>Código:</strong> <code id="modalProdutoCodigo"></code></p>
                <p class="mb-3" id="modalQtdAtualWrap"><strong>Quantidade atual:</strong> <span id="modalQtdAtual"></span></p>

                <div class="mb-3" id="modalEntradaMercadoriasWrap" style="display:none;">
                    <label class="form-label fw-bold" for="modalEntradaMercadoriasId">Nota de Entrada</label>
                    <select id="modalEntradaMercadoriasId" class="form-select">
                        <option value="">Sem nota</option>
                        <?php foreach ($notasEntradas as $nota): ?>
                            <?php
                                $nf = $nota['nf_fiscal'] ?? '-';
                                $fornecedorNome = $nota['fornecedor_nome'] ?? 'Não informado';
                                $dataPedido = $nota['data_pedido'] ?? null;
                                $dataPedidoFmt = $dataPedido ? date('d/m/Y', strtotime($dataPedido)) : '-';
                                $idNota = (int)($nota['id'] ?? 0);
                            ?>
                            <option value="<?= $idNota ?>">
                                <?= htmlspecialchars($nf) ?> - <?= htmlspecialchars($fornecedorNome) ?> (<?= htmlspecialchars($dataPedidoFmt) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Opcional: vincula essa entrada à nota fiscal selecionada (CD).</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold" id="modalLabelQuantidade">Quantidade a adicionar</label>
                    <input type="number" id="modalQuantidade" value="0" class="form-control" min="0" step="1">
                    <div class="form-text" id="modalDicaQuantidade">Digite quantas unidades deseja adicionar ao estoque atual.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold" for="modalQuantidadeMinima">Quantidade mínima</label>
                    <input type="number" value="0" id="modalQuantidadeMinima" class="form-control" min="0" step="1">
                    <div class="form-text">Alerta quando o estoque atingir ou ficar abaixo deste valor.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarEstoque">
                    <i class="fas fa-save me-1"></i>Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var baseUrl = '<?= $url ?>pages/Estoque/json.php';
    var lojaIdInicial = <?= json_encode($loja_id_filtro) ?>;

    var table = $('#tabelaEstoque').DataTable({
        ajax: {
            url: baseUrl,
            type: 'POST',
            dataSrc: 'data',
            data: function(d) {
                d.loja_id = $('#filtroLoja').length ? ($('#filtroLoja').val() || '') : (lojaIdInicial ?? '');
            }
        },
        columns: [
            { data: 'loja_nome' },
            { data: 'codigo_formatado', render: function(v) { return '<code>' + (v || '') + '</code>'; } },
            { data: 'nome_produto' },
            { data: 'quantidade_formatada', className: 'text-center', render: function(v) { return '<span class="badge bg-info">' + (v || '0') + '</span>'; } },
            { data: 'quantidade_minima_formatada', className: 'text-center' },
            { data: 'status', className: 'text-center', render: function(v) {
                return '<span class="badge bg-' + (v === 'Baixo' ? 'danger' : 'success') + '">' + (v || 'OK') + '</span>';
            }},
            { data: null, className: 'text-center', orderable: false, render: function(d) {
                var lojaId = d.loja_id != null ? d.loja_id : 0;
                var podeEditar = true;
                if (lojaId === 0 && !<?= json_encode($isAdmin) ?>) { podeEditar = false; }
                if (!podeEditar) return '-';
                var qtdMin = d.quantidade_minima != null ? d.quantidade_minima : '';
                var qtd = parseFloat(d.quantidade) || 0;
                var temEstoque = qtd > 0;
                var btn = '';
                if (temEstoque) {
                    btn = '<button type="button" class="btn btn-outline-primary btn-editar btn-sm" title="Editar quantidade" data-id="' + d.produto_id + '" data-loja-id="' + lojaId + '" data-loja-nome="' + (d.loja_nome || '').replace(/"/g, '&quot;') + '" data-nome="' + (d.nome_produto || '').replace(/"/g, '&quot;') + '" data-codigo="' + (d.codigo_formatado || '') + '" data-qtd="' + d.quantidade + '" data-qtd-min="' + qtdMin + '"><i class="fas fa-edit"></i> Editar</button>';
                } else {
                    btn = '<button type="button" class="btn btn-outline-success btn-adicionar btn-sm" title="Primeira entrada no estoque" data-id="' + d.produto_id + '" data-loja-id="' + lojaId + '" data-loja-nome="' + (d.loja_nome || '').replace(/"/g, '&quot;') + '" data-nome="' + (d.nome_produto || '').replace(/"/g, '&quot;') + '" data-codigo="' + (d.codigo_formatado || '') + '" data-qtd="0" data-qtd-min="' + qtdMin + '"><i class="fas fa-plus"></i> Adicionar</button>';
                }
                return '<div class="btn-group btn-group-sm">' + btn + '</div>';
            }}
        ],
        columnDefs: [
            { targets: 0, visible: <?= json_encode($mostrarColunaLoja) ?> }
        ],
        order: [[1, 'asc']],
        language: {
            sEmptyTable: "Nenhum dado disponível na tabela",
            sInfo: "Mostrando de _START_ até _END_ de _TOTAL_ entradas",
            sInfoEmpty: "Mostrando 0 até 0 de 0 entradas",
            sInfoFiltered: "(filtrado de _MAX_ entradas totais)",
            sLengthMenu: "Mostrar _MENU_ entradas",
            sLoadingRecords: "Carregando...",
            sProcessing: "Processando...",
            sSearch: "Pesquisar:",
            sZeroRecords: "Nenhum registro encontrado",
            oPaginate: {
                sFirst: "Primeiro",
                sPrevious: "Anterior",
                sNext: "Próximo",
                sLast: "Último"
            }
        }
    });

    $('#filtroLoja').on('change', function() {
        var val = $(this).val();
        table.column(0).visible(!val);
        table.ajax.reload();
        var params = new URLSearchParams(window.location.search);
        params.set('page', '!/Estoque/listar');
        params.set('loja_id', val);
        if (history.replaceState) {
            history.replaceState(null, '', window.location.pathname + '?' + params.toString());
        }
    });

    var baseUrlSave = '<?= $url ?>pages/Estoque/estoque_salvar.php';
    var modal = new bootstrap.Modal(document.getElementById('modalEstoque'));

    function abrirModal(acao, produtoId, lojaId, lojaNome, nomeProduto, codigo, qtdAtual, qtdMinima) {
        $('#modalProdutoId').val(produtoId);
        $('#modalLojaId').val(lojaId);
        $('#modalAcao').val(acao);
        $('#modalLojaNome').text(lojaNome || '-');
        $('#modalProdutoNome').text(nomeProduto || '-');
        $('#modalProdutoCodigo').text(codigo || '');
        $('#modalQtdAtual').text(qtdAtual || '0');
        $('#modalQuantidade').val('').attr('min', 1);
        $('#modalEntradaMercadoriasId').val('');
        if (parseInt(lojaId, 10) === 0) {
            $('#modalEntradaMercadoriasWrap').show();
        } else {
            $('#modalEntradaMercadoriasWrap').hide();
        }
        $('#modalQuantidadeMinima').val(qtdMinima !== '' && qtdMinima !== undefined && qtdMinima !== null ? qtdMinima : '');
        if (acao === 'editar') {
            $('#modalEstoqueTitulo').text('Editar Quantidade');
        } else {
            $('#modalEstoqueTitulo').text('Adicionar ao Estoque');
        }
        $('#modalLabelQuantidade').text('Quantidade a adicionar');
        $('#modalDicaQuantidade').text('Digite quantas unidades deseja adicionar ao estoque atual.');
        $('#modalQtdAtualWrap').show();
        modal.show();
        setTimeout(function() { $('#modalQuantidade').focus(); }, 300);
    }

    $(document).on('click', '.btn-editar', function() {
        var $el = $(this);
        abrirModal('editar', $el.data('id'), $el.data('loja-id'), $el.data('loja-nome'), $el.data('nome'), $el.data('codigo'), $el.data('qtd'), $el.data('qtd-min'));
    });

    $(document).on('click', '.btn-adicionar', function() {
        var $el = $(this);
        abrirModal('adicionar', $el.data('id'), $el.data('loja-id'), $el.data('loja-nome'), $el.data('nome'), $el.data('codigo'), $el.data('qtd'), $el.data('qtd-min'));
    });

    $('#btnSalvarEstoque').on('click', function() {
        var acao = $('#modalAcao').val(), produtoId = $('#modalProdutoId').val(), lojaId = $('#modalLojaId').val(), qtd = $('#modalQuantidade').val();
        if (!produtoId) return;
        qtd = parseFloat(String(qtd).replace(',', '.')) || 0;
        if ((acao === 'editar' || acao === 'adicionar') && qtd <= 0) {
            alert('Informe uma quantidade maior que zero para adicionar.');
            return;
        }
        var qtdMin = $('#modalQuantidadeMinima').val();
        qtdMin = qtdMin !== '' ? (parseFloat(String(qtdMin).replace(',', '.')) || 0) : null;

        var $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
        var entradaMercadoriasId = $('#modalEntradaMercadoriasId').val();
        $.post(baseUrlSave, {
            acao: acao,
            produto_id: produtoId,
            loja_id: lojaId,
            quantidade: qtd,
            quantidade_minima: qtdMin,
            descricao_produto: $('#modalProdutoNome').text(),
            entrada_mercadorias_id: entradaMercadoriasId
        })
        .done(function(r) {
            try { r = typeof r === 'string' ? JSON.parse(r) : r; } catch(e) { r = { ok: false, msg: 'Resposta inválida' }; }
            if (r.ok) {
                modal.hide();
                table.ajax.reload(null, false);
                alert(r.msg);
            } else {
                alert(r.msg || 'Erro ao salvar.');
            }
        })
        .fail(function(xhr) {
            var msg = 'Erro na requisição.';
            try {
                if (xhr.responseText) {
                    var r = typeof xhr.responseText === 'string' ? JSON.parse(xhr.responseText) : xhr.responseText;
                    if (r && r.msg) msg = r.msg;
                }
            } catch(e) {}
            alert(msg);
        })
        .always(function() {
            $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar');
        });
    });
});
</script>
