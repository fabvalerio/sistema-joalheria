<?php

use App\Models\Produtos\Controller;

// Instanciar o Controller
$controller = new Controller();

// Obter listas de fornecedores, grupos, subgrupos e cotações
$fornecedores = $controller->listarFornecedores();
$grupos = $controller->listarGrupos();
$subgrupos = $controller->listarSubgrupos();
$cotacoes = $controller->listarCotacoes();
$modelos = $controller->listarModelos();
$pedras = $controller->listarPedras();
?>
<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Orçamento de Fulano de Tal</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body-container">
        <!-- Orçamento inicial com data-orcamento-id="0" -->
        <div class="card-body orcamento" data-orcamento-id="0" style="
    margin: 1vw;
    border: 2px solid #0000000f;
    border-radius: 20px;
">
            <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-lg-4">
                        <label class="form-label">Data pedido</label>
                        <input type="date" name="pedido[]" class="form-control">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Data Entrega</label>
                        <input type="date" name="entrega[]" class="form-control">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Status de Pagamento</label>
                        <select name="pagamento[]" class="form-select">
                            <option value="Pago">Pago</option>
                            <option value="Aberto">Aberto</option>
                            <option value="Parcial">Parcial</option>
                        </select>
                    </div>

                    <div class="col-lg-12">
                        <label class="form-label">Descrição</label>
                        <input type="text" name="descricao[0]" class="form-control" required placeholder="Descrição do Orçamento">
                    </div>

                    <div class="col-lg-12">
                        <label class="form-label">Material</label>
                        <div>
                            <?php foreach ($grupos as $grupo): ?>
                                <input type="checkbox" class="btn-check material-checkbox" id="<?= htmlspecialchars($grupo['nome_grupo']) ?>1" data-material="<?= htmlspecialchars($grupo['nome_grupo']) ?>">
                                <label class="btn btn-outline-success" for="<?= htmlspecialchars($grupo['nome_grupo']) ?>1"><?= htmlspecialchars($grupo['nome_grupo']) ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <label class="form-label">Cotação do Material</label>
                        <ul class="list-group cotacao-material">
                            <!-- Aqui serão adicionadas as cotações dinamicamente -->
                        </ul>
                    </div>

                    <div class="col-lg-12">
                        <label class="form-label">Pedra</label>
                        <div>
                            <?php foreach ($pedras as $pedra): ?>
                                <input type="checkbox" class="btn-check pedra-checkbox mt-1" name="pedra_<?= htmlspecialchars($pedra['nome']) ?>[0]" id="<?= htmlspecialchars($pedra['nome']) ?>1" data-pedra="<?= htmlspecialchars($pedra['nome']) ?>">
                                <label class="btn btn-outline-success mt-2" for="<?= htmlspecialchars($pedra['nome']) ?>1"><?= htmlspecialchars($pedra['nome']) ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <label class="form-label">Cotação da Pedra</label>
                        <ul class="list-group cotacao-pedra">
                            <!-- Aqui serão adicionadas as cotações dinamicamente -->
                        </ul>
                    </div>

                    <div class="col-lg-12 text-end">
                        <button type="button" class="btn btn-danger btn-remover">Remover</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="card-footer">
        <button type="button" class="btn btn-success w-100" id="addOrcamento">Adicionar Orçamento</button>
    </div>
</div>

