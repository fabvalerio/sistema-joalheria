<?php
require_once dirname(__DIR__, 2) . '/db/db.class.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'msg' => 'Não autorizado']);
        exit;
    }

    $q = trim($_POST['q'] ?? $_GET['q'] ?? '');
    $q = (string)$q;

    $limitRaw = (int)($_POST['limit'] ?? $_GET['limit'] ?? 50);
    $limit = max(1, min(100, $limitRaw));

    $isNumeric = preg_match('/^\d+$/', $q) === 1;
    $like = '%' . $q . '%';

    $db = new db();

    if ($q === '') {
        $db->query("
            SELECT 
                p.id,
                p.descricao_etiqueta AS nome_produto,
                e.quantidade AS estoque
            FROM produtos p
            LEFT JOIN estoque e ON p.id = e.produtos_id
            ORDER BY p.descricao_etiqueta ASC
            LIMIT {$limit}
        ");
    } else {
        if ($isNumeric) {
            $db->query("
                SELECT 
                    p.id,
                    p.descricao_etiqueta AS nome_produto,
                    e.quantidade AS estoque
                FROM produtos p
                LEFT JOIN estoque e ON p.id = e.produtos_id
                WHERE p.id = :pid OR p.descricao_etiqueta LIKE :term
                ORDER BY p.descricao_etiqueta ASC
                LIMIT {$limit}
            ");
            $db->bind(':pid', (int)$q);
        } else {
            $db->query("
                SELECT 
                    p.id,
                    p.descricao_etiqueta AS nome_produto,
                    e.quantidade AS estoque
                FROM produtos p
                LEFT JOIN estoque e ON p.id = e.produtos_id
                WHERE p.descricao_etiqueta LIKE :term
                ORDER BY p.descricao_etiqueta ASC
                LIMIT {$limit}
            ");
        }
        $db->bind(':term', $like);
    }

    $rows = $db->resultSet();
    echo json_encode(['ok' => true, 'data' => $rows]);
} catch (\Throwable $e) {
    echo json_encode(['ok' => false, 'msg' => 'Erro: ' . $e->getMessage()]);
}

