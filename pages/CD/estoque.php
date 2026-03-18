<?php

// CD/estoque usa tabela estoque (estoque principal do CD)

?>

<link href="<?= $url ?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="<?= $url ?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $url ?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

<div class="card">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">
            <i class="fas fa-warehouse me-2"></i>Estoque do Centro de Distribuição
        </h3>
        <div class="d-flex gap-2">
            <a href="<?= $url ?>!/CD/movimentacoes" class="btn btn-info btn-sm">
                <i class="fas fa-exchange-alt me-1"></i>Movimentações
            </a>
            <a href="<?= $url ?>!/CD/transferir" class="btn btn-warning btn-sm">
                <i class="fas fa-truck-loading me-1"></i>Transferir para Lojas
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="tabelaEstoqueCD" class="table table-striped table-hover">
                <thead>
                    <tr>
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
                <input type="hidden" id="modalAcao">
                <p class="mb-2"><strong>Produto:</strong> <span id="modalProdutoNome"></span></p>
                <p class="mb-2"><strong>Código:</strong> <code id="modalProdutoCodigo"></code></p>
                <p class="mb-3" id="modalQtdAtualWrap"><strong>Quantidade atual:</strong> <span id="modalQtdAtual"></span></p>
                <div class="mb-3">
                    <label class="form-label fw-bold" id="modalLabelQuantidade">Nova quantidade</label>
                    <input type="number" id="modalQuantidade" value="0" class="form-control" min="0" step="1">
                    <div class="form-text" id="modalDicaQuantidade">Digite a quantidade final desejada no estoque.</div>
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
    var baseUrl = '<?= $url ?>pages/CD/estoque_json.php';

    var table = $('#tabelaEstoqueCD').DataTable({
        ajax: {
            url: baseUrl,
            dataSrc: 'data'
        },
        columns: [
            { data: 'codigo_formatado', render: function(v) { return '<code>' + (v || '') + '</code>'; } },
            { data: 'nome_produto' },
            { data: 'quantidade_formatada', className: 'text-center', render: function(v) { return '<span class="badge bg-info">' + (v || '0') + '</span>'; } },
            { data: 'quantidade_minima_formatada', className: 'text-center' },
            { data: 'status', className: 'text-center', render: function(v) {
                return '<span class="badge bg-' + (v === 'Baixo' ? 'danger' : 'success') + '">' + (v || 'OK') + '</span>';
            }},
            { data: null, className: 'text-center', orderable: false, render: function(d) {
                var qtdMin = d.quantidade_minima != null ? d.quantidade_minima : '';
                var qtd = parseFloat(d.quantidade) || 0;
                var temEstoque = qtd > 0;
                var btn = '';
                if (temEstoque) {
                    btn = '<button type="button" class="btn btn-outline-primary btn-editar" title="Editar quantidade (já existe no estoque)" data-id="' + d.produto_id + '" data-nome="' + (d.nome_produto || '').replace(/"/g, '&quot;') + '" data-codigo="' + (d.codigo_formatado || '') + '" data-qtd="' + d.quantidade + '" data-qtd-min="' + qtdMin + '"><i class="fas fa-edit"></i> Editar</button>';
                } else {
                    btn = '<button type="button" class="btn btn-outline-success btn-adicionar" title="Primeira entrada no estoque" data-id="' + d.produto_id + '" data-nome="' + (d.nome_produto || '').replace(/"/g, '&quot;') + '" data-codigo="' + (d.codigo_formatado || '') + '" data-qtd="0" data-qtd-min="' + qtdMin + '"><i class="fas fa-plus"></i> Adicionar</button>';
                }
                return '<div class="btn-group btn-group-sm">' + btn + '</div>';
            }}
        ],
        order: [[0, 'asc']],
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

    var baseUrlSave = '<?= $url ?>pages/CD/estoque_salvar.php';
    var modal = new bootstrap.Modal(document.getElementById('modalEstoque'));

    function abrirModal(acao, produtoId, nomeProduto, codigo, qtdAtual, qtdMinima) {
        $('#modalProdutoId').val(produtoId);
        $('#modalAcao').val(acao);
        $('#modalProdutoNome').text(nomeProduto || '-');
        $('#modalProdutoCodigo').text(codigo || '');
        $('#modalQtdAtual').text(qtdAtual || '0');
        $('#modalQuantidade').val('').attr('min', 1);
        $('#modalQuantidadeMinima').val(qtdMinima !== '' && qtdMinima !== undefined && qtdMinima !== null ? qtdMinima : '');
        if (acao === 'editar') {
            $('#modalEstoqueTitulo').text('Editar Quantidade');
            $('#modalLabelQuantidade').text('Quantidade a adicionar');
            $('#modalDicaQuantidade').text('Digite quantas unidades deseja adicionar ao estoque atual. Será somado à quantidade existente.');
            $('#modalQtdAtualWrap').show();
        } else {
            $('#modalEstoqueTitulo').text('Adicionar ao Estoque');
            $('#modalLabelQuantidade').text('Quantidade a adicionar');
            $('#modalDicaQuantidade').text('Digite quantas unidades deseja adicionar.');
            $('#modalQtdAtualWrap').show();
        }
        modal.show();
        setTimeout(function() { $('#modalQuantidade').focus(); }, 300);
    }

    $(document).on('click', '.btn-editar', function() {
        var id = $(this).data('id'), nome = $(this).data('nome'), codigo = $(this).data('codigo'), qtd = $(this).data('qtd'), qtdMin = $(this).data('qtd-min');
        abrirModal('editar', id, nome, codigo, qtd, qtdMin);
    });

    $(document).on('click', '.btn-adicionar', function() {
        var id = $(this).data('id'), nome = $(this).data('nome'), codigo = $(this).data('codigo'), qtd = $(this).data('qtd'), qtdMin = $(this).data('qtd-min');
        abrirModal('adicionar', id, nome, codigo, qtd, qtdMin);
    });

    $('#btnSalvarEstoque').on('click', function() {
        var acao = $('#modalAcao').val(), produtoId = $('#modalProdutoId').val(), qtd = $('#modalQuantidade').val();
        if (!produtoId) return;
        qtd = parseFloat(String(qtd).replace(',', '.')) || 0;
        if ((acao === 'editar' || acao === 'adicionar') && qtd <= 0) { alert('Informe uma quantidade maior que zero para adicionar.'); return; }

        var qtdMin = $('#modalQuantidadeMinima').val();
        qtdMin = qtdMin !== '' ? (parseFloat(String(qtdMin).replace(',', '.')) || 0) : null;

        var $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');
        $.post(baseUrlSave, {
            acao: acao,
            produto_id: produtoId,
            quantidade: qtd,
            quantidade_minima: qtdMin,
            descricao_produto: $('#modalProdutoNome').text()
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
