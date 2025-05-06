<?php

namespace App\Models\Home;
use db;

class Controller
{
    /*
        KPI FINAICENRIO
        Posição em HOJE
        Recebimentos
        Recebido
        Pagamentos
    */
    public function financeiro()
    {
        $db = new db();
        $db->query("SELECT 
            SUM(CASE WHEN tipo = 'R' AND status = 'Pendente' THEN valor ELSE 0 END) as recebimentos,
            SUM(CASE WHEN tipo = 'R' AND status = 'Pago' THEN valor ELSE 0 END) as recebido,
            SUM(CASE WHEN tipo = 'P' THEN valor ELSE 0 END) as pagamentos
        FROM financeiro_contas 
        WHERE data_vencimento >= CURDATE()");
        return $db->resultSet();
    }


    /*
    Listar 5 produtos mais vendidos da tabela pedidos_itens
     */
    public function produtosMaisVendidos()
    {
        $db = new db();
        $db->query("SELECT descricao_produto, COUNT(*) as total FROM pedidos_itens GROUP BY descricao_produto ORDER BY total DESC LIMIT 4");
        return $db->resultSet();
    }

    /*
    Desempenho de produtos
    */
    /*
    Desempenho de produtos - Retorna os produtos mais vendidos com valor total
    */
    public function desempenhoProdutos()
    {
        $db = new db();
        $db->query("SELECT 
            descricao_produto,
            COUNT(*) as total_vendas,
            SUM(quantidade * valor_unitario) as valor_total,
            MAX(valor_unitario) as valor_maximo,
            MIN(valor_unitario) as valor_minimo
        FROM pedidos_itens 
        GROUP BY descricao_produto
        ORDER BY valor_total DESC 
        LIMIT 3");
        return $db->resultSet();
    }

    /*
    Desempenho de vendas por vendedor
    */
    public function desempenhoVendedores()
    {
        $db = new db();
        $db->query("SELECT 
            u.id as cod_vendedor,
            u.nome_completo,
            COUNT(p.id) as total_vendas,
            COALESCE(SUM(p.total), 0) as valor_total,
            MAX(p.total) as valor_maximo,
            MIN(p.total) as valor_minimo
        FROM usuarios u
        LEFT JOIN pedidos p ON u.id = p.cod_vendedor 
            AND p.data_pedido >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY u.id, u.nome_completo
        ORDER BY valor_total DESC
        LIMIT 4");
        return $db->resultSet();
    }

    /*
    Chart de vendas por dia
    Chart-area de jan a dez do ano atual
    */
public function vendasPorMes()
{
    $db = new db();
    $db->query("SELECT 
        MONTH(data_pedido) as mes,
        COALESCE(SUM(total), 0) as total_vendas
    FROM pedidos 
    WHERE YEAR(data_pedido) = '2025'
    GROUP BY MONTH(data_pedido)
    ORDER BY mes ASC");
    
    // Inicializa array com 12 meses zerados
    $vendas = array_fill(0, 13, 0);
    
    // Preenche com os valores do banco
    foreach($db->resultSet() as $row) {
        $vendas[$row['mes']] = floatval($row['total_vendas']);
    }
    
    // Retorna o array completo
    return $vendas;
}

/*
Chart pia de pedidos 
*/
public function statusFabrica()
{
    $db = new db();
    $db->query("SELECT 
        status,
        COUNT(*) as total
    FROM fabrica
    GROUP BY status");
    
    $result = $db->resultSet();
    
    // Inicializa array com status zerados
    $status = [
        'Aguardando Separacao' => 0,
        'Em Producao' => 0, 
        'Finalizado' => 0
    ];
    
    // Preenche com os valores do banco
    foreach($result as $row) {
        $status[$row['status']] = intval($row['total']);
    }
    
    return array_values($status); // Retorna apenas os valores na ordem correta
}

/*
kpis das informaçoes: 
Pronto Entrega
Pronto Retirada
Separado
Em Produção
Finalizado
Aguard. Separação
*/
public function kpisFabrica()
{
    $db = new db();
    $db->query("SELECT 
        status,
        COUNT(*) as total
    FROM fabrica
    GROUP BY status");
    return $db->resultSet();
}




    
}