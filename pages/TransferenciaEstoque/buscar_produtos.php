<?php

include '../../db/db.class.php';

header('Content-Type: application/json');

$loja_id = $_GET['loja_id'] ?? null;

if (!$loja_id) {
    echo json_encode([]);
    exit;
}

$db = new db();
$db->query("
    SELECT 
        p.id, 
        p.descricao_etiqueta AS nome_produto,
        p.codigo_fabricante,
        el.quantidade AS estoque
    FROM produtos p
    INNER JOIN estoque_loja el ON p.id = el.produto_id
    WHERE el.loja_id = :loja_id AND el.quantidade > 0
    ORDER BY p.descricao_etiqueta ASC
");
$db->bind(':loja_id', $loja_id);
echo json_encode($db->resultSet());
