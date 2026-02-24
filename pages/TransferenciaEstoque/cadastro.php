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

                <div class="col-12">
                    <hr>
                    <h5>Produtos para Transferir</h5>
                    <div id="produtos-container">
                        <div class="alert alert-info">Selecione a loja de origem para carregar os produtos disponíveis.</div>
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
document.addEventListener('DOMContentLoaded', function() {
    const lojaOrigemSelect = document.getElementById('loja_origem_id');
    const lojaDestinoSelect = document.getElementById('loja_destino_id');
    const produtosContainer = document.getElementById('produtos-container');
    const btnTransferir = document.getElementById('btnTransferir');
    const lojaOrigemNome = document.getElementById('loja_origem_nome');
    const lojaDestinoNome = document.getElementById('loja_destino_nome');
    let produtosData = [];

    lojaOrigemSelect.addEventListener('change', function() {
        const lojaId = this.value;
        lojaOrigemNome.value = this.options[this.selectedIndex].text;

        if (!lojaId) {
            produtosContainer.innerHTML = '<div class="alert alert-info">Selecione a loja de origem para carregar os produtos disponíveis.</div>';
            btnTransferir.disabled = true;
            return;
        }

        fetch('<?= $url ?>pages/TransferenciaEstoque/buscar_produtos.php?loja_id=' + lojaId)
            .then(r => r.json())
            .then(data => {
                produtosData = data;
                renderProdutos();
            })
            .catch(() => {
                produtosContainer.innerHTML = '<div class="alert alert-danger">Erro ao carregar produtos.</div>';
            });
    });

    lojaDestinoSelect.addEventListener('change', function() {
        lojaDestinoNome.value = this.options[this.selectedIndex].text;
    });

    function renderProdutos() {
        if (produtosData.length === 0) {
            produtosContainer.innerHTML = '<div class="alert alert-warning">Nenhum produto com estoque disponível nesta loja.</div>';
            btnTransferir.disabled = true;
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-striped"><thead><tr>';
        html += '<th>Selecionar</th><th>Código</th><th>Produto</th><th>Estoque Disponível</th><th>Qtd a Transferir</th>';
        html += '</tr></thead><tbody>';

        produtosData.forEach((p, i) => {
            html += `<tr>
                <td><input type="checkbox" class="form-check-input produto-check" data-index="${i}"></td>
                <td>${p.id}</td>
                <td>${p.nome_produto}</td>
                <td><span class="badge bg-info">${p.estoque}</span></td>
                <td>
                    <input type="number" step="0.01" min="0.01" max="${p.estoque}" class="form-control qtd-input" 
                        data-index="${i}" disabled style="width:100px">
                    <input type="hidden" name="produtos[${i}][id]" value="" class="prod-id-hidden" data-index="${i}">
                    <input type="hidden" name="produtos[${i}][nome_produto]" value="${p.nome_produto}" class="prod-nome-hidden" data-index="${i}">
                    <input type="hidden" name="produtos[${i}][quantidade]" value="0" class="prod-qtd-hidden" data-index="${i}">
                </td>
            </tr>`;
        });

        html += '</tbody></table></div>';
        produtosContainer.innerHTML = html;
        btnTransferir.disabled = false;

        document.querySelectorAll('.produto-check').forEach(cb => {
            cb.addEventListener('change', function() {
                const idx = this.dataset.index;
                const qtdInput = document.querySelector(`.qtd-input[data-index="${idx}"]`);
                const idHidden = document.querySelector(`.prod-id-hidden[data-index="${idx}"]`);

                if (this.checked) {
                    qtdInput.disabled = false;
                    qtdInput.value = 1;
                    idHidden.value = produtosData[idx].id;
                    document.querySelector(`.prod-qtd-hidden[data-index="${idx}"]`).value = 1;
                } else {
                    qtdInput.disabled = true;
                    qtdInput.value = '';
                    idHidden.value = '';
                    document.querySelector(`.prod-qtd-hidden[data-index="${idx}"]`).value = 0;
                }
            });
        });

        document.querySelectorAll('.qtd-input').forEach(input => {
            input.addEventListener('input', function() {
                const idx = this.dataset.index;
                document.querySelector(`.prod-qtd-hidden[data-index="${idx}"]`).value = this.value || 0;
            });
        });
    }
});
</script>