<script>
    // Utilizaremos um contador para gerar um ID único para cada orçamento adicionado
    let contadorOrcamento = 1;

    document.getElementById("addOrcamento").addEventListener("click", function() {
        let container = document.querySelector(".card-body-container");
        let original = document.querySelector(".card-body");
        let clone = original.cloneNode(true);

        // Gerar um identificador único usando o contador
        let uniqueId = contadorOrcamento++;

        // Atribuir o ID único ao novo orçamento
        clone.setAttribute("data-orcamento-id", uniqueId);

        // Atualizar os atributos de id, for e name dos elementos clonados
        clone.querySelectorAll("input, select, label").forEach((element) => {
            if (element.id) {
                element.id = element.id + "_" + uniqueId;
            }
            if (element.hasAttribute("for")) {
                element.setAttribute("for", element.getAttribute("for") + "_" + uniqueId);
            }
            if (element.tagName === "INPUT" || element.tagName === "SELECT") {
                if (element.name) {
                    // Remove qualquer valor entre colchetes (ex.: [0]) e concatena o uniqueId, mantendo os colchetes no final
                    let baseName = element.name.replace(/\[\d*\]$/, "");
                    element.name = baseName + "_" + uniqueId + "[]";
                }
                // Limpar o valor do input ou select
                element.value = "";
            }
        });

        // Limpar as listas de cotações para que não sejam copiadas do orçamento anterior
        clone.querySelectorAll(".cotacao-material").forEach(el => el.innerHTML = "");
        clone.querySelectorAll(".cotacao-pedra").forEach(el => el.innerHTML = "");

        // Desmarcar todos os checkboxes do novo orçamento
        clone.querySelectorAll("input[type=checkbox]").forEach(checkbox => {
            checkbox.checked = false;
        });

        // Re-adicionar os eventos para os checkboxes de materiais e pedras
        clone.querySelectorAll(".material-checkbox").forEach(checkbox => {
            checkbox.addEventListener("change", toggleCotacaoMaterial);
        });
        clone.querySelectorAll(".pedra-checkbox").forEach(checkbox => {
            checkbox.addEventListener("change", toggleCotacaoPedra);
        });

        clone.querySelector(".btn-remover").addEventListener("click", function() {
            removeOrcamento(this);
        });

        container.appendChild(clone);
    });

    function removeOrcamento(button) {
        let container = document.querySelector(".card-body-container");
        if (container.children.length > 1) {
            button.closest(".card-body").remove();
        } else {
            alert("O orçamento principal não pode ser removido!");
        }
    }

    // Adicionar eventos aos checkboxes do primeiro orçamento
    document.querySelectorAll(".material-checkbox").forEach(checkbox => {
        checkbox.addEventListener("change", toggleCotacaoMaterial);
    });
    document.querySelectorAll(".pedra-checkbox").forEach(checkbox => {
        checkbox.addEventListener("change", toggleCotacaoPedra);
    });

    function toggleCotacaoMaterial(event) {
        // Obter o nome do material selecionado
        let material = event.target.getAttribute("data-material");

        // Obter o contêiner do orçamento e o ID único deste orçamento
        let orcamento = event.target.closest(".orcamento");
        let orcamentoId = orcamento.getAttribute("data-orcamento-id");

        // Selecionar a lista de cotações de material deste orçamento
        let cotacaoContainer = orcamento.querySelector(".cotacao-material");

        if (event.target.checked) {
            // Ao adicionar, incluímos o número (orcamentoId) dentro dos colchetes para diferenciar
            let html = `
            <li class="list-group-item d-flex justify-content-between align-items-center cotacao-${material}">
                <span class="badge text-bg-success rounded-pill">${material.charAt(0).toUpperCase() + material.slice(1)}</span>
                <div class="d-flex">
                    <input type="text" name="cotacao_${material}[${orcamentoId}]" class="form-control me-2" placeholder="Cotação">
                    <input type="text" name="gramas_${material}[${orcamentoId}]" class="form-control me-2" placeholder="GR">
                    <input type="text" name="margem_${material}[${orcamentoId}]" class="form-control me-2" placeholder="Margem (%)">
                    <span class="input-group-text">R$</span>
                    <input type="text" name="total_${material}[${orcamentoId}]" class="form-control" placeholder="Total">
                </div>
            </li>
            `;
            cotacaoContainer.insertAdjacentHTML("beforeend", html);
        } else {
            // Remove a cotação se o checkbox for desmarcado
            let rowToRemove = cotacaoContainer.querySelector(`.cotacao-${material}`);
            if (rowToRemove) {
                rowToRemove.remove();
            }
        }
    }

    function toggleCotacaoPedra(event) {
        let pedra = event.target.getAttribute("data-pedra");
        // Para manter a consistência, também adicionamos o identificador único no name dos inputs de pedra
        let orcamento = event.target.closest(".orcamento");
        let orcamentoId = orcamento.getAttribute("data-orcamento-id");
        let cotacaoContainer = orcamento.querySelector(".cotacao-pedra");

        if (event.target.checked) {
            let html = `
            <li class="list-group-item d-flex justify-content-between align-items-center cotacao-${pedra}">
                <span class="badge text-bg-primary rounded-pill">${pedra.charAt(0).toUpperCase() + pedra.slice(1)}</span>
                <div class="d-flex">
                    <input type="text" name="cotacao_${pedra}[${orcamentoId}]" class="form-control me-2" placeholder="Cotação">
                    <input type="text" name="quantidade_${pedra}[${orcamentoId}]" class="form-control me-2" placeholder="QL">
                    <input type="text" name="margem_${pedra}[${orcamentoId}]" class="form-control me-2" placeholder="Margem (%)">
                    <span class="input-group-text">R$</span>
                    <input type="text" name="total_${pedra}[${orcamentoId}]" class="form-control" placeholder="Total">
                </div>
            </li>
            `;
            cotacaoContainer.insertAdjacentHTML("beforeend", html);
        } else {
            let rowToRemove = cotacaoContainer.querySelector(`.cotacao-${pedra}`);
            if (rowToRemove) {
                rowToRemove.remove();
            }
        }
    }
</script>