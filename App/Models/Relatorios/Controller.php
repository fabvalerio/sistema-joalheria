<?php

namespace App\Models\Relatorios;

use db;

class Controller
{
    // Listar todas as contas
    public function listar($tipo = null, $inicio = null, $fim = null)
    {
        $where = "";

        // Garantir que o tipo seja válido ('R' para Receber, 'P' para Pagar ou null para todos)
        if (!empty($tipo) && in_array($tipo, ['R', 'P'])) {
            $where .= " AND tipo = '{$tipo}'";
        }

        if (!empty($inicio) && !empty($fim)) {
            $where .= " AND data_vencimento BETWEEN '{$inicio}' AND '{$fim}'";
        }

        $db = new db();

        // Construir a query base com o espaço antes do ORDER BY
        $query = "SELECT * FROM financeiro_contas WHERE id > 0 " . $where . " ORDER BY data_vencimento ASC";




        $db->query($query);


        return $db->resultSet();
    }
    // Listar todas as contas
    public function soma($inicio = null, $fim = null)
    {
        $where = "";

        if (!empty($inicio) && !empty($fim)) {
            $where .= " AND data_vencimento BETWEEN '{$inicio}' AND '{$fim}'";
        }


        $db = new db();

        // Construir a query base
        $query = "SELECT *,
                    (SELECT SUM(valor) FROM financeiro_contas WHERE tipo = 'R' {$where}) AS total_receber,
                    (SELECT SUM(valor) FROM financeiro_contas WHERE tipo = 'P' {$where}) AS total_pagar
                    FROM financeiro_contas
                    ORDER BY data_vencimento ASC";




        $db->query($query);


        return $db->resultSet();
    }


    public function listaPages($tipo = null, $inicio = null, $fim = null, $paginaAtual = 1, $itensPorPagina = 9, $url = null)
    {
        $where = "";

        if (!empty($tipo) && !in_array($tipo, ['R', 'P'])) {
            $where .= " AND tipo = '{$tipo}'";
        }

        if (!empty($inicio) && !empty($fim)) {
            $where .= " AND data_vencimento BETWEEN '{$inicio}' AND '{$fim}'";
        }

        $db = new db();

        // Calcular o número total de registros
        $queryTotal = "SELECT COUNT(*) as total FROM financeiro_contas WHERE id > 0 " . $where;
        $db->query($queryTotal);
        $totalRegistros = $db->resultSet()[0]['total'];

        // Calcular o número total de páginas
        $totalPaginas = ceil($totalRegistros / $itensPorPagina);
        $paginaAtual = max(1, min($paginaAtual, $totalPaginas));
        $offset = ($paginaAtual - 1) * $itensPorPagina;

        // Query paginada
        $query = "SELECT * FROM financeiro_contas WHERE id > 0 " . $where . " ORDER BY data_vencimento ASC LIMIT {$itensPorPagina} OFFSET {$offset}";
        $db->query($query);
        $registros = $db->resultSet();

        // Criar navegação
        $paginaCentralizada = [];
        $intervalo = 2;
        $inicioIntervalo = max(1, $paginaAtual - $intervalo);
        $fimIntervalo = min($totalPaginas, $paginaAtual + $intervalo);

        for ($i = $inicioIntervalo; $i <= $fimIntervalo; $i++) {
            $paginaCentralizada[] = $i;
        }

        // Criar HTML da paginação
        $htmlPaginacao = '<div class="mt-3"><nav aria-label="Paginação"><ul class="pagination">';

        // Início e Voltar
        $htmlPaginacao .= '<li class="page-item ' . (($paginaAtual == 1) ? 'disabled' : '') . '">
            <a class="page-link" href="' . $url . '&pagina=1">Início</a></li>';

        $htmlPaginacao .= '<li class="page-item ' . (($paginaAtual == 1) ? 'disabled' : '') . '">
            <a class="page-link" href="' . $url . '&pagina=' . max(1, $paginaAtual - 1) . '">Voltar</a></li>';

        // Páginas centralizadas
        foreach ($paginaCentralizada as $pag) {
            $active = ($paginaAtual == $pag) ? 'active' : '';
            $htmlPaginacao .= '<li class="page-item ' . $active . '">
                <a class="page-link" href="' . $url . '&pagina=' . $pag . '">' . $pag . '</a></li>';
        }

        // Próximo e Final
        $htmlPaginacao .= '<li class="page-item ' . (($paginaAtual == $totalPaginas) ? 'disabled' : '') . '">
            <a class="page-link" href="' . $url . '&pagina=' . min($totalPaginas, $paginaAtual + 1) . '">Próximo</a></li>';

        $htmlPaginacao .= '<li class="page-item ' . (($paginaAtual == $totalPaginas) ? 'disabled' : '') . '">
            <a class="page-link" href="' . $url . '&pagina=' . $totalPaginas . '">Final</a></li>';

        $htmlPaginacao .= '</ul></nav></div>';

        return [
            'registros' => $registros,
            'navegacaoHtml' => $htmlPaginacao
        ];
    }


