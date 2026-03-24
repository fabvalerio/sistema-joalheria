# 🤖 JARVIS TASK — 24/03/2026 13:23

## Projeto
**sistema-joalheria**

## Solicitação
> na pasta Pedidos/cadastro, preciso que o campo "Forma de Pagamento" seja de vários, por exemplo, posso fazer o pagamento via Dinheiro, Cartão e Material tudo ao mesmo pedido.
Por exemplo uma Joia de 10.000 reais, eu dei Material de ouro de R$1.000 + R$5000 em dinheiro e o restante no cartão de credito parcelado. Com essa forma de pagamento eu consigo completar o montando do valor total de R$10.000, e se o valor não bater, não poderá salvar ou finalizar o pedido. Alguma dúvida para o planejamento antes de editar?

---

## Análise e Instruções do Jarvis

# Guia Técnico: Múltiplas Formas de Pagamento no Cadastro de Pedidos

## Análise da Situação

Antes de implementar, preciso entender melhor a estrutura atual. Com base no que foi fornecido, vou mapear os arquivos relevantes e propor a implementação completa.

---

## Arquivos que Precisam ser Identificados/Criados/Modificados

### Ordem de Implementação

---

## 1. Banco de Dados — Migração

**Arquivo a criar:** `database/migrations/alter_pedidos_formas_pagamento.sql`

```sql
-- Criar tabela de formas de pagamento do pedido (relacionamento 1:N)
CREATE TABLE IF NOT EXISTS `pedido_pagamentos` (
  `id`            INT(11)        NOT NULL AUTO_INCREMENT,
  `pedido_id`     INT(11)        NOT NULL,
  `forma`         VARCHAR(50)    NOT NULL COMMENT 'dinheiro|cartao_credito|cartao_debito|material|cheque|pix|outro',
  `valor`         DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  `parcelas`      INT(3)         NULL DEFAULT 1 COMMENT 'Apenas para cartão de crédito',
  `observacao`    VARCHAR(255)   NULL COMMENT 'Ex: nome do material, bandeira do cartão',
  `created_at`    DATETIME       DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pedido_pagamentos_pedido` (`pedido_id`),
  CONSTRAINT `fk_pedido_pagamentos_pedido`
    FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Remover a coluna antiga de forma_pagamento da tabela pedidos
-- (fazer backup antes!)
-- ALTER TABLE `pedidos` DROP COLUMN `forma_pagamento`;
-- ALTER TABLE `pedidos` DROP COLUMN `parcelas`;
-- (manter as colunas antigas por segurança e migrar depois)
```

---

## 2. Model — Pedido

**Arquivo a modificar:** `App/Models/Pedido.php` (ajuste o path conforme seu projeto)

Adicionar os métodos para manipular os pagamentos:

```php
<?php
// Dentro da classe Pedido (ou onde estiver sua lógica de model)

/**
 * Salva as formas de pagamento de um pedido.
 * Remove as anteriores e insere as novas.
 *
 * @param int   $pedidoId
 * @param array $pagamentos  Ex: [['forma'=>'dinheiro','valor'=>5000,'parcelas'=>1,'observacao'=>''],...]
 * @param PDO   $pdo
 * @return bool
 */
