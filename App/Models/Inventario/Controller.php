<?php

namespace App\Models\Inventario;

use db;
use App\Models\Estoque\Controller as EstoqueController;

class Controller
{
    /**
     * Registra movimentação de inventário (entrada ou saída) e altera o estoque.
     * @param array $dados ['produto_id', 'quantidade', 'motivo', 'loja_id', 'pedido_id' => opcional, 'tipo' => 'Entrada'|'Saida']
     * @return array ['ok' => bool, 'msg' => string]
     */
    public function registrarDevolucao($dados)
    {
        $produto_id = (int)($dados['produto_id'] ?? 0);
        $quantidade = (float)str_replace(',', '.', (string)($dados['quantidade'] ?? 0));
        $motivo = trim((string)($dados['motivo'] ?? ''));
        $loja_id = (int)($dados['loja_id'] ?? 0);
        $tipo = in_array($dados['tipo'] ?? '', ['Entrada', 'Saida']) ? $dados['tipo'] : 'Entrada';
        $pedido_id_raw = $dados['pedido_id'] ?? null;
        $pedido_id = ($pedido_id_raw !== null && $pedido_id_raw !== '' && (int)$pedido_id_raw > 0)
            ? (int)$pedido_id_raw : null;
        $usuario_id = (int)($_COOKIE['id'] ?? 0);

        if ($produto_id <= 0) {
            return ['ok' => false, 'msg' => 'Produto inválido.'];
        }
        if ($quantidade <= 0) {
            return ['ok' => false, 'msg' => 'Quantidade inválida.'];
        }
        if ($motivo === '') {
            return ['ok' => false, 'msg' => 'Motivo é obrigatório.'];
        }
        if ($loja_id <= 0) {
            return ['ok' => false, 'msg' => 'Estoque é obrigatório.'];
        }
        if ($usuario_id <= 0) {
            return ['ok' => false, 'msg' => 'Usuário não autenticado.'];
        }

        $estoqueCtrl = new EstoqueController();
        $ehCD = $estoqueCtrl->lojaEhCD($loja_id);

        $db = new db();
        $db->query("SELECT id, descricao_etiqueta FROM produtos WHERE id = :pid LIMIT 1");
        $db->bind(':pid', $produto_id);
        $produto = $db->single();
        if (!$produto) {
            return ['ok' => false, 'msg' => 'Produto não encontrado.'];
        }
        $descricao_produto = $produto['descricao_etiqueta'] ?? 'Produto #' . $produto_id;

        if ($tipo === 'Entrada') {
            if ($ehCD) {
                $result = $estoqueCtrl->adicionarEstoqueCD($produto_id, $quantidade, $descricao_produto);
            } else {
                $result = $estoqueCtrl->adicionarEstoqueLoja($loja_id, $produto_id, $quantidade, $descricao_produto);
            }
        } else {
            if ($ehCD) {
                $result = $estoqueCtrl->removerEstoqueCD($produto_id, $quantidade, $descricao_produto, $motivo);
            } else {
                $result = $estoqueCtrl->removerEstoqueLoja($loja_id, $produto_id, $quantidade, $descricao_produto, $motivo);
            }
        }

        if (!$result['ok']) {
            return $result;
        }

        if ($pedido_id !== null && $pedido_id > 0) {
            $db->query("SELECT id FROM pedidos WHERE id = :pid LIMIT 1");
            $db->bind(':pid', $pedido_id);
            $pedidoExiste = $db->single();
            if (!$pedidoExiste) {
                return ['ok' => false, 'msg' => 'Número da venda informado não existe.'];
            }
        }

        try {
            $db->query("
                INSERT INTO inventario_devolucoes (
                    produto_id, usuario_responsavel_id, data_devolucao, hora_devolucao,
                    pedido_id, motivo, loja_id, quantidade, tipo
                ) VALUES (
                    :produto_id, :usuario_id, :data_devolucao, :hora_devolucao,
                    :pedido_id, :motivo, :loja_id, :quantidade, :tipo
                )
            ");
            $db->bind(':produto_id', $produto_id);
            $db->bind(':usuario_id', $usuario_id);
            $db->bind(':data_devolucao', date('Y-m-d'));
            $db->bind(':hora_devolucao', date('H:i:s'));
            $db->bind(':pedido_id', $pedido_id);
            $db->bind(':motivo', $motivo);
            $db->bind(':loja_id', $loja_id);
            $db->bind(':quantidade', $quantidade);
            $db->bind(':tipo', $tipo);
            $db->execute();

            return ['ok' => true, 'msg' => ($tipo === 'Entrada' ? 'Entrada' : 'Saída') . ' registrada com sucesso.'];
        } catch (\Throwable $e) {
            error_log('Inventario::registrarDevolucao: ' . $e->getMessage());
            $msg = $e->getMessage();
            if (strpos($msg, "doesn't exist") !== false || strpos($msg, 'não existe') !== false) {
                return ['ok' => false, 'msg' => 'Tabela inventario_devolucoes não encontrada. Execute a migration em migration/2026-03-20-inventario_devolucoes.sql'];
            }
            if (strpos($msg, 'foreign key') !== false || strpos($msg, 'Foreign key') !== false) {
                return ['ok' => false, 'msg' => 'Dados inconsistentes. Verifique se o produto, loja e número da venda existem.'];
            }
            return ['ok' => false, 'msg' => 'Erro ao salvar registro de devolução.'];
        }
    }

    /**
     * Lista devoluções (para tela operacional).
     */
    public function listar($limite = 50)
    {
        $db = new db();
        $db->query("
            SELECT d.*, p.descricao_etiqueta AS nome_produto, l.nome AS loja_nome, l.tipo AS loja_tipo,
                   u.nome_completo AS responsavel_nome
            FROM inventario_devolucoes d
            LEFT JOIN produtos p ON d.produto_id = p.id
            LEFT JOIN loja l ON d.loja_id = l.id
            LEFT JOIN usuarios u ON d.usuario_responsavel_id = u.id
            ORDER BY d.id DESC
            LIMIT " . (int)$limite . "
        ");
        return $db->resultSet();
    }

    /**
     * Relatório de movimentações por período (paginado).
     */
    public function listarDevolucoes($inicio = null, $fim = null, $motivo = null, $loja_id = null, $tipo = null, $paginaAtual = 1, $itensPorPagina = 15, $url = null)
    {
        $where = " WHERE d.id > 0";
        $binds = [];

        if (!empty($inicio) && !empty($fim)) {
            $where .= " AND d.data_devolucao BETWEEN :data_inicio AND :data_fim";
            $binds[':data_inicio'] = $inicio;
            $binds[':data_fim'] = $fim;
        }
        if (!empty($motivo)) {
            $where .= " AND d.motivo LIKE :motivo";
            $binds[':motivo'] = '%' . $motivo . '%';
        }
        if (!empty($tipo) && in_array($tipo, ['Entrada', 'Saida'])) {
            $where .= " AND d.tipo = :tipo";
            $binds[':tipo'] = $tipo;
        }
        if (!empty($loja_id)) {
            $where .= " AND d.loja_id = :loja_id";
            $binds[':loja_id'] = (int)$loja_id;
        }

        $db = new db();
        $queryTotal = "SELECT COUNT(*) as total FROM inventario_devolucoes d" . $where;
        $db->query($queryTotal);
        foreach ($binds as $k => $v) {
            $db->bind($k, $v);
        }
        $totalRegistros = (int)($db->resultSet()[0]['total'] ?? 0);

        $totalPaginas = max(1, ceil($totalRegistros / $itensPorPagina));
        $paginaAtual = max(1, min($paginaAtual, $totalPaginas));
        $offset = ($paginaAtual - 1) * $itensPorPagina;

        $query = "
            SELECT d.*, p.descricao_etiqueta AS nome_produto, p.id AS produto_codigo,
                   l.nome AS loja_nome, l.tipo AS loja_tipo,
                   u.nome_completo AS responsavel_nome
            FROM inventario_devolucoes d
            LEFT JOIN produtos p ON d.produto_id = p.id
            LEFT JOIN loja l ON d.loja_id = l.id
            LEFT JOIN usuarios u ON d.usuario_responsavel_id = u.id
            {$where}
            ORDER BY d.data_devolucao DESC, d.hora_devolucao DESC
            LIMIT " . (int)$itensPorPagina . " OFFSET " . (int)$offset . "
        ";
        $db->query($query);
        foreach ($binds as $k => $v) {
            $db->bind($k, $v);
        }
        $registros = $db->resultSet();

        $htmlPaginacao = $this->gerarPaginacaoHtml($paginaAtual, $totalPaginas, $url);

        return [
            'registros' => $registros,
            'navegacaoHtml' => $htmlPaginacao,
            'total' => $totalRegistros
        ];
    }

    private function gerarPaginacaoHtml($paginaAtual, $totalPaginas, $url)
    {
        if (empty($url)) {
            return '';
        }

        $paginaCentralizada = [];
        $intervalo = 2;
        $inicioIntervalo = max(1, $paginaAtual - $intervalo);
        $fimIntervalo = min($totalPaginas, $paginaAtual + $intervalo);
        for ($i = $inicioIntervalo; $i <= $fimIntervalo; $i++) {
            $paginaCentralizada[] = $i;
        }

        $html = '<div class="mt-3"><nav aria-label="Paginação"><ul class="pagination">';
        $html .= '<li class="page-item ' . ($paginaAtual == 1 ? 'disabled' : '') . '"><a class="page-link" href="' . $url . '&pagina=1">Início</a></li>';
        $html .= '<li class="page-item ' . ($paginaAtual == 1 ? 'disabled' : '') . '"><a class="page-link" href="' . $url . '&pagina=' . max(1, $paginaAtual - 1) . '">Voltar</a></li>';

        foreach ($paginaCentralizada as $pag) {
            $active = ($paginaAtual == $pag) ? 'active' : '';
            $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $url . '&pagina=' . $pag . '">' . $pag . '</a></li>';
        }

        $html .= '<li class="page-item ' . ($paginaAtual == $totalPaginas ? 'disabled' : '') . '"><a class="page-link" href="' . $url . '&pagina=' . min($totalPaginas, $paginaAtual + 1) . '">Próximo</a></li>';
        $html .= '<li class="page-item ' . ($paginaAtual == $totalPaginas ? 'disabled' : '') . '"><a class="page-link" href="' . $url . '&pagina=' . $totalPaginas . '">Final</a></li>';
        $html .= '</ul></nav></div>';

        return $html;
    }
}
