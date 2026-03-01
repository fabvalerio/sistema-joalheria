<?php

require_once dirname(__DIR__, 2) . '/db/db.class.php';

header('Content-Type: application/json; charset=utf-8');

$loja_id = $_GET['loja_id'] ?? null;
$busca = trim($_GET['busca'] ?? '');
$limite = 500;

if (!$loja_id) {
    echo json_encode(['data' => []]);
    exit;
}

$db = new db();

// Verifica se a loja é CD (estoque principal) ou loja comum (estoque_loja)
$db->query("SELECT id, tipo FROM loja WHERE id = :loja_id");
$db->bind(':loja_id', $loja_id);
$loja = $db->single();
$tipo = (is_array($loja) && isset($loja['tipo'])) ? $loja['tipo'] : '';

$condicao_busca = "";
if (!empty($busca)) {
    $condicao_busca = " AND (p.descricao_etiqueta LIKE :busca OR p.id LIKE :busca2 OR p.codigo_fabricante LIKE :busca3)";
}

if ($tipo === 'CD') {
    // CD usa tabela estoque (produtos_id) - agregar por produto
    $db->query("
        SELECT 
            p.id, 
            p.descricao_etiqueta AS nome_produto,
            p.codigo_fabricante,
            COALESCE(SUM(e.quantidade), 0) AS estoque
        FROM produtos p
        INNER JOIN estoque e ON p.id = e.produtos_id
        WHERE e.quantidade > 0 {$condicao_busca}
        GROUP BY p.id, p.descricao_etiqueta, p.codigo_fabricante
        HAVING estoque > 0
        ORDER BY p.descricao_etiqueta ASC
        LIMIT $limite
    ");
    if (!empty($busca)) {
        $db->bind(':busca', '%' . $busca . '%');
        $db->bind(':busca2', '%' . $busca . '%');
        $db->bind(':busca3', '%' . $busca . '%');
    }
} else {
    // Loja comum usa estoque_loja
    $db->query("
        SELECT 
            p.id, 
            p.descricao_etiqueta AS nome_produto,
            p.codigo_fabricante,
            el.quantidade AS estoque
        FROM produtos p
        INNER JOIN estoque_loja el ON p.id = el.produto_id
        WHERE el.loja_id = :loja_id AND el.quantidade > 0 {$condicao_busca}
        ORDER BY p.descricao_etiqueta ASC
        LIMIT $limite
    ");
    $db->bind(':loja_id', $loja_id);
    if (!empty($busca)) {
        $db->bind(':busca', '%' . $busca . '%');
        $db->bind(':busca2', '%' . $busca . '%');
        $db->bind(':busca3', '%' . $busca . '%');
    }
}

$resultado = $db->resultSet();
$data = [];
foreach ($resultado as $row) {
    $data[] = [
        'id' => $row['id'],
        'nome_produto' => $row['nome_produto'] ?? '-',
        'codigo_fabricante' => $row['codigo_fabricante'] ?? '',
        'estoque' => (float)($row['estoque'] ?? 0)
    ];
}

echo json_encode(['data' => $data]);
