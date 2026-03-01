<?php
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
    $url = "https://" . $_SERVER['HTTP_HOST'] . "/sistema-joias/";
    header("Location: " . $url . "login.php");
    exit();
}

$dir = '../../';

include $dir.'db/db.class.php';
include $dir.'App/php/htaccess.php';
include $dir.'App/php/function.php';
include $dir.'App/php/notify.php';

ob_end_flush();

use App\Models\Pedidos\Controller;

$controller = new Controller();
$id = $_GET['id'] ?? null;
$via = $_GET['via'] ?? 'ambas'; // 'ambas', 'loja' ou 'cliente'

if (!$id) {
    echo notify('danger', 'ID do pedido não informado.');
    exit;
}

$dados = $controller->ver($id);
$pedido = $dados['pedido'];
$itens = $dados['itens'];

$loja = [
    'nome' => 'JOALHERIA GONCALVES',
    'endereco' => 'Rua Monsenhor João Soares, 143 - Centro',
    'cidade' => 'Sorocaba - SP',
    'cep' => 'CEP:18010-000',
    'telefones' => '(15)97404-9700 / (15)99186-7699 / (15)97405-0267 / (15)97405-0593',
    'email' => 'contato@goncalvesjoias.com.br'
];

$soloCliente = ($via === 'cliente');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Pedido de Venda #<?= $id ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; text-transform: uppercase; }
        body { font-family: 'Courier New', monospace; background-color: #f5f5f5; padding: 20px; font-weight: 600; }
        .receipt-container { width: 80mm; background-color: white; margin: 0 auto; padding: 5mm; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); border: 1px solid #ddd; }
        .receipt-header { margin-bottom: 10px; border-bottom: 2px dashed #000; padding-bottom: 8px; }
        .receipt-header h1 { font-size: 18px; font-weight: 900; margin-bottom: 2px; line-height: 1.2; }
        .receipt-header p { font-size: 12px; margin: 2px 0; line-height: 1.3; }
        .section-separator { margin: 8px 0; }
        .receipt-info { font-size: 12px; margin-bottom: 8px; }
        .receipt-info-row { display: flex; margin: 3px 0; line-height: 1.3; }
        .section-title { font-size: 12px; font-weight: bold; margin: 8px 0 4px 0; text-align: left; text-decoration: underline; }
        .items-table { width: 100%; font-size: 12px; margin-bottom: 8px; }
        .items-table th { border-bottom: 1px dashed #000; padding: 3px 0; text-align: left; font-weight: bold; }
        .items-table td { padding: 3px 2px; border-bottom: 1px solid #eee; }
        .items-table tr:last-child td { border-bottom: 1px dashed #000; }
        .text-right { text-align: right; }
        .totals { font-size: 12px; margin: 8px 0; }
        .totals-row { display: flex; margin: 2px 0; line-height: 1.4; }
        .totals-label { font-weight: bold; }
        .totals-value { font-weight: bold; }
        .total-final { font-size: 12px; font-weight: bold; border-top: 2px dashed #000; border-bottom: 2px dashed #000; padding: 4px 0; text-align: right; margin: 4px 0; }
        .observations { font-size: 9px; margin: 8px 0; padding: 4px; border: 1px dashed #000; line-height: 1.3; }
        .footer { font-size: 9px; text-align: center; margin-top: 8px; line-height: 1.4; }
        @media print {
            .no-print { display: none !important; }
            .receipt-container { box-shadow: none !important; border: none !important; }
            hr.corte { display: block; border: none; margin: 0; padding: 0; height: 0; page-break-after: always; break-after: page; }
            @page { margin: 0; }
        }
        hr.corte { border: none; border-top: 2px dashed red; margin: 20px 0; }
    </style>
</head>
<body>
    <?php if (!$soloCliente): ?>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1><?= $loja['nome'] ?></h1>
            <p><?= $loja['endereco'] ?> <?= $loja['cidade'] ?></p>
            <p><?= $loja['cep'] ?></p>
            <div style="font-size: 11px;display: flex;flex-direction: row;"><div>FONE:</div><div><?= $loja['telefones'] ?></div></div>
            <p style="font-size: 11px; text-transform: lowercase;">E-mail: <?= $loja['email'] ?></p>
        </div>
        <p style="text-align: center; font-size: 10px;">--- PEDIDO DE VENDA - Via da Loja ---</p>

        <div class="section-separator">
            <div class="receipt-info">
                <div class="receipt-info-row"><div>PEDIDO:</div><div><?= str_pad($id, 6, '0', STR_PAD_LEFT) ?></div></div>
                <div class="receipt-info-row"><div>DATA:</div><div><?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?></div></div>
                <?php if (!empty($pedido['data_entrega'])): ?>
                <div class="receipt-info-row"><div>ENTREGA:</div><div><?= date('d/m/Y', strtotime($pedido['data_entrega'])) ?></div></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="receipt-info">
            <div class="receipt-info-row"><div>CLIENTE:</div><div><?= $pedido['idCliente'] ?? '' ?> - <?= htmlspecialchars(!empty($pedido['nome_pf']) ? $pedido['nome_pf'] : ($pedido['nome_fantasia_pj'] ?? 'Não informado')) ?></div></div>
            <?php if (!empty($pedido['telefone'])): ?>
            <div class="receipt-info-row"><div>TELEFONE:</div><div><?= $pedido['telefone'] ?></div></div>
            <?php endif; ?>
            <?php if (!empty($pedido['whatsapp'])): ?>
            <div class="receipt-info-row"><div>Whatsapp:</div><div><?= $pedido['whatsapp'] ?></div></div>
            <?php endif; ?>
            <?php if (!empty($pedido['forma_pagamento'])): ?>
            <div class="receipt-info-row"><div>PAGAMENTO:</div><div><?= htmlspecialchars($pedido['forma_pagamento']) ?></div></div>
            <?php endif; ?>
        </div>

        <div class="section-separator">
            <h3 class="section-title">PRODUTOS</h3>
            <div style="font-size: 12px;">
                <?php foreach ($itens as $item):
                    $subtotal = ($item['quantidade'] * $item['valor_unitario']) * (1 - (($item['desconto_percentual'] ?? 0) / 100));
                    $nome = $item['nome_produto'] ?? $item['descricao_produto'] ?? '-';
                ?>
                    <?= number_format($item['quantidade'], 2, ',', '.') ?> &nbsp; <?= htmlspecialchars($nome) ?><br>
                    R$<?= number_format($subtotal, 2, ',', '.') ?><br>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!empty($pedido['observacoes'])): ?>
        <div class="section-separator">
            <div class="observations"><?= nl2br(htmlspecialchars($pedido['observacoes'])) ?></div>
        </div>
        <?php endif; ?>

        <div class="totals">
            <div class="totals-row"><span class="totals-label">SUB-TOTAL:</span><span class="totals-value">R$<?= number_format($pedido['total'], 2, ',', '.') ?></span></div>
            <?php if (!empty($pedido['desconto']) && $pedido['desconto'] > 0): ?>
            <div class="totals-row"><span class="totals-label">DESC.:</span><span class="totals-value">-<?= number_format($pedido['desconto'], 2, ',', '.') ?>%</span></div>
            <?php endif; ?>
            <div class="total-final">
                <div class="totals-row"><span>TOTAL:</span><span>R$<?= number_format($pedido['total'], 2, ',', '.') ?></span></div>
            </div>
        </div>

        <div class="footer">
            <p>PEDIDO DE VENDA - NÃO É DOCUMENTO FISCAL</p>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!$soloCliente): ?><hr class="corte"><?php endif; ?>

    <div class="receipt-container">
        <div class="receipt-header">
            <h1><?= $loja['nome'] ?></h1>
            <p><?= $loja['endereco'] ?> <?= $loja['cidade'] ?></p>
            <p><?= $loja['cep'] ?></p>
            <div style="font-size: 11px;display: flex;flex-direction: row;"><div>FONE:</div><div><?= $loja['telefones'] ?></div></div>
            <p style="font-size: 11px; text-transform: lowercase;">E-mail: <?= $loja['email'] ?></p>
        </div>
        <p style="text-align: center; font-size: 10px;">--- PEDIDO DE VENDA - Via do Cliente ---</p>

        <div class="section-separator">
            <div class="receipt-info">
                <div class="receipt-info-row"><div>PEDIDO:</div><div><?= str_pad($id, 6, '0', STR_PAD_LEFT) ?></div></div>
                <div class="receipt-info-row"><div>DATA:</div><div><?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?></div></div>
                <?php if (!empty($pedido['data_entrega'])): ?>
                <div class="receipt-info-row"><div>ENTREGA:</div><div><?= date('d/m/Y', strtotime($pedido['data_entrega'])) ?></div></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="receipt-info">
            <div class="receipt-info-row"><div>CLIENTE:</div><div><?= $pedido['idCliente'] ?? '' ?> - <?= htmlspecialchars(!empty($pedido['nome_pf']) ? $pedido['nome_pf'] : ($pedido['nome_fantasia_pj'] ?? 'Não informado')) ?></div></div>
            <?php if (!empty($pedido['telefone'])): ?>
            <div class="receipt-info-row"><div>TELEFONE:</div><div><?= $pedido['telefone'] ?></div></div>
            <?php endif; ?>
            <?php if (!empty($pedido['forma_pagamento'])): ?>
            <div class="receipt-info-row"><div>PAGAMENTO:</div><div><?= htmlspecialchars($pedido['forma_pagamento']) ?></div></div>
            <?php endif; ?>
        </div>

        <div class="section-separator">
            <h3 class="section-title">PRODUTOS</h3>
            <div style="font-size: 12px;">
                <?php foreach ($itens as $item):
                    $subtotal = ($item['quantidade'] * $item['valor_unitario']) * (1 - (($item['desconto_percentual'] ?? 0) / 100));
                    $nome = $item['nome_produto'] ?? $item['descricao_produto'] ?? '-';
                ?>
                    <?= number_format($item['quantidade'], 2, ',', '.') ?> &nbsp; <?= htmlspecialchars($nome) ?><br>
                    R$<?= number_format($subtotal, 2, ',', '.') ?><br>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!empty($pedido['observacoes'])): ?>
        <div class="section-separator">
            <div class="observations"><?= nl2br(htmlspecialchars($pedido['observacoes'])) ?></div>
        </div>
        <?php endif; ?>

        <div class="totals">
            <div class="totals-row"><span class="totals-label">SUB-TOTAL:</span><span class="totals-value">R$<?= number_format($pedido['total'], 2, ',', '.') ?></span></div>
            <div class="total-final">
                <div class="totals-row"><span>TOTAL:</span><span>R$<?= number_format($pedido['total'], 2, ',', '.') ?></span></div>
            </div>
        </div>

        <div class="footer">
            <p>PEDIDO DE VENDA - NÃO É DOCUMENTO FISCAL</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <a href="<?= $url ?? '' ?>!/Pedidos/listar" class="btn btn-secondary">Voltar para Pedidos</a>
    </div>

    <script>window.onload = function() { window.print(); };</script>
</body>
</html>
