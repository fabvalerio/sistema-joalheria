<?php

require_once dirname(__DIR__, 2) . '/db/db.class.php';

header('Content-Type: application/json; charset=utf-8');

// Verifica autenticação
if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

// Módulo CD: apenas Administrador
if (($_COOKIE['nivel_acesso'] ?? '') !== 'Administrador') {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

use App\Models\Estoque\Controller;

$controller = new Controller();

// Tabela estoque = estoque do CD (principal)
$estoque = $controller->estoquePrincipal();

$data = [];
foreach ($estoque as $item) {
    $qtdMin = (float)($item['quantidade_minima'] ?? 0);
    $status = ($qtdMin > 0 && (float)$item['quantidade'] <= $qtdMin) ? 'Baixo' : 'OK';

    $data[] = [
        'loja_nome' => $item['loja_nome'] ?? '-',
        'produto_id' => $item['produto_id'],
        'codigo_formatado' => str_pad($item['produto_id'], 6, '0', STR_PAD_LEFT),
        'nome_produto' => $item['nome_produto'] ?? '-',
        'quantidade' => (float)$item['quantidade'],
        'quantidade_formatada' => number_format((float)$item['quantidade'], 0, ',', '.'),
        'quantidade_minima' => $item['quantidade_minima'] ? (float)$item['quantidade_minima'] : null,
        'quantidade_minima_formatada' => $item['quantidade_minima'] ? number_format((float)$item['quantidade_minima'], 0, ',', '.') : '-',
        'status' => $status
    ];
}

echo json_encode(['data' => $data]);
