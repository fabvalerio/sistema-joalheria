<?php

require_once dirname(__DIR__, 2) . '/db/db.class.php';

header('Content-Type: application/json');

$busca = trim($_GET['busca'] ?? '');
$limite = 50;

$db = new db();

if (!empty($busca)) {
    $db->query("
        SELECT 
            p.id, 
            p.descricao_etiqueta AS nome_produto, 
            p.em_reais AS preco, 
            e.quantidade AS estoque, 
            p.capa,
            c.valor AS cotacao_valor,
            p.peso_gr,
            p.margem,
            p.preco_ql
        FROM 
            produtos p
        LEFT JOIN cotacoes c ON p.cotacao = c.id
        LEFT JOIN estoque e ON p.id = e.produtos_id
        WHERE 
            p.insumo = 1
            AND (p.descricao_etiqueta LIKE :busca OR p.id LIKE :busca2)
        ORDER BY 
            p.descricao_etiqueta ASC
        LIMIT $limite
    ");
    $db->bind(':busca', '%' . $busca . '%');
    $db->bind(':busca2', '%' . $busca . '%');
} else {
    $db->query("
        SELECT 
            p.id, 
            p.descricao_etiqueta AS nome_produto, 
            p.em_reais AS preco, 
            e.quantidade AS estoque, 
            p.capa,
            c.valor AS cotacao_valor,
            p.peso_gr,
            p.margem,
            p.preco_ql
        FROM 
            produtos p
        LEFT JOIN cotacoes c ON p.cotacao = c.id
        LEFT JOIN estoque e ON p.id = e.produtos_id
        WHERE p.insumo = 1
        ORDER BY 
            p.descricao_etiqueta ASC
        LIMIT $limite
    ");
}

$produtos = $db->resultSet();

// Mesma fórmula de App/php/function.php -> cotacao()
function calcularPreco($preco_ql, $peso_gr, $cotacao_valor, $margem) {
    return ($preco_ql * $peso_gr * $cotacao_valor) * (1 + $margem / 100);
}

$resultado = [];
foreach ($produtos as $produto) {
    $preco = calcularPreco(
        $produto['preco_ql'],
        $produto['peso_gr'],
        $produto['cotacao_valor'],
        $produto['margem']
    );

    $resultado[] = [
        'id'           => $produto['id'],
        'nome_produto' => $produto['nome_produto'],
        'preco'        => $preco > 0 ? $preco : $produto['preco'],
        'estoque'      => $produto['estoque'],
        'capa'         => $produto['capa'],
    ];
}

echo json_encode($resultado);
