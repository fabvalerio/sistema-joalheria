<?php
// Garante que não há saída antes do início da sessão
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inicia a sessão apenas se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está autenticado
if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
    // Redireciona para a página de login se não estiver autenticado
    $url = "https://" . $_SERVER['HTTP_HOST'] . "/sistema-joias/";
    header("Location: " . $url . "login.php");
    exit();
}

$dir = '../../';

// Incluir arquivos necessários APÓS verificar a sessão
include $dir.'db/db.class.php';
include $dir.'App/php/htaccess.php';
include $dir.'App/php/function.php';
include $dir.'App/php/notify.php';

// Controlador e ação padrão
$controller = $_GET['controller'] ?? 'Home';
$action = $_GET['action'] ?? 'index';

// Finaliza o buffer de saída para evitar erros
ob_end_flush();


use App\Models\Orcamento\Controller;

$controller = new Controller();
$id = $_GET['id'] ?? null;

if (!$id) {
    echo notify('danger', 'ID do pedido não informado.');
    exit;
}

$dados = $controller->ver($id);
$pedido = $dados['pedido'];
$itens = $dados['itens'];

// Buscar dados da loja (você pode ajustar conforme necessário)
$loja = [
    'nome' => 'JOALHERIA GONCALVES',
    'endereco' => 'Rua Monsenhor João Soares, 143 - Centro',
    'cidade' => 'Sorocaba - SP',
    'cep' => 'CEP:18010-000',
    'telefones' => '(15)97404-9700 / (15)99186-7699 / (15)97405-0267 / (15)97405-0593',
    'email' => 'contato@goncalvesjoias.com.br'
];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Pedido #<?= $id ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            text-transform: uppercase;
        }

        body {
            font-family: 'Courier New', monospace;
            background-color: #f5f5f5;
            padding: 20px;
            font-weight: 600;
        }

        .receipt-container {
            width: 80mm;
            background-color: white;
            margin: 0 auto;
            padding: 5mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .receipt-header {
            /* text-align: center; */
            margin-bottom: 10px;
            border-bottom: 2px dashed #000;
            padding-bottom: 8px;
        }

        .receipt-header h1 {
            font-size: 18px;
            font-weight: 900;
            margin-bottom: 2px;
            line-height: 1.2;
        }

        .receipt-header p {
            font-size: 12px;
            margin: 2px 0;
            line-height: 1.3;
        }

        .section-separator {
            /* border-bottom: 1px dashed #000; */
            margin: 8px 0;
            /* padding-bottom: 8px; */
        }

        .receipt-info {
            font-size: 12px;
            margin-bottom: 8px;
        }

        .receipt-info-row {
            display: flex;
            /* justify-content: space-between; */
            margin: 3px 0;
            line-height: 1.3;
        }

        /* .receipt-info-label {
            font-weight: bold;
            text-align: left;
            flex: 0 0 45%;
        } */

        /* .receipt-info-value {
            text-align: right;
            flex: 1;
        } */

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 8px 0 4px 0;
            text-align: left;
            text-decoration: underline;
        }

        .items-table {
            width: 100%;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .items-table th {
            border-bottom: 1px dashed #000;
            padding: 3px 0;
            text-align: left;
            font-weight: bold;
        }

        .items-table td {
            padding: 3px 2px;
            border-bottom: 1px solid #eee;
        }

        .items-table tr:last-child td {
            border-bottom: 1px dashed #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            font-size: 12px;
            margin: 8px 0;
        }

        .totals-row {
            display: flex;
            /* justify-content: space-between; */
            margin: 2px 0;
            line-height: 1.4;
        }

        .totals-label {
            font-weight: bold;
        }

        .totals-value {
            font-weight: bold;
        }

        .total-final {
            font-size: 12px;
            font-weight: bold;
            border-top: 2px dashed #000;
            border-bottom: 2px dashed #000;
            padding: 4px 0;
            text-align: right;
            margin: 4px 0;
        }

        .observations {
            font-size: 9px;
            margin: 8px 0;
            padding: 4px;
            border: 1px dashed #000;
            /* min-height: 40px; */
            line-height: 1.3;
        }

        .footer {
            font-size: 9px;
            text-align: center;
            margin-top: 8px;
            line-height: 1.4;
        }

        .signature-line {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 9px;
        }

        @media print {
            body {
                background-color: white;
                padding: 0;
                margin: 0;
            }

            .receipt-container {
                /* width: 80mm; */
                /* padding: 10mm; */
                margin: 0;
                box-shadow: none;
                border: none;
                page-break-after: always;
            }

            @page {
                /* size: 80mm auto; */
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <h1><?= $loja['nome'] ?></h1>
            <p><?= $loja['endereco'] ?> <?= $loja['cidade'] ?></p>
            <p><?= $loja['cep'] ?></p>
            <div style="font-size: 11px;display: flex;flex-direction: row;">
                <div>FONE:</div>
                <div><?= $loja['telefones'] ?></div>
            </div>
            <p style="font-size: 11px; text-transform: lowercase;">E-mail: <?= $loja['email'] ?></p>
        </div>

        <p style="text-align: center; font-size: 10px;">--- Via da loja ---</p>

        <!-- Pedido Info -->
        <div class="section-separator">
            <div class="receipt-info">
                <div class="receipt-info-row">
                    <div class="receipt-info-label">PEDIDO:</div>
                    <div class="receipt-info-value"><?= str_pad($id, 6, '0', STR_PAD_LEFT) ?></div>
                </div>
                <div class="receipt-info-row">
                    <div class="receipt-info-label">DATA PEDIDO:</div>
                    <div class="receipt-info-value"><?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?></div>
                </div>
                <?php if (!empty($pedido['data_entrega'])): ?>
                <div class="receipt-info-row">
                    <div class="receipt-info-label">DATA ENTREGA:</div>
                    <div class="receipt-info-value"><?= subtrairDiasUteis(date('d/m/Y', strtotime($pedido['data_entrega'])), 2) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cliente Info -->
        <div class="receipt-info">
            <div class="receipt-info-row">
                <div class="receipt-info-label">CLIENTE:</div>
                <div class="receipt-info-value">
                    <?= $pedido['idCliente'] ?? '' ?> - 
                    <?= htmlspecialchars(
                        !empty($pedido['nome_pf']) 
                        ? $pedido['nome_pf'] 
                        : ($pedido['nome_fantasia_pj'] ?? 'Não informado')
                    ) ?>
                </div>
            </div>
            <div class="receipt-info-row">
                <div class="receipt-info-label">TELEFONE:</div>
                <div class="receipt-info-value">
                    <?= $pedido['telefone'] ?? '----'; ?>
                </div>
            </div>
            <?php if (!empty($pedido['whatsapp'])): ?>
            <div class="receipt-info-row">
                <div class="receipt-info-label">Whatsapp:</div>
                <div class="receipt-info-value">
                    <?= $pedido['whatsapp'] ?? '----'; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($pedido['forma_pagamento'])): ?>
            <div class="receipt-info-row">
                <div class="receipt-info-label">PAGAMENTO:</div>
                <div class="receipt-info-value"><?= htmlspecialchars($pedido['forma_pagamento']) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Items -->
        <div class="section-separator">
            <h3 class="section-title">DESCRIÇÃO</h3>
            
            <div style="font-size: 12px;">
                <?php foreach ($itens as $item): 
                        $subtotal = ($item['quantidade'] * $item['valor_unitario']) * (1 - ($item['desconto_percentual'] / 100));
                    ?>
                        <?= number_format($item['quantidade'], 2, ',', '.') ?>
                        &nbsp;&nbsp;&nbsp;
                        <?= htmlspecialchars($item['nome_produto'] ?? $item['descricao_produto']) ?>
                        <br>
                        R$<?= number_format($subtotal, 2, ',', '.') ?>
                        <br>
                <?php endforeach; ?>
            </div>

        </div>

        

        <!-- Observations -->
        <?php if (!empty($pedido['observacoes'])): ?>
        <div class="section-separator">
            <div class="observations">
                <?= nl2br(htmlspecialchars($pedido['observacoes'])) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Totals -->
        <div class="totals">
            <div class="totals-row">
                <span class="totals-label">SUB-TOTAL:</span>
                <span class="totals-value">R$<?= number_format($pedido['total'], 2, ',', '.') ?></span>
            </div>
            <?php if (!empty($pedido['desconto']) && $pedido['desconto'] > 0): ?>
            <div class="totals-row">
                <span class="totals-label">DESC./ACRÉSC.:</span>
                <span class="totals-value">-<?= number_format($pedido['desconto'], 2, ',', '.') ?>%</span>
            </div>
            <?php endif; ?>
            <div class="total-final">
                <div class="totals-row">
                    <span>TOTAL COMANDO:</span>
                    <span>R$<?= number_format($pedido['total'], 2, ',', '.') ?></span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>CONCORDO COM O SERVIÇO ACIMA DESCRIMINADO.</p>
            <div class="signature-line">
                Assinatura do Cliente
            </div>
            <p style="margin-top: 8px; font-size: 8px;">
                - NÃO ENTREGAREMOS AS JOIAS SEM ESTE COMPROVANTE.<br>
                - NÃO NOS RESPONSABILIZAMOS POR ELE APÓS 90 DIAS DA DATA DE CONCLUSÃO DO SERVIÇO.
            </p>
        </div>

    </div>

    <div class="receipt-container">
        <!-- Header -->

        <div class="receipt-header">
            <h1><?= $loja['nome'] ?></h1>
            <p><?= $loja['endereco'] ?> <?= $loja['cidade'] ?></p>
            <p><?= $loja['cep'] ?></p>
            <div style="font-size: 11px;display: flex;flex-direction: row;">
                <div>FONE:</div>
                <div><?= $loja['telefones'] ?></div>
            </div>
            <p style="font-size: 11px; text-transform: lowercase;">E-mail: <?= $loja['email'] ?></p>
        </div>

        
        <p style="text-align: center; font-size: 10px;">--- Via do Cliente ---</p>

        <!-- Pedido Info -->
        <div class="section-separator">
            <div class="receipt-info">
                <div class="receipt-info-row">
                    <div class="receipt-info-label">PEDIDO:</div>
                    <div class="receipt-info-value"><?= str_pad($id, 6, '0', STR_PAD_LEFT) ?></div>
                </div>
                <div class="receipt-info-row">
                    <div class="receipt-info-label">DATA PEDIDO:</div>
                    <div class="receipt-info-value"><?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?></div>
                </div>
                <?php if (!empty($pedido['data_entrega'])): ?>
                <div class="receipt-info-row">
                    <div class="receipt-info-label">DATA ENTREGA:</div>
                    <div class="receipt-info-value"><?= date('d/m/Y', strtotime($pedido['data_entrega'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cliente Info -->
        <div class="receipt-info">
            <div class="receipt-info-row">
                <div class="receipt-info-label">CLIENTE:</div>
                <div class="receipt-info-value">
                    <?= $pedido['idCliente'] ?? '' ?> - 
                    <?= htmlspecialchars(
                        !empty($pedido['nome_pf']) 
                        ? $pedido['nome_pf'] 
                        : ($pedido['nome_fantasia_pj'] ?? 'Não informado')
                    ) ?>
                </div>
            </div>
            <div class="receipt-info-row">
                <div class="receipt-info-label">TELEFONE:</div>
                <div class="receipt-info-value">
                    <?= $pedido['telefone'] ?? '----'; ?>
                </div>
            </div>
            <?php if (!empty($pedido['whatsapp'])): ?>
            <div class="receipt-info-row">
                <div class="receipt-info-label">Whatsapp:</div>
                <div class="receipt-info-value">
                    <?= $pedido['whatsapp'] ?? '----'; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($pedido['forma_pagamento'])): ?>
            <div class="receipt-info-row">
                <div class="receipt-info-label">PAGAMENTO:</div>
                <div class="receipt-info-value"><?= htmlspecialchars($pedido['forma_pagamento']) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Items -->
        <div class="section-separator">
            <h3 class="section-title">DESCRIÇÃO</h3>
            
            <div style="font-size: 12px;">
                <?php foreach ($itens as $item): 
                        $subtotal = ($item['quantidade'] * $item['valor_unitario']) * (1 - ($item['desconto_percentual'] / 100));
                    ?>
                        <?= number_format($item['quantidade'], 2, ',', '.') ?>
                        &nbsp;&nbsp;&nbsp;
                        <?= htmlspecialchars($item['nome_produto'] ?? $item['descricao_produto']) ?>
                        <br>
                        R$<?= number_format($subtotal, 2, ',', '.') ?>
                        <br>
                <?php endforeach; ?>
            </div>

        </div>

        

        <!-- Observations -->
        <?php if (!empty($pedido['observacoes'])): ?>
        <div class="section-separator">
            <div class="observations">
                <?= nl2br(htmlspecialchars($pedido['observacoes'])) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Totals -->
        <div class="totals">
            <div class="totals-row">
                <span class="totals-label">SUB-TOTAL:</span>
                <span class="totals-value">R$<?= number_format($pedido['total'], 2, ',', '.') ?></span>
            </div>
            <?php if (!empty($pedido['desconto']) && $pedido['desconto'] > 0): ?>
            <div class="totals-row">
                <span class="totals-label">DESC./ACRÉSC.:</span>
                <span class="totals-value">-<?= number_format($pedido['desconto'], 2, ',', '.') ?>%</span>
            </div>
            <?php endif; ?>
            <div class="total-final">
                <div class="totals-row">
                    <span>TOTAL COMANDO:</span>
                    <span>R$<?= number_format($pedido['total'], 2, ',', '.') ?></span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>CONCORDO COM O SERVIÇO ACIMA DESCRIMINADO.</p>
            <div class="signature-line">
                Assinatura do Cliente
            </div>
            <p style="margin-top: 8px; font-size: 8px;">
                - NÃO ENTREGAREMOS AS JOIAS SEM ESTE COMPROVANTE.<br>
                - NÃO NOS RESPONSABILIZAMOS POR ELE APÓS 90 DIAS DA DATA DE CONCLUSÃO DO SERVIÇO.
            </p>
        </div>
    </div>



    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