    public function vendas($tipo = null, $inicio = null, $fim = null, $paginaAtual = 1, $itensPorPagina = 9, $url = null, $vendedor_id = null)
    {
        $where = "";
        $where2 = "";

        if (!empty($tipo)) {
            $where .= " AND p.forma_pagamento LIKE '%{$tipo}%'";
        }

        if (!empty($inicio) && !empty($fim)) {
            $where2 .= " AND p.data_pedido BETWEEN '{$inicio}' AND '{$fim}'";
        }

        if (!empty($vendedor_id)) {
            $where .= " AND p.cod_vendedor = '{$vendedor_id}'";
        }

        $db = new db();

        $queryTotal = "SELECT COUNT(*) as total FROM pedidos p WHERE p.id > 0 AND p.orcamento is null " . $where . " " . $where2;
        $db->query($queryTotal);
        $totalRegistros = $db->resultSet()[0]['total'];

        $totalPaginas = ceil($totalRegistros / $itensPorPagina);
        $paginaAtual = max(1, min($paginaAtual, $totalPaginas));
        $offset = ($paginaAtual - 1) * $itensPorPagina;

        $query = "SELECT p.*, 
                    c.nome_pf, 
                    c.nome_fantasia_pj,
                    u.nome_completo as vendedor_nome
                    FROM pedidos p
                    LEFT JOIN clientes c ON p.cliente_id = c.id
                    LEFT JOIN usuarios u ON p.cod_vendedor = u.id
                    WHERE p.id > 0 AND p.orcamento is null " . $where . " " . $where2 . " ORDER BY p.data_pedido ASC LIMIT {$itensPorPagina} OFFSET {$offset}";
        $db->query($query);
        $registros = $db->resultSet();

        // Criar navegação
        $paginaCentralizada = [];
        $intervalo = 2;
        $inicioIntervalo = max(1, $paginaAtual - $intervalo);
        $fimIntervalo = min($totalPaginas, $paginaAtual + $intervalo);

        for ($i = $inicioIntervalo; $i <= $fimIntervalo; $i++) {
            $paginaCentralizada[] = $i;
        }

        // Criar HTML da paginação
        $htmlPaginacao = '<div class="mt-3"><nav aria-label="Paginação"><ul class="pagination">';

        // Início e Voltar
        $htmlPaginacao .= '<li class="page-item ' . (($paginaAtual == 1) ? 'disabled' : '') . '">
            <a class="page-link" href="' . $url . '&pagina=1">Início</a></li>';

        $htmlPaginacao .= '<li class="page-item ' . (($paginaAtual == 1) ? 'disabled' : '') . '">
            <a class="page-link" href="' . $url . '&pagina=' . max(1, $paginaAtual - 1) . '">Voltar</a></li>';

        // Páginas centralizadas
        foreach ($paginaCentralizada as $pag) {
            $active = ($paginaAtual == $pag) ? 'active' : '';
            $htmlPaginacao .= '<li class="page-item ' . $active . '">
                <a class="page-link" href="' . $url . '&pagina=' . $pag . '">' . $pag . '</a></li>';
        }

        // Próximo e Final
        $htmlPaginacao .= '<li class="page-item ' . (($paginaAtual == $totalPaginas) ? 'disabled' : '') . '">
            <a class="page-link" href="' . $url . '&pagina=' . min($totalPaginas, $paginaAtual + 1) . '">Próximo</a></li>';

        $htmlPaginacao .= '<li class="page-item ' . (($paginaAtual == $totalPaginas) ? 'disabled' : '') . '">
            <a class="page-link" href="' . $url . '&pagina=' . $totalPaginas . '">Final</a></li>';

        $htmlPaginacao .= '</ul></nav></div>';

        return [
            'registros' => $registros,
            'navegacaoHtml' => $htmlPaginacao
        ];
    }


