<?php

namespace App\Models\MovimentacaoEstoque;

use db;

class Controller
{
  public function listar($produto = null, $inicio = null, $fim = null, $paginaAtual = 1, $itensPorPagina = 9, $url = null)
  {

    $where = "";
    $where2 = "";

    if (!empty($produto)) {
        $where .= " AND descricao_produto LIKE '%{$produto}%'";
    }

    if (!empty($inicio) && !empty($fim)) {
        $where2 .= " AND data_movimentacao BETWEEN '{$inicio}' AND '{$fim}'";
    }

    $db = new db();
    // $db->query("
    //         SELECT 
    //             id, 
    //             produto_id, 
    //             descricao_produto, 
    //             tipo_movimentacao, 
    //             quantidade, 
    //             documento, 
    //             data_movimentacao, 
    //             motivo, 
    //             estoque_antes, 
    //             estoque_atualizado
    //         FROM movimentacao_estoque
    //         WHERE id > 0 " . $where . " " . $where2 . "
    //         ORDER BY id DESC
    //     ");
    
    
        // Calcular o número total de registros
        $queryTotal = "SELECT COUNT(*) as total FROM movimentacao_estoque WHERE id > 0 " . $where . " " . $where2;
        $db->query($queryTotal);
        $totalRegistros = $db->resultSet()[0]['total'];

        // Calcular o número total de páginas
        $totalPaginas = ceil($totalRegistros / $itensPorPagina);
        $paginaAtual = max(1, min($paginaAtual, $totalPaginas));
        $offset = ($paginaAtual - 1) * $itensPorPagina;

        // Query paginada
        $query = "SELECT 
                      id, 
                      produto_id, 
                      descricao_produto, 
                      tipo_movimentacao, 
                      quantidade, 
                      documento, 
                      data_movimentacao, 
                      motivo, 
                      estoque_antes, 
                      estoque_atualizado
                    FROM movimentacao_estoque 
                    WHERE id > 0 " . $where . " " . $where2 . " ORDER BY data_movimentacao ASC LIMIT {$itensPorPagina} OFFSET {$offset}";
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