public static function salvarPagamentos(int $pedidoId, array $pagamentos, PDO $pdo): bool
{
    // Remove pagamentos anteriores
    $stmt = $pdo->prepare("DELETE FROM pedido_pagamentos WHERE pedido_id = :pedido_id");
    $stmt->execute([':pedido_id' => $pedidoId]);

    // Insere os novos
    $stmt = $pdo->prepare("
        INSERT INTO pedido_pagamentos (pedido_id, forma, valor, parcelas, observacao)
        VALUES (:pedido_id, :forma, :valor, :parcelas, :observacao)
    ");

    foreach ($pagamentos as $pag) {
        $stmt->execute([
            ':pedido_id'   => $pedidoId,
            ':forma'       => $pag['forma'],
            ':valor'       => $pag['valor'],
            ':parcelas'    => $pag['parcelas'] ?? 1,
            ':observacao'  => $pag['observacao'] ?? null,
        ]);
    }

    return true;
}

/**
 * Busca as formas de pagamento de um pedido.
 */
public static function buscarPagamentos(int $pedidoId, PDO $pdo): array
{
    $stmt = $pdo->prepare("
        SELECT * FROM pedido_pagamentos WHERE pedido_id = :pedido_id ORDER BY id ASC
    ");
    $stmt->execute([':pedido_id' => $pedidoId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Soma total dos pagamentos de um pedido.
 */
public static function totalPagamentos(int $pedidoId, PDO $pdo): float
{
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(valor), 0) as total
        FROM pedido_pagamentos
        WHERE pedido_id = :pedido_id
    ");
    $stmt->execute([':pedido_id' => $pedidoId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return (float) $row['total'];
}
```

---

## 3. Controller / API — Salvar Pedido

**Arquivo a modificar:** Onde o pedido é salvo (provavelmente `App/Controllers/PedidoController.php` ou similar)

Localizar o método `salvar`/`store` e adicionar a lógica de validação + persistência dos pagamentos:

```php
// Dentro do método salvar/store

// 1. Receber os pagamentos do POST
$pagamentos = json_decode($_POST['pagamentos'] ?? '[]', true);

// 2. Validar se veio pelo menos 1 pagamento
if (empty($pagamentos)) {
    return $this->jsonError('Informe ao menos uma forma de pagamento.');
}

// 3. Validar cada item
$totalPago = 0;
foreach ($pagamentos as $pag) {
    if (empty($pag['forma'])) {
        return $this->jsonError('Forma de pagamento inválida.');
    }
    $valor = floatval($pag['valor']);
    if ($valor <= 0) {
        return $this->jsonError('O valor de cada pagamento deve ser maior que zero.');
    }
    $totalPago += $valor;
}

// 4. Comparar com o total do pedido (tolerância de R$0,01 por arredondamento)
$totalPedido = floatval($_POST['total']); // valor total do pedido
if (abs($totalPago - $totalPedido) > 0.01) {
    return $this->jsonError(
        sprintf(
            'A soma dos pagamentos (R$ %.2f) não corresponde ao total do pedido (R$ %.2f).',
            $totalPago,
            $totalPedido
        )
    );
}

// 5. Salvar pedido normalmente...
// $pedidoId = ... (seu código atual)

// 6. Salvar os pagamentos
Pedido::salvarPagamentos($pedidoId, $pagamentos, $pdo);
```

---

## 4. View — Cadastro do Pedido

**Arquivo a modificar:** `Pedidos/cadastro/index.php` (ou equivalente)

### 4.1 — Remover o campo antigo de forma de pagamento

Localizar e **substituir** o campo único de forma de pagamento pelo novo bloco multi-pagamento:

```html
<!-- ============================================================
     BLOCO: FORMAS DE PAGAMENTO (substitui o campo único antigo)
     ============================================================ -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-money-bill-wave"></i> Formas de Pagamento
        </h6>
        <button type="button" class="btn btn-success btn-sm" id="btnAdicionarPagamento">
            <i class="fas fa-plus"></i> Adicionar Forma
        </button>
    </div>
    <div class="card-body">

        <!-- Container onde as linhas de pagamento são inseridas -->
        <div id="listaPagamentos">
            <!-- As linhas são geradas pelo JS -->
        </div>

        <!-- Totalizador -->
        <hr>
        <div class="row">
            <div class="col-md-6 offset-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-right font-weight-bold">Total do Pedido:</td>
                        <td class="text-right" id="exibeTotalPedido">R$ 0,00</td>
                    </tr>
                    <tr>
                        <td class="text-right font-weight-bold">Total Pago:</td>
                        <td class="text-right" id="exibeTotalPago">R$ 0,00</td>
                    </tr>
                    <tr id="trDiferenca">
                        <td class="text-right font-weight-bold text-danger">Diferença:</td>
                        <td class="text-right font-weight-bold text-danger" id="exibeDiferenca">R$ 0,00</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Alerta de erro de pagamento -->
        <div id="alertaPagamento" class="alert alert-danger d-none" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <span id="msgAlertaPagamento"></span>
        </div>

    </div>
</div>

<!-- Campo hidden que armazena o JSON dos pagamentos para submeter ao servidor -->
<input type="hidden" name="pagamentos" id="inputPagamentos" value="[]">
```

### 4.2 — Template HTML da linha de pagamento (adicionar antes do `</body>`)

```html
<!-- Template de linha de pagamento (não é renderizado) -->
<template id="templateLinhaPagamento">
    <div class="linha-pagamento row align-items-end mb-3 border rounded p-2 bg-light">

        <!-- Forma -->
        <div class="col-md-3">
            <label class="small font-weight-bold">Forma <span class="text-danger">*</span></label>
            <select class="form-control form-control-sm select-forma" name="forma_pagamento[]" required>
                <option value="">Selecione...</option>
                <option value="dinheiro">💵 Dinheiro</option>
                <option value="pix">📱 PIX</option>
                <option value="cartao_credito">💳 Cartão de Crédito</option>
                <option value="cartao_debito">💳 Cartão de Débito</option>
                <option value="cheque">📄 Cheque</option>
                <option value="material">🪙 Material (troca/permuta)</option>
                <option value="outro">📦 Outro</option>
            </select>
        </div>

        <!-- Parcelas (só aparece para cartão de crédito) -->
        <div class="col-md-2 campo-parcelas d-none">
            <label class="small font-weight-bold">Parcelas</label>
            <select class="form-control form-control-sm select-parcelas" name="parcelas[]">
                <?php for ($i = 1; $i <= 24; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?>x</option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Valor -->
        <div class="col-md-3">
            <label class="small font-weight-bold">Valor (R$) <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control form-control-sm input-valor money-mask"
                   name="valor_pagamento[]"
                   placeholder="0,00"
                   required>
        </div>

        <!-- Observação -->
        <div class="col-md-3">
            <label class="small font-weight-bold">Observação</label>
            <input type="text"
                   class="form-control form-control-sm input-observacao"
                   name="obs_pagamento[]"
                   placeholder="Ex: Ouro 18k, Visa, etc.">
        </div>

        <!-- Botão remover -->
        <div class="col-md-1 text-center">
            <button type="button"
                    class="btn btn-danger btn-sm btn-remover-pagamento"
                    title="Remover esta forma de pagamento">
                <i class="fas fa-trash"></i>
            </button>
        </div>

    </div>
</template>
```

---

## 5. JavaScript — Lógica Multi-pagamento

**Arquivo a criar:** `assets/js/pedido-pagamento.js`

```javascript
/**
 * pedido-pagamento.js
 * Gerencia múltiplas formas de pagamento no cadastro de pedidos.
 */

(function ($) {
    'use strict';

    // ---------------------------------------------------------------
    // Configuração
    // ---------------------------------------------------------------
    const FORMAS_COM_PARCELAS = ['cartao_credito'];

    // ---------------------------------------------------------------
    // Inicialização
    // ---------------------------------------------------------------
    $(document).ready(function () {
        // Adiciona a primeira linha automaticamente ao abrir o formulário
        adicionarLinha();

        // Botão de adicionar
        $(document).on('click', '#btnAdicionarPagamento', function () {
            adicionarLinha();
        });

        // Botão de remover linha
        $(document).on('click', '.btn-remover-pagamento', function () {
            const $linha = $(this).closest('.linha-pagamento');
            if ($('#listaPagamentos .linha-pagamento').length <= 1) {
                mostrarAlerta('É necessário ao menos uma forma de pagamento.');
                return;
            }
            $linha.remove();
            recalcularTotais();
            atualizarInputHidden();
        });

        // Mudança na forma de pagamento — exibir/ocultar parcelas
        $(document).on('change', '.select-forma', function () {
            const $linha   = $(this).closest('.linha-pagamento');
            const forma    = $(this).val();
            const $parcelas = $linha.find('.campo-parcelas');

            if (FORMAS_COM_PARCELAS.includes(forma)) {
                $parcelas.removeClass('d-none');
            } else {
                $parcelas.addClass('d-none');
                $parcelas.find('.select-parcelas').val(1);
            }

            atualizarInputHidden();
        });

        // Digitação do valor
        $(document).on('input', '.input-valor', function () {
            recalcularTotais();
            atualizarInputHidden();
        });

        // Mudança em qualquer campo da linha
        $(document).on('change', '.select-parcelas, .input-observacao', function () {
            atualizarInputHidden();
        });

        // Antes de submeter o formulário, valida
        $(document).on('submit', '#formPedido', function (e) {
            if (!validarPagamentos()) {
                e.preventDefault();
                return false;
            }
        });
    });

    // ---------------------------------------------------------------
    // Funções
    // ---------------------------------------------------------------

    /**
     * Adiciona uma nova linha de pagamento ao container.
     */
    function adicionarLinha() {
        const template = document.getElementById('templateLinhaPagamento');
        const clone    = template.content.cloneNode(true);
        document.getElementById('listaPagamentos').appendChild(clone);
        aplicarMascaraMoeda();
    }

    /**
     * Aplica máscara de

---

## Checklist de Implementação
- [ ] Revisar os arquivos indicados acima
- [ ] Implementar as alterações descritas
- [ ] Testar localmente
- [ ] Commitar com mensagem descritiva
- [ ] Abrir Pull Request no GitHub

---
*Gerado automaticamente pelo Jarvis em 24/03/2026 13:23*