    public function somaVendas($inicio = null, $fim = null)
    {
        $where = "";

        if (!empty($inicio) && !empty($fim)) {
            $where .= " AND data_pedido BETWEEN '{$inicio}' AND '{$fim}'";
        }

        $db = new db();

        $query = "SELECT *,
                    (SELECT SUM(total) FROM pedidos WHERE orcamento is null AND status_pedido = 'Pendente' {$where}) AS Pendente,
                    (SELECT SUM(total) FROM pedidos WHERE orcamento is null AND status_pedido = 'Pago' {$where}) AS Pago
                    FROM pedidos
                    ORDER BY data_pedido ASC";

        $db->query($query);

        return $db->resultSet();
    }

    public function somaVendasPorPagamento($inicio = null, $fim = null)
    {
        $where = "";

        if (!empty($inicio) && !empty($fim)) {
            $where .= " AND data_pedido BETWEEN '{$inicio}' AND '{$fim}'";
        }

        $db = new db();

        $query = "SELECT 
                    SUM(CASE WHEN forma_pagamento = 'Dinheiro' THEN total ELSE 0 END) AS dinheiro,
                    SUM(CASE WHEN forma_pagamento LIKE '%Cartão%' OR forma_pagamento LIKE '%Crédito%' OR forma_pagamento LIKE '%Débito%' OR forma_pagamento LIKE '%Parcelado%' THEN total ELSE 0 END) AS cartao,
                    SUM(CASE WHEN forma_pagamento = 'Cheque' THEN total ELSE 0 END) AS cheque,
                    SUM(CASE WHEN forma_pagamento LIKE '%Carnê%' THEN total ELSE 0 END) AS carne,
                    SUM(CASE WHEN forma_pagamento LIKE '%Pix%' OR forma_pagamento LIKE '%Depósito%' OR forma_pagamento LIKE '%Transferência%' THEN total ELSE 0 END) AS deposito,
                    SUM(total) AS total_geral
                FROM pedidos
                WHERE orcamento is null {$where}";

        $db->query($query);

        return $db->single();
    }

    public function vendasParaImprimir($tipo = null, $inicio = null, $fim = null, $vendedor_id = null)
    {
        $where = "";
        $where2 = "";

        if (!empty($tipo)) {
            $where .= " AND p.forma_pagamento LIKE '%{$tipo}%'";
        }

        if (!empty($inicio) && !empty($fim)) {
            $where2 .= " AND p.data_pedido BETWEEN '{$inicio}' AND '{$fim}'";
        }

        if (!empty($vendedor_id)) {
            $where .= " AND p.cod_vendedor = '{$vendedor_id}'";
        }

        $db = new db();

        $query = "SELECT p.*, 
                    c.nome_pf, 
                    c.nome_fantasia_pj,
                    u.nome_completo as vendedor_nome,
                    CASE WHEN p.forma_pagamento = 'Dinheiro' THEN p.total ELSE 0 END as dinheiro,
                    CASE WHEN p.forma_pagamento = 'Cheque' THEN p.total ELSE 0 END as cheque,
                    CASE WHEN p.forma_pagamento LIKE '%Cartão%' OR p.forma_pagamento LIKE '%Crédito%' OR p.forma_pagamento LIKE '%Débito%' OR p.forma_pagamento LIKE '%Parcelado%' THEN p.total ELSE 0 END as cartao,
                    0 as ouro,
                    CASE WHEN p.forma_pagamento LIKE '%Carnê%' THEN p.total ELSE 0 END as carne,
                    CASE WHEN p.forma_pagamento LIKE '%Depósito%' OR p.forma_pagamento LIKE '%Pix%' OR p.forma_pagamento LIKE '%Transferência%' THEN p.total ELSE 0 END as deposito,
                    COALESCE((
                        SELECT SUM(
                            (pi.valor_unitario * pi.quantidade * (1 - COALESCE(pi.desconto_percentual, 0) / 100)) 
                            * COALESCE(cv.comissao_v, 0) / 100
                        )
                        FROM pedidos_itens pi
                        JOIN produtos pr ON pi.produto_id = pr.id
                        LEFT JOIN comissao_vendedor cv ON cv.grupo_produtos_id = pr.grupo_id AND cv.usuarios_id = p.cod_vendedor
                        WHERE pi.pedido_id = p.id
                    ), 0) as comissao
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN usuarios u ON p.cod_vendedor = u.id
                WHERE p.id > 0 AND p.orcamento is null " . $where . " " . $where2 . " 
                ORDER BY u.nome_completo ASC, p.id ASC";
        $db->query($query);
        
        return $db->resultSet();
    }

