<?php

use App\Models\FinanceiroContas\Controller;

// Obter listas de clientes, fornecedores e categorias
$controller = new Controller();
$clientes = $controller->listarClientes();
$fornecedores = $controller->listarFornecedores();
$categorias = $controller->listarCategorias();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'fornecedor_id' => $_POST['fornecedor_id'] ?? null,
        'cliente_id' => $_POST['cliente_id'] ?? null,
        'categoria_id' => $_POST['categoria_id'] ?? null,
        'data_vencimento' => $_POST['data_vencimento'],
        'valor' => $_POST['valor'],
        'data_pagamento' => $_POST['data_pagamento'] ?? null,
        'status' => $_POST['status'],
        'observacao' => $_POST['observacao'],
        'recorrente' => $_POST['recorrente'],
        'tipo' => $_POST['tipo'],
        'num_parcelas' => $_POST['num_parcelas'] ?? null,
    ];

    $return = $controller->cadastro($dados);

    if ($return) {
        echo notify('success', "Conta cadastrada com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao cadastrar a conta.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro de Contas</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <!-- Tipo da Conta -->
                <div class="col-lg-6">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" name="tipo" id="tipo-conta" required>
                        <option value="">Selecione o Tipo</option>
                        <option value="R">Contas a Receber</option>
                        <option value="P">Contas a Pagar</option>
                    </select>
                </div>

                <!-- Cliente (para Contas a Receber) -->
                <div class="col-lg-6" id="cliente-field" style="display: none;">
                    <label class="form-label">Cliente</label>
                    <select class="form-select" name="cliente_id">
                        <option value="">Selecione um Cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['nome_pf'] ?: $cliente['razao_social_pj']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Fornecedor (para Contas a Pagar) -->
                <div class="col-lg-6" id="fornecedor-field" style="display: none;">
                    <label class="form-label">Fornecedor</label>
                    <select class="form-select" name="fornecedor_id">
                        <option value="">Selecione um Fornecedor</option>
                        <?php foreach ($fornecedores as $fornecedor): ?>
                            <option value="<?= $fornecedor['id'] ?>"><?= htmlspecialchars($fornecedor['razao_social']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Categoria (somente para Contas a Pagar) -->
                <div class="col-lg-6" id="categoria-field" style="display: none;">
                    <label class="form-label">Categoria</label>
                    <select class="form-select" name="categoria_id">
                        <option value="">Selecione uma Categoria</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['descricao']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Data de Vencimento -->
                <div class="col-lg-6">
                    <label class="form-label">Data de Vencimento</label>
                    <input type="date" class="form-control" name="data_vencimento" required>
                </div>

                <!-- Número de Parcelas (somente para Contas a Pagar) -->
                <div class="col-lg-6" id="num-parcelas-field" style="display: none;">
                    <label class="form-label">Número de Parcelas</label>
                    <select class="form-select" name="num_parcelas" id="num_parcelas">
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?>x</option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Campos de Parcelas -->
                <div id="parcelas-container" style="display: none;">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <div class="row g-3 parcela-fields" id="parcela_<?= $i ?>" style="display: none;">
                            <div class="col-lg-6">
                                <label class="form-label">Valor da Parcela <?= $i ?> (R$)</label>
                                <input type="number" step="0.01" class="form-control parcela-input" name="val_par<?= $i ?>" data-parcela="<?= $i ?>">
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Data da Parcela <?= $i ?></label>
                                <input type="date" class="form-control" name="dt_par<?= $i ?>">
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

                <!-- Valor Total -->
                <div class="col-lg-6">
                    <label class="form-label">Valor Total (R$)</label>
                    <input type="number" step="0.01" class="form-control" name="valor" id="valor_total" required>
                </div>

                <!-- Status -->
                <div class="col-lg-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        <option value="Pendente">Pendente</option>
                        <option value="Pago">Pago</option>
                    </select>
                </div>

                <!-- Recorrente -->
                <div class="col-lg-6" id="recorrente-field" style="display: none;">
                    <label class="form-label">Recorrente</label>
                    <select class="form-select" name="recorrente">
                        <option value="N">Não</option>
                        <option value="S">Sim</option>
                    </select>
                </div>

                <!-- Observação -->
                <div class="col-lg-12">
                    <label class="form-label">Observação</label>
                    <textarea class="form-control" name="observacao" rows="3"></textarea>
                </div>

                <!-- Botão Salvar -->
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Resetar formulário ao mudar tipo de conta
    document.getElementById('tipo-conta').addEventListener('change', function () {
        const tipo = this.value;
        document.getElementById('cliente-field').style.display = (tipo === 'R') ? 'block' : 'none';
        document.getElementById('fornecedor-field').style.display = (tipo === 'P') ? 'block' : 'none';
        document.getElementById('categoria-field').style.display = (tipo === 'P') ? 'block' : 'none';
        document.getElementById('recorrente-field').style.display = (tipo === 'P') ? 'block' : 'none';
        document.getElementById('num-parcelas-field').style.display = (tipo === 'P') ? 'block' : 'none';
        document.getElementById('parcelas-container').style.display = 'none';
        document.getElementById('valor_total').value = '';
        document.getElementById('num_parcelas').value = '1';
        document.querySelectorAll('.parcela-fields').forEach(field => field.style.display = 'none');
    });

    // Exibir/ocultar parcelas e atualizar valor total
    document.getElementById('num_parcelas').addEventListener('change', function () {
        const numParcelas = parseInt(this.value);
        const parcelasContainer = document.getElementById('parcelas-container');
        const valorTotalInput = document.getElementById('valor_total');
        const parcelaFields = document.querySelectorAll('.parcela-fields');
        document.getElementById('valor_total').value = '';

        parcelasContainer.style.display = (numParcelas > 1) ? 'block' : 'none';
        valorTotalInput.readOnly = (numParcelas > 1);
        valorTotalInput.value = (numParcelas === 1) ? '' : valorTotalInput.value;

        parcelaFields.forEach((field, index) => {
            field.style.display = (index < numParcelas) ? 'flex' : 'none';
        });
    });

    // Atualizar valor total dinamicamente
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('parcela-input')) {
            const parcelaInputs = document.querySelectorAll('.parcela-input');
            let total = 0;

            parcelaInputs.forEach(input => {
              
                total += parseFloat(input.value) || 0;
            });

            document.getElementById('valor_total').value = total.toFixed(2);
            
        }
    });
</script>
