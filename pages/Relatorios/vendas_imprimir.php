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

$controller = $_GET['controller'] ?? 'Home';
$action = $_GET['action'] ?? 'index';

ob_end_flush();

use App\Models\Relatorios\Controller;

$tipo = $_GET['tipo'] ?? null;
$inicio = $_GET['data_inicio'] ?? null;
$fim = $_GET['data_final'] ?? null;
$vendedor_id = $_GET['vendedor_id'] ?? null;

$controller = new Controller();

$contas = $controller->vendasParaImprimir($tipo, $inicio, $fim, $vendedor_id);
$r = $controller->somaVendas($inicio, $fim);

$porVendedor = [];
if (!empty($contas) && is_array($contas)) {
    foreach ($contas as $conta) {
        $vendedor = $conta['vendedor_nome'] ?? 'SEM VENDEDOR';
        $porVendedor[$vendedor][] = $conta;
    }
}

$totalGeral = ['valor' => 0, 'dinheiro' => 0, 'cheque' => 0, 'cartao' => 0, 'ouro' => 0, 'carne' => 0, 'deposito' => 0, 'comissao' => 0];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Vendas - Impressão</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 15px;
            font-size: 11px;
            color: #000;
        }
        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .info-filtros {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        .info-filtros p {
            margin: 3px 0;
        }
        .vendedor-nome {
            font-weight: bold;
            font-style: italic;
            font-size: 12px;
            margin: 15px 0 3px 0;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: left;
            font-size: 10px;
        }
        table th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }
        td.numero {
            text-align: right;
        }
        tr.subtotal td {
            font-weight: bold;
            border-top: 2px solid #000;
        }
        tr.total-geral td {
            font-weight: bold;
            font-size: 11px;
            border-top: 3px double #000;
        }
        .rodape {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 5mm; }
        }
        .btn-imprimir {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            font-size: 13px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .btn-imprimir:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <button class="btn-imprimir no-print" onclick="window.print()">🖨️ Imprimir</button>
    
    <h1>Relatório de Vendas</h1>
    
    <div class="info-filtros">
        <p><strong>Período:</strong> 
            <?php 
                if (!empty($inicio) && !empty($fim)) {
                    echo date('d/m/Y', strtotime($inicio)) . ' até ' . date('d/m/Y', strtotime($fim));
                } else {
                    echo 'Todos os períodos';
                }
            ?>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Tipo:</strong> <?php echo !empty($tipo) ? $tipo : 'Todos'; ?>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Emissão:</strong> <?php echo date('d/m/Y H:i:s'); ?>
        </p>
    </div>

    <?php if (!empty($porVendedor)): ?>
        <?php foreach ($porVendedor as $nomeVendedor => $vendas): ?>
            <?php
                $subValor = 0; $subDinheiro = 0; $subCheque = 0; $subCartao = 0;
                $subOuro = 0; $subCarne = 0; $subDeposito = 0; $subComissao = 0;
            ?>
            <div class="vendedor-nome"><?php echo strtoupper($nomeVendedor); ?></div>
            <table>
                <thead>
                    <tr>
                        <th width="60">Pedido</th>
                        <th>Cliente</th>
                        <th width="70">Valor</th>
                        <th width="65">Dinheiro</th>
                        <th width="60">Cheque</th>
                        <th width="65">Cartão</th>
                        <th width="55">Ouro</th>
                        <th width="55">Carnê</th>
                        <th width="65">Pix</th>
                        <th width="65">Comissão</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vendas as $venda): ?>
                        <?php
                            $valor = (float)($venda['total'] ?? 0);
                            $din = (float)($venda['dinheiro'] ?? 0);
                            $chq = (float)($venda['cheque'] ?? 0);
                            $crt = (float)($venda['cartao'] ?? 0);
                            $our = (float)($venda['ouro'] ?? 0);
                            $crn = (float)($venda['carne'] ?? 0);
                            $dep = (float)($venda['deposito'] ?? 0);
                            $com = (float)($venda['comissao'] ?? 0);

                            $subValor += $valor;
                            $subDinheiro += $din;
                            $subCheque += $chq;
                            $subCartao += $crt;
                            $subOuro += $our;
                            $subCarne += $crn;
                            $subDeposito += $dep;
                            $subComissao += $com;
                        ?>
                        <tr>
                            <td><?php echo str_pad($venda['id'], 6, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo ($venda['nome_pf'] ?? $venda['nome_fantasia_pj'] ?? 'N/A'); ?></td>
                            <td class="numero"><?php echo number_format($valor, 2, ',', '.'); ?></td>
                            <td class="numero"><?php echo number_format($din, 2, ',', '.'); ?></td>
                            <td class="numero"><?php echo number_format($chq, 2, ',', '.'); ?></td>
                            <td class="numero"><?php echo number_format($crt, 2, ',', '.'); ?></td>
                            <td class="numero"><?php echo number_format($our, 2, ',', '.'); ?></td>
                            <td class="numero"><?php echo number_format($crn, 2, ',', '.'); ?></td>
                            <td class="numero"><?php echo number_format($dep, 2, ',', '.'); ?></td>
                            <td class="numero"><?php echo number_format($com, 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="subtotal">
                        <td colspan="2"></td>
                        <td class="numero"><?php echo number_format($subValor, 2, ',', '.'); ?></td>
                        <td class="numero"><?php echo number_format($subDinheiro, 2, ',', '.'); ?></td>
                        <td class="numero"><?php echo number_format($subCheque, 2, ',', '.'); ?></td>
                        <td class="numero"><?php echo number_format($subCartao, 2, ',', '.'); ?></td>
                        <td class="numero"><?php echo number_format($subOuro, 2, ',', '.'); ?></td>
                        <td class="numero"><?php echo number_format($subCarne, 2, ',', '.'); ?></td>
                        <td class="numero"><?php echo number_format($subDeposito, 2, ',', '.'); ?></td>
                        <td class="numero"><?php echo number_format($subComissao, 2, ',', '.'); ?></td>
                    </tr>
                </tbody>
            </table>
            <?php
                $totalGeral['valor'] += $subValor;
                $totalGeral['dinheiro'] += $subDinheiro;
                $totalGeral['cheque'] += $subCheque;
                $totalGeral['cartao'] += $subCartao;
                $totalGeral['ouro'] += $subOuro;
                $totalGeral['carne'] += $subCarne;
                $totalGeral['deposito'] += $subDeposito;
                $totalGeral['comissao'] += $subComissao;
            ?>
        <?php endforeach; ?>

        <table>
            <tr class="total-geral">
                <td style="text-align: center;"><strong>Total</strong></td>
                <td style="text-align: center;"><strong>Pagamentos</strong></td>
                <td class="numero">Total: R$ <?php echo number_format($totalGeral['valor'], 2, ',', '.'); ?></td>
                <td class="numero">Dinheiro: R$ <?php echo number_format($totalGeral['dinheiro'], 2, ',', '.'); ?></td>
                <td class="numero">Cheque: R$ <?php echo number_format($totalGeral['cheque'], 2, ',', '.'); ?></td>
                <td class="numero">Cartão: R$ <?php echo number_format($totalGeral['cartao'], 2, ',', '.'); ?></td>
                <td class="numero">Ouro: R$ <?php echo number_format($totalGeral['ouro'], 2, ',', '.'); ?></td>
                <td class="numero">Carnê: R$ <?php echo number_format($totalGeral['carne'], 2, ',', '.'); ?></td>
                <td class="numero">Pix: R$ <?php echo number_format($totalGeral['deposito'], 2, ',', '.'); ?></td>
                <td class="numero">Comissão: R$ <?php echo number_format($totalGeral['comissao'], 2, ',', '.'); ?></td>
            </tr>
        </table>
    <?php else: ?>
        <p style="text-align: center; margin-top: 30px;">Nenhuma venda encontrada para os filtros selecionados.</p>
    <?php endif; ?>
    
    <div class="rodape">
        <p>Relatório gerado automaticamente pelo Sistema de Joalheria</p>
    </div>
</body>
</html>