    public function listarVendedores()
    {
        $db = new db();
        $db->query("SELECT id, nome_completo FROM usuarios WHERE status = 1 ORDER BY nome_completo ASC");
        return $db->resultSet();
    }



    public function movimentos($tipo = null, $inicio = null, $fim = null, $paginaAtual = 1, $itensPorPagina = 9, $url = null, $loja_id = null)
    {
        $where = "";

        if (!empty($tipo) && in_array($tipo, ['Entrada', 'Saida'])) {
            $where .= " AND m.tipo_movimentacao = '{$tipo}'";
        }

        if (!empty($inicio) && !empty($fim)) {
            $where .= " AND m.data_movimentacao BETWEEN '{$inicio}' AND '{$fim}'";
        }

        if (!empty($loja_id)) {
            $where .= " AND m.loja_id = '{$loja_id}'";
        }

        $db = new db();

        $queryTotal = "SELECT COUNT(*) as total FROM movimentacao_estoque as m WHERE m.id > 0 " . $where;
        $db->query($queryTotal);
        $totalRegistros = $db->resultSet()[0]['total'];

        $totalPaginas = ceil($totalRegistros / $itensPorPagina);
        $paginaAtual = max(1, min($paginaAtual, $totalPaginas));
        $offset = ($paginaAtual - 1) * $itensPorPagina;

        $query = "SELECT m.produto_id, 
                        m.descricao_produto,
                        m.tipo_movimentacao, 
                        SUM(m.quantidade) AS quantidade,
                        MAX(m.data_movimentacao),
                        MAX(e.quantidade) AS atual,
                        l.nome AS loja_nome
                FROM movimentacao_estoque AS m
                LEFT JOIN estoque AS e ON m.produto_id = e.produtos_id
                LEFT JOIN loja l ON m.loja_id = l.id
                WHERE m.id > 0 {$where} 
                GROUP BY m.produto_id, m.descricao_produto, m.tipo_movimentacao, l.nome
                LIMIT {$itensPorPagina} OFFSET {$offset}";
        $db->query($query);
        $registros = $db->resultSet();

        // Criar navegação
        $paginaCentralizada = [];
        $intervalo = 2;
        $inicioIntervalo = max(1, $paginaAtual - $intervalo);
        $fimIntervalo = min($totalPaginas, $paginaAtual + $intervalo);

        for ($i = $inicioIntervalo; $i <= $fimIntervalo; $i++) {
            $paginaCentralizada[] = $i;
        }

        // Criar HTML da paginação
        $htmlPaginacao = '<div class="mt-3"><nav aria-label="Paginação"><ul class="pagination">';

        // Início e Voltar
        $htmlPaginacao .= '<li class="page-item ' . (($paginaAtual == 1) ? 'disabled' : '') . '">
            <a class="page-link" href="' . $url . '&pagina=1">Início</a></li>';

        $htmlPaginacao .= '<li class="page-item ' . (($paginaAtual == 1) ? 'disabled' : '') . '">
            <a class="page-link" href="' . $url . '&pagina=' . max(1, $paginaAtual - 1) . '">Voltar</a></li>';

        // Páginas centralizadas
        foreach ($paginaCentralizada as $pag) {
            $active = ($paginaAtual == $pag) ? 'active' : '';
            $htmlPaginacao .= '<li class="page-item ' . $active . '">
                <a class="page-link" href="' . $url . '&pagina=' . $pag . '">' . $pag . '</a></li>';
        }

        // Próximo e Final
        $htmlPaginacao .= '<li class="page-item ' . (($paginaAtual == $totalPaginas) ? 'disabled' : '') . '">
            <a class="page-link" href="' . $url . '&pagina=' . min($totalPaginas, $paginaAtual + 1) . '">Próximo</a></li>';

        $htmlPaginacao .= '<li class="page-item ' . (($paginaAtual == $totalPaginas) ? 'disabled' : '') . '">
            <a class="page-link" href="' . $url . '&pagina=' . $totalPaginas . '">Final</a></li>';

        $htmlPaginacao .= '</ul></nav></div>';

        return [
            'registros' => $registros,
            'navegacaoHtml' => $htmlPaginacao
        ];
    }

