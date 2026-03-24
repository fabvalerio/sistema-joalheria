/**
 * Múltiplas formas de pagamento — cadastro de pedido.
 */
(function () {
    'use strict';

    var cfg = window.__pedidoPagamentoConfig || {};
    var baseUrl = (cfg.baseUrl || '').replace(/\/?$/, '/');

    function brl(n) {
        var x = Number(n) || 0;
        return 'R$ ' + x.toFixed(2).replace('.', ',');
    }

    function getTotalPedido() {
        var el = document.getElementById('total');
        return parseFloat(el && el.value ? el.value : '0') || 0;
    }

    function listaEl() {
        return document.getElementById('listaPagamentos');
    }

    function syncChequeMaterialVisibility() {
        var rows = document.querySelectorAll('.linha-pagamento');
        var temCheque = false;
        rows.forEach(function (row) {
            var f = row.querySelector('.pp-forma');
            if (!f) return;
            if (f.value === 'Cheque') temCheque = true;
        });
        var ch = document.getElementById('cheque_container');
        if (ch) ch.style.display = temCheque ? 'block' : 'none';
    }

    function atualizarHiddenJson() {
        var rows = document.querySelectorAll('.linha-pagamento');
        var arr = [];
        rows.forEach(function (row) {
            var forma = row.querySelector('.pp-forma');
            var valIn = row.querySelector('.pp-valor');
            var obs = row.querySelector('.pp-obs');
            var cartSel = row.querySelector('.pp-cartao');
            var parcSel = row.querySelector('.pp-parcelas');
            if (!forma || !forma.value) return;
            var v = parseFloat(valIn && valIn.value ? valIn.value : '0') || 0;
            var cartaoId = null;
            if (cartSel && cartSel.value) cartaoId = parseInt(cartSel.value, 10) || null;
            var parcelas = 1;
            if (forma.value === 'Cartão de Crédito' && parcSel && parcSel.value) {
                parcelas = parseInt(parcSel.value, 10) || 1;
            }
            var item = {
                forma: forma.value,
                valor: Math.round(v * 100) / 100,
                parcelas: parcelas,
                observacao: obs && obs.value ? obs.value.trim() : '',
                cartao_id: cartaoId
            };
            if (forma.value === 'Material') {
                var msel = row.querySelector('.pp-material');
                var gIn = row.querySelector('.pp-gramas');
                item.material_id = msel && msel.value ? parseInt(msel.value, 10) : null;
                item.gramas = gIn && gIn.value ? parseFloat(gIn.value) : null;
            }
            arr.push(item);
        });
        var hid = document.getElementById('inputPagamentos');
        if (hid) hid.value = JSON.stringify(arr);
    }

    function somaLinhas() {
        var s = 0;
        document.querySelectorAll('.linha-pagamento .pp-valor').forEach(function (inp) {
            s += parseFloat(inp.value || '0') || 0;
        });
        return Math.round(s * 100) / 100;
    }

    function mostrarAlertaPagamento(msg, show) {
        var al = document.getElementById('alertaPagamento');
        var sp = document.getElementById('msgAlertaPagamento');
        if (!al || !sp) return;
        if (show) {
            sp.textContent = msg;
            al.classList.remove('d-none');
        } else {
            al.classList.add('d-none');
            sp.textContent = '';
        }
    }

    function recalcularTotais() {
        var totalPed = getTotalPedido();
        var totalPago = somaLinhas();
        var diff = Math.round((totalPago - totalPed) * 100) / 100;
        var exT = document.getElementById('exibeTotalPedido');
        var exP = document.getElementById('exibeTotalPago');
        var exD = document.getElementById('exibeDiferencaPagamento');
        if (exT) exT.textContent = brl(totalPed);
        if (exP) exP.textContent = brl(totalPago);
        if (exD) exD.textContent = brl(diff);
        var tr = document.getElementById('trDiferencaPagamento');
        if (tr) {
            if (Math.abs(diff) < 0.02) tr.classList.add('d-none');
            else tr.classList.remove('d-none');
        }
        var vp = document.getElementById('valor_pago');
        if (vp) vp.value = totalPago > 0 ? totalPago.toFixed(2) : '';
        atualizarHiddenJson();
        if (Math.abs(diff) < 0.02) mostrarAlertaPagamento('', false);
    }

    window.pedidoPagamentoRecalc = recalcularTotais;

    function fetchJson(url) {
        return fetch(url).then(function (r) {
            if (!r.ok) throw new Error('fetch');
            return r.json();
        });
    }

    function preencherCartoes(row, tipoUrl) {
        var wrap = row.querySelector('.pp-cartao-wrap');
        var sel = row.querySelector('.pp-cartao');
        var parcW = row.querySelector('.pp-parcelas-wrap');
        var parc = row.querySelector('.pp-parcelas');
        if (!sel) return;
        sel.innerHTML = '<option value="">Carregando...</option>';
        fetchJson(baseUrl + 'pages/Pedidos/listar_cartoes.php?tipo=' + encodeURIComponent(tipoUrl))
            .then(function (cartoes) {
                sel.innerHTML = '<option value="">Selecione</option>';
                cartoes.forEach(function (c) {
                    var o = document.createElement('option');
                    o.value = c.id;
                    o.textContent = c.bandeira || c.nome_cartao || ('#' + c.id);
                    o.dataset.maxParcelas = c.max_parcelas || 12;
                    for (var i = 1; i <= 12; i++) {
                        var jk = 'juros_parcela_' + i;
                        if (c[jk] != null) o.dataset[jk] = c[jk];
                    }
                    sel.appendChild(o);
                });
            })
            .catch(function () {
                sel.innerHTML = '<option value="">Erro ao carregar</option>';
            });
        if (wrap) wrap.style.display = 'block';
        if (tipoUrl === 'Crédito') {
            if (parcW) parcW.style.display = 'block';
        } else {
            if (parcW) parcW.style.display = 'none';
            if (parc) {
                parc.innerHTML = '<option value="1">1x</option>';
                parc.value = '1';
            }
        }
    }

    function onCartaoChange(row) {
        var forma = row.querySelector('.pp-forma');
        var sel = row.querySelector('.pp-cartao');
        var parcW = row.querySelector('.pp-parcelas-wrap');
        var parc = row.querySelector('.pp-parcelas');
        if (!forma || forma.value !== 'Cartão de Crédito' || !sel || !parc) return;
        var opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) {
            if (parc) parc.innerHTML = '';
            return;
        }
        var max = parseInt(opt.dataset.maxParcelas || '12', 10) || 12;
        parc.innerHTML = '';
        for (var i = 1; i <= max; i++) {
            var o = document.createElement('option');
            o.value = String(i);
            var ju = parseFloat(opt.dataset['juros_parcela_' + i] || '0') || 0;
            o.textContent = i + 'x' + (ju ? ' (juros ' + ju + '%)' : '');
            o.dataset.juros = String(ju);
            parc.appendChild(o);
        }
        parc.value = '1';
        if (parcW) parcW.style.display = 'block';
    }

    function onFormaChange(row) {
        var forma = row.querySelector('.pp-forma');
        var wrap = row.querySelector('.pp-cartao-wrap');
        var sel = row.querySelector('.pp-cartao');
        var parcW = row.querySelector('.pp-parcelas-wrap');
        var parc = row.querySelector('.pp-parcelas');
        if (!forma) return;
        setMaterialRowVisible(row, false);
        if (wrap) wrap.style.display = 'none';
        if (parcW) parcW.style.display = 'none';
        if (sel) sel.innerHTML = '<option value="">...</option>';
        if (parc) parc.innerHTML = '';
        if (forma.value === 'Cartão de Crédito') {
            preencherCartoes(row, 'Crédito');
        } else if (forma.value === 'Cartão de Débito') {
            preencherCartoes(row, 'Débito');
        } else if (forma.value === 'Material') {
            setMaterialRowVisible(row, true);
            ensureMateriaisLoaded().then(function () {
                fillMaterialSelectOptions(row.querySelector('.pp-material'));
                updateMaterialValorFromRow(row);
            });
        }
        syncChequeMaterialVisibility();
        if (forma.value === 'Cheque') ensureChequesLoaded();
        recalcularTotais();
    }

    var chequesLoaded = false;
    function ensureChequesLoaded() {
        if (chequesLoaded) return;
        var select = document.getElementById('cheque_config_id');
        if (!select) return;
        fetchJson(baseUrl + 'pages/Pedidos/listar_cheques.php')
            .then(function (cheques) {
                select.innerHTML = '<option value="" disabled selected>Selecione uma configuração</option>';
                cheques.forEach(function (ch) {
                    var opt = document.createElement('option');
                    opt.value = ch.id;
                    opt.textContent = (ch.nome_cheque || '') + ' (' + (ch.max_parcelas || 0) + 'x)';
                    opt.dataset.maxParcelas = ch.max_parcelas || 1;
                    for (var i = 1; i <= 12; i++) {
                        opt.dataset['juros_parcela_' + i] = ch['juros_parcela_' + i] || 0;
                    }
                    select.appendChild(opt);
                });
                chequesLoaded = true;
            })
            .catch(function () {});
    }

    var materiaisLista = [];

    function ensureMateriaisLoaded() {
        if (materiaisLista.length) {
            return Promise.resolve(materiaisLista);
        }
        return fetchJson(baseUrl + 'pages/Pedidos/listar_materiais_pagamento.php')
            .then(function (list) {
                materiaisLista = list || [];
                return materiaisLista;
            })
            .catch(function () {
                materiaisLista = [];
                return materiaisLista;
            });
    }

    function fillMaterialSelectOptions(sel) {
        if (!sel) return;
        var cur = sel.value;
        sel.innerHTML = '<option value="">Selecione...</option>';
        materiaisLista.forEach(function (m) {
            var o = document.createElement('option');
            o.value = m.id;
            o.textContent = (m.tipo_material || 'Material') + ' — R$ ' + parseFloat(m.valor_por_grama || 0).toFixed(2) + '/g';
            o.dataset.valorPorGrama = String(m.valor_por_grama != null ? m.valor_por_grama : 0);
            sel.appendChild(o);
        });
        if (cur) sel.value = cur;
    }

    function setMaterialRowVisible(row, show) {
        var ms = row.querySelector('.pp-material-select-wrap');
        var gw = row.querySelector('.pp-gramas-wrap');
        var valIn = row.querySelector('.pp-valor');
        if (ms) ms.style.display = show ? 'block' : 'none';
        if (gw) gw.style.display = show ? 'block' : 'none';
        if (valIn) {
            if (show) {
                valIn.setAttribute('readonly', 'readonly');
                valIn.classList.add('bg-light');
            } else {
                valIn.removeAttribute('readonly');
                valIn.classList.remove('bg-light');
            }
        }
        if (!show) {
            var msel = row.querySelector('.pp-material');
            var gr = row.querySelector('.pp-gramas');
            if (msel) msel.innerHTML = '<option value="">Selecione...</option>';
            if (gr) gr.value = '';
        }
    }

    function updateMaterialValorFromRow(row) {
        var sel = row.querySelector('.pp-material');
        var gr = row.querySelector('.pp-gramas');
        var valIn = row.querySelector('.pp-valor');
        if (!sel || !gr || !valIn) return;
        var opt = sel.options[sel.selectedIndex];
        var vpg = parseFloat(opt && opt.dataset.valorPorGrama ? opt.dataset.valorPorGrama : '0') || 0;
        var gramas = parseFloat(gr.value || '0') || 0;
        var total = Math.round(gramas * vpg * 100) / 100;
        valIn.value = total > 0 ? total.toFixed(2) : '';
        recalcularTotais();
    }

    function setupChequeJurosHandlers() {
        var cfgSel = document.getElementById('cheque_config_id');
        var numParc = document.getElementById('numero_parcelas_cheque');
        var container = document.getElementById('cheque_numero_container');
        if (!cfgSel || !numParc) return;

        cfgSel.addEventListener('change', function () {
            var opt = cfgSel.options[cfgSel.selectedIndex];
            var max = parseInt(opt && opt.dataset.maxParcelas ? opt.dataset.maxParcelas : '1', 10) || 1;
            numParc.innerHTML = '<option value="" disabled selected>Selecione as parcelas</option>';
            for (var i = 1; i <= max; i++) {
                var juros = opt.dataset['juros_parcela_' + i] || 0;
                var o = document.createElement('option');
                o.value = String(i);
                o.textContent = i + 'x (Juros: ' + juros + '%)';
                o.dataset.juros = String(juros);
                numParc.appendChild(o);
            }
            if (container) container.innerHTML = '';
        });

        numParc.addEventListener('change', function () {
            var n = parseInt(numParc.value, 10) || 0;
            if (container) {
                container.innerHTML = '';
                for (var i = 1; i <= n; i++) {
                    var div = document.createElement('div');
                    div.className = 'col-lg-4';
                    div.innerHTML = '<label class="form-label">Nº Cheque parcela ' + i + '</label><input type="text" class="form-control" name="numero_cheque[' + i + ']" placeholder="Número do cheque">';
                    container.appendChild(div);
                }
            }
            var opt = numParc.options[numParc.selectedIndex];
            var juros = parseFloat(opt && opt.dataset.juros ? opt.dataset.juros : '0') || 0;
            var totalField = document.getElementById('total');
            var jurosAplicado = document.getElementById('juros_aplicado');
            if (!totalField || !jurosAplicado) return;
            var totalSemJuros = parseFloat(totalField.value || '0') - parseFloat(jurosAplicado.value || '0');
            if (totalSemJuros > 0 && !isNaN(juros)) {
                var totalComJuros = totalSemJuros * (1 + juros / 100);
                totalField.value = totalComJuros.toFixed(2);
                jurosAplicado.value = (totalComJuros - totalSemJuros).toFixed(2);
            }
            recalcularTotais();
        });
    }

    function syncMateriaisPostFields() {
        var form = document.getElementById('formPedido');
        if (!form) return;
        var old = document.getElementById('materiais-inline-holder');
        if (old) old.remove();
        var holder = document.createElement('div');
        holder.id = 'materiais-inline-holder';
        holder.setAttribute('aria-hidden', 'true');
        holder.style.display = 'none';
        var idx = 0;
        document.querySelectorAll('.linha-pagamento').forEach(function (row) {
            var f = row.querySelector('.pp-forma');
            if (!f || f.value !== 'Material') return;
            var mid = row.querySelector('.pp-material');
            var gr = row.querySelector('.pp-gramas');
            if (!mid || !mid.value || !gr) return;
            var inpId = document.createElement('input');
            inpId.type = 'hidden';
            inpId.name = 'materiais[' + idx + '][material_id]';
            inpId.value = mid.value;
            holder.appendChild(inpId);
            var inpG = document.createElement('input');
            inpG.type = 'hidden';
            inpG.name = 'materiais[' + idx + '][gramas]';
            inpG.value = gr.value || '0';
            holder.appendChild(inpG);
            idx++;
        });
        if (idx > 0) {
            form.appendChild(holder);
        }
    }

    function addLinha() {
        var tpl = document.getElementById('templateLinhaPagamento');
        var host = listaEl();
        if (!tpl || !host || !tpl.content) return;
        var node = tpl.content.cloneNode(true).querySelector('.linha-pagamento');
        if (!node) return;
        host.appendChild(node);
        var forma = node.querySelector('.pp-forma');
        var btnR = node.querySelector('.pp-remove');
        if (forma) {
            forma.addEventListener('change', function () {
                onFormaChange(node);
            });
        }
        var cart = node.querySelector('.pp-cartao');
        if (cart) {
            cart.addEventListener('change', function () {
                onCartaoChange(node);
                recalcularTotais();
            });
        }
        var parc = node.querySelector('.pp-parcelas');
        if (parc) {
            parc.addEventListener('change', recalcularTotais);
        }
        var valIn = node.querySelector('.pp-valor');
        if (valIn) {
            valIn.addEventListener('input', function () {
                var fo = node.querySelector('.pp-forma');
                if (fo && fo.value === 'Material') return;
                recalcularTotais();
            });
            valIn.addEventListener('change', function () {
                var fo = node.querySelector('.pp-forma');
                if (fo && fo.value === 'Material') return;
                recalcularTotais();
            });
        }
        var msel = node.querySelector('.pp-material');
        if (msel) {
            msel.addEventListener('change', function () {
                updateMaterialValorFromRow(node);
            });
        }
        var grIn = node.querySelector('.pp-gramas');
        if (grIn) {
            grIn.addEventListener('input', function () {
                updateMaterialValorFromRow(node);
            });
        }
        var obs = node.querySelector('.pp-obs');
        if (obs) obs.addEventListener('change', atualizarHiddenJson);
        if (btnR) {
            btnR.addEventListener('click', function () {
                if (document.querySelectorAll('.linha-pagamento').length <= 1) {
                    mostrarAlertaPagamento('É necessário ao menos uma forma de pagamento.', true);
                    return;
                }
                node.remove();
                syncChequeMaterialVisibility();
                recalcularTotais();
            });
        }
    }

    function validarAntesSubmit() {
        var clienteId = document.getElementById('cliente_id');
        var dataPedido = document.getElementById('data_pedido');
        var total = getTotalPedido();
        if (!clienteId || !clienteId.value) {
            alert('Selecione o cliente.');
            return false;
        }
        if (!dataPedido || !dataPedido.value) {
            alert('Informe a data do pedido.');
            return false;
        }
        if (total <= 0) {
            alert('O total do pedido deve ser maior que zero.');
            return false;
        }
        var rows = document.querySelectorAll('.linha-pagamento');
        if (!rows.length) {
            alert('Adicione ao menos uma forma de pagamento.');
            return false;
        }
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var f = row.querySelector('.pp-forma');
            var v = row.querySelector('.pp-valor');
            if (!f || !f.value) {
                alert('Selecione a forma de pagamento em todas as linhas.');
                return false;
            }
            var vv = parseFloat(v && v.value ? v.value : '0') || 0;
            if (vv <= 0) {
                alert('Informe valores maiores que zero em cada linha.');
                return false;
            }
            if (f.value === 'Cartão de Crédito' || f.value === 'Cartão de Débito') {
                var cs = row.querySelector('.pp-cartao');
                if (!cs || !cs.value) {
                    alert('Selecione o cartão em cada linha de cartão.');
                    return false;
                }
            }
            if (f.value === 'Cartão de Crédito') {
                var ps = row.querySelector('.pp-parcelas');
                if (!ps || !ps.value) {
                    alert('Selecione as parcelas do cartão de crédito.');
                    return false;
                }
            }
            if (f.value === 'Material') {
                var mid = row.querySelector('.pp-material');
                var gr = row.querySelector('.pp-gramas');
                if (!mid || !mid.value) {
                    alert('Selecione o tipo de material em cada linha de Material.');
                    return false;
                }
                if (!gr || parseFloat(gr.value || '0') <= 0) {
                    alert('Informe as gramas do material (maior que zero) em cada linha de Material.');
                    return false;
                }
            }
        }
        var totalPago = somaLinhas();
        if (Math.abs(totalPago - total) > 0.02) {
            mostrarAlertaPagamento('A soma dos pagamentos deve ser igual ao total do pedido.', true);
            alert('A soma dos pagamentos deve ser igual ao total do pedido.');
            return false;
        }
        var temChequeLinha = false;
        document.querySelectorAll('.linha-pagamento .pp-forma').forEach(function (s) {
            if (s.value === 'Cheque') temChequeLinha = true;
        });
        if (temChequeLinha) {
            var np = document.getElementById('numero_parcelas_cheque');
            if (!np || !np.value) {
                alert('Informe as parcelas do cheque.');
                return false;
            }
        }
        mostrarAlertaPagamento('', false);
        atualizarHiddenJson();
        syncMateriaisPostFields();
        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var btnAdd = document.getElementById('btnAdicionarPagamento');
        if (btnAdd) btnAdd.addEventListener('click', addLinha);

        addLinha();
        setupChequeJurosHandlers();
        ensureMateriaisLoaded();
        recalcularTotais();

        var form = document.getElementById('formPedido');
        if (form) {
            form.addEventListener('submit', function (e) {
                if (!validarAntesSubmit()) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        var totalField = document.getElementById('total');
        if (totalField) {
            totalField.addEventListener('input', recalcularTotais);
        }
    });
})();
