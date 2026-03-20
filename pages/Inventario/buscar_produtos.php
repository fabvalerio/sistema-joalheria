<?php

require_once dirname(__DIR__, 2) . '/db/db.class.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
    http_response_code(401);
    echo json_encode(['data' => []]);
    exit;
}

$busca = trim($_GET['busca'] ?? '');
$limite = 50;

if (strlen($busca) < 1) {
    echo json_encode(['data' => []]);
    exit;
}

$db = new db();
$condicao_busca = "";
$binds = [];

if (!empty($busca)) {
    $condicao_busca = " AND (p.descricao_etiqueta LIKE :busca OR p.id = :busca_id OR p.codigo_fabricante LIKE :busca2)";
    $binds[':busca'] = '%' . $busca . '%';
    $binds[':busca_id'] = is_numeric($busca) ? (int)$busca : 0;
    $binds[':busca2'] = '%' . $busca . '%';
}

$db->query("
    SELECT p.id, p.descricao_etiqueta AS nome_produto, p.codigo_fabricante
    FROM produtos p
    WHERE p.id IS NOT NULL {$condicao_busca}
    ORDER BY p.descricao_etiqueta ASC
    LIMIT " . (int)$limite
);

foreach ($binds as $k => $v) {
    $db->bind($k, $v);
}

$resultado = $db->resultSet();
$data = [];
foreach ($resultado as $row) {
    $data[] = [
        'id' => $row['id'],
        'nome_produto' => $row['nome_produto'] ?? '-',
        'codigo_fabricante' => $row['codigo_fabricante'] ?? '',
        'codigo_formatado' => str_pad($row['id'], 6, '0', STR_PAD_LEFT)
    ];
}

echo json_encode(['data' => $data]);
