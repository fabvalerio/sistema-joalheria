<?php

namespace App\Models\Relatorios;

use db;

class Controller
{
    // Listar todas as contas
    public function listar($tipo = null, $inicio = null, $fim = null)
    {
        $where = "";

        // Garantir que o tipo seja valido ('R' para Receber, 'P' para Pagar ou null para todos)
        if(!empty($tipo) && !in_array($tipo, ['Entrada', 'Saida'])) {
            $where .= " AND tipo = '{$tipo}'";
        }

        if(!empty($inicio) && !empty($fim)) {
            $where .= " AND data_vencimento BETWEEN '{$inicio}' AND '{$fim}'";
        }

        $db = new db();

        // Construir a query base
        $query = "SELECT * FROM financeiro_contas WHERE id > 0 " . $where . 'ORDER BY data_vencimento ASC';



        $db->query($query);


        return $db->resultSet();
    }
    // Listar todas as contas
    public function soma($inicio = null, $fim = null)
    {
        $where = "";

        if(!empty($inicio) && !empty($fim)) {
            $where .= " AND data_vencimento BETWEEN '{$inicio }' AND '{$fim}'";
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
    
    
    
    public function movimentos($tipo = null, $inicio = null, $fim = null, $paginaAtual = 1, $itensPorPagina = 9, $url = null)
    {
        $where = "";

        if (!empty($tipo) && in_array($tipo, ['Entrada', 'Saida'])) {
          $where .= " AND tipo_movimentacao = '{$tipo}'";
        }
    
        if (!empty($inicio) && !empty($fim)) {
            $where .= " AND data_vencimento BETWEEN '{$inicio}' AND '{$fim}'";
        }
    
        $db = new db();
    
        // Calcular o número total de registros
        $queryTotal = "SELECT COUNT(*) as total FROM movimentacao_estoque WHERE id > 0 " . $where;
        $db->query($queryTotal);
        $totalRegistros = $db->resultSet()[0]['total'];
    
        // Calcular o número total de páginas
        $totalPaginas = ceil($totalRegistros / $itensPorPagina);
        $paginaAtual = max(1, min($paginaAtual, $totalPaginas));
        $offset = ($paginaAtual - 1) * $itensPorPagina;
    
        // Query paginada
        $query = "SELECT produto_id, 
                        descricao_produto,
                        tipo_movimentacao, 
                        MIN(estoque_antes) AS estoque_inicial,
                        MAX(estoque_atualizado) AS estoque_final ,
                        MAX(data_movimentacao)
                FROM movimentacao_estoque 
                WHERE id > 0 {$where} 
                GROUP BY produto_id, descricao_produto, tipo_movimentacao
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
    
    
    
    



}