    // Relatório de Consignações
    public function consignacoes($status = null, $inicio = null, $fim = null)
    {
        $where = "WHERE c.id > 0";

        // Filtro por status
        if (!empty($status) && in_array($status, ['Aberta', 'Finalizada', 'Canceleda'])) {
            $where .= " AND c.status = '{$status}'";
        }

        // Filtro por data
        if (!empty($inicio) && !empty($fim)) {
            $where .= " AND c.data_consignacao BETWEEN '{$inicio}' AND '{$fim}'";
        }

        $db = new db();

        $query = "
            SELECT 
                c.id, 
                c.data_consignacao, 
                c.valor, 
                c.status,
                c.desconto_percentual,
                c.observacao,
                cl.nome_pf, 
                cl.nome_fantasia_pj,
                COUNT(ci.id) as total_itens
            FROM 
                consignacao c
            LEFT JOIN 
                clientes cl ON c.cliente_id = cl.id
            LEFT JOIN
                consignacao_itens ci ON c.id = ci.consignacao_id
            {$where}
            GROUP BY c.id
            ORDER BY 
                c.data_consignacao DESC
        ";

        $db->query($query);
        return $db->resultSet();
    }

    public function listarLojas()
    {
        $db = new db();
        $db->query("SELECT id, nome, tipo FROM loja WHERE status = 1 ORDER BY tipo ASC, nome ASC");
        return $db->resultSet();
    }

    public function estoquePorLoja($loja_id = null)
    {
        $db = new db();
        $where = "";
        if (!empty($loja_id)) {
            $where = " AND el.loja_id = '{$loja_id}'";
        }

        $db->query("
            SELECT 
                el.loja_id,
                l.nome AS loja_nome,
                l.tipo AS loja_tipo,
                el.produto_id,
                p.descricao_etiqueta AS nome_produto,
                p.codigo_fabricante,
                el.quantidade,
                el.quantidade_minima
            FROM estoque_loja el
            INNER JOIN loja l ON el.loja_id = l.id
            LEFT JOIN produtos p ON el.produto_id = p.id
            WHERE el.quantidade > 0 {$where}
            ORDER BY l.tipo ASC, l.nome ASC, p.descricao_etiqueta ASC
        ");
        return $db->resultSet();
    }

    // Soma e estatísticas de consignações
    public function somaConsignacoes($status = null, $inicio = null, $fim = null)
    {
        $where = "WHERE c.id > 0";

        // Filtro por status
        if (!empty($status) && in_array($status, ['Aberta', 'Finalizada', 'Canceleda'])) {
            $where .= " AND c.status = '{$status}'";
        }

        // Filtro por data
        if (!empty($inicio) && !empty($fim)) {
            $where .= " AND c.data_consignacao BETWEEN '{$inicio}' AND '{$fim}'";
        }

        $db = new db();

        $whereSub = str_replace('c.', '', $where); // Remove o alias 'c.' para as subconsultas
        
        $query = "
            SELECT 
                COUNT(c.id) as total_consignacoes,
                SUM(c.valor) as valor_total,
                AVG(c.desconto_percentual) as desconto_medio,
                SUM(c.valor / (1 - (c.desconto_percentual / 100))) as subtotal_total,
                SUM((c.valor / (1 - (c.desconto_percentual / 100))) - c.valor) as desconto_total,
                (SELECT COUNT(*) FROM consignacao {$whereSub} AND status = 'Aberta') as total_abertas,
                (SELECT COUNT(*) FROM consignacao {$whereSub} AND status = 'Finalizada') as total_finalizadas,
                (SELECT COUNT(*) FROM consignacao {$whereSub} AND status = 'Canceleda') as total_canceladas
            FROM 
                consignacao c
            {$where}
        ";

        $db->query($query);
        return $db->single();
    }
}
