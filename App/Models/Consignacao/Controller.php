<?php

namespace App\Models\Consignacao;

use db;
use App\Models\Estoque\Controller as EstoqueController;

class Controller
{
    private function getCDLojaId()
    {
        return (new EstoqueController())->getCDLojaId();
    }

    // Listar todas as consignações
    public function listar()
    {
        $db = new db();
        $db->query("
            SELECT 
                c.id, 
                c.data_consignacao, 
                c.valor, 
                c.status,
                c.desconto_percentual, 
                cl.nome_pf, 
                cl.nome_fantasia_pj,
                cl.telefone,
                cl.whatsapp,
                c.bonificacao
            FROM 
                consignacao c
            LEFT JOIN 
                clientes cl ON c.cliente_id = cl.id
            ORDER BY 
                c.data_consignacao DESC
        ");
        return $db->resultSet();
    }

    // Visualizar uma consignação e seus itens
    public function ver($id)
    {
        $db = new db();

        // Consulta principal da consignação
        $db->query("
            SELECT 
                c.id, 
                c.data_consignacao, 
                c.valor, 
                c.status, 
                c.observacao,
                c.desconto_percentual, 
                cl.nome_pf, 
                cl.nome_fantasia_pj,
                cl.telefone,
                cl.whatsapp,
                ci.id AS item_id,
                c.bonificacao
            FROM 
                consignacao c
            LEFT JOIN 
                clientes cl ON c.cliente_id = cl.id
            LEFT JOIN 
                consignacao_itens ci ON c.id = ci.consignacao_id
            WHERE 
                c.id = :id
            ORDER BY 
                ci.id ASC
        ");
        $db->bind(':id', $id);
        $consignacao = $db->single();

        // Consulta para os itens da consignação
        $db->query("
            SELECT 
                ci.id,
                ci.produto_id, 
                ci.quantidade, 
                ci.valor, 
                ci.qtd_devolvido, 
                p.descricao_etiqueta AS nome_produto,
                p.codigo_fabricante,
                p.id as produto_id
            FROM 
                consignacao_itens ci
            LEFT JOIN 
                produtos p ON ci.produto_id = p.id
            WHERE 
                ci.consignacao_id = :consignacao_id
            ORDER BY 
                p.id ASC
        ");
        $db->bind(':consignacao_id', $id);
        $itens = $db->resultSet();

        return [
            'consignacao' => $consignacao,
            'itens' => $itens
        ];
    }

    // Cadastrar uma nova consignação (saída do CD)
    public function cadastro($dados)
    {
        $db = new db();
        $cd_id = $this->getCDLojaId();

        try {
            $db->beginTransaction();

            $db->query("
                INSERT INTO consignacao (
                    cliente_id, data_consignacao, valor, status, observacao, desconto_percentual, bonificacao, loja_id
                ) VALUES (
                    :cliente_id, :data_consignacao, :valor, :status, :observacao, :desconto_percentual, :bonificacao, :loja_id
                )
            ");
            $db->bind(':cliente_id', $dados['cliente_id']);
            $db->bind(':data_consignacao', $dados['data_consignacao']);
            $db->bind(':valor', $dados['valor']);
            $db->bind(':status', $dados['status']);
            $db->bind(':observacao', $dados['observacao']);
            $db->bind(':desconto_percentual', $dados['desconto_percentual']);
            $db->bind(':bonificacao', $dados['bonificacao']);
            $db->bind(':loja_id', $cd_id);

            if (!$db->execute()) {
                $db->cancelTransaction();
                return false;
            }

            $consignacaoId = $db->lastInsertId();

            foreach ($dados['itens'] as $item) {
                if (!isset($item['produto_id'], $item['quantidade'], $item['valor'])) {
                    continue;
                }

                $qtd = (float)$item['quantidade'];
                $produto_id = (int)$item['produto_id'];

                $db->query("
                    INSERT INTO consignacao_itens (
                        consignacao_id, produto_id, quantidade, valor, qtd_devolvido
                    ) VALUES (
                        :consignacao_id, :produto_id, :quantidade, :valor, :qtd_devolvido
                    )
                ");
                $db->bind(':consignacao_id', $consignacaoId);
                $db->bind(':produto_id', $produto_id);
                $db->bind(':quantidade', $qtd);
                $db->bind(':valor', $item['valor']);
                $db->bind(':qtd_devolvido', $item['qtd_devolvido'] ?? 0);

                if (!$db->execute()) {
                    $db->cancelTransaction();
                    return false;
                }

                $db->query("SELECT COALESCE(SUM(quantidade), 0) as total FROM estoque WHERE produtos_id = :pid");
                $db->bind(':pid', $produto_id);
                $r = $db->single();
                $estoqueAntes = $r ? (float)($r['total'] ?? 0) : 0;
                $estoqueAtualizado = $estoqueAntes - $qtd;

                $db->query("SELECT descricao_etiqueta FROM produtos WHERE id = :id LIMIT 1");
                $db->bind(':id', $produto_id);
                $p = $db->single();
                $descricaoProduto = $p ? ($p['descricao_etiqueta'] ?? '') : '';

                $db->query("
                    UPDATE estoque
                    SET quantidade = quantidade - :quantidade
                    WHERE produtos_id = :produto_id
                ");
                $db->bind(':quantidade', $qtd);
                $db->bind(':produto_id', $produto_id);
                $db->execute();

                if ($cd_id) {
                    $db->query("
                        UPDATE estoque_loja
                        SET quantidade = quantidade - :quantidade
                        WHERE produto_id = :produto_id AND loja_id = :loja_id
                    ");
                    $db->bind(':quantidade', $qtd);
                    $db->bind(':produto_id', $produto_id);
                    $db->bind(':loja_id', $cd_id);
                    $db->execute();
                }

                $db->query("
                    INSERT INTO movimentacao_estoque (
                        produto_id, descricao_produto, tipo_movimentacao, quantidade, documento,
                        data_movimentacao, motivo, estoque_antes, estoque_atualizado, loja_id
                    ) VALUES (
                        :produto_id, :descricao_produto, :tipo_movimentacao, :quantidade, :documento,
                        :data_movimentacao, :motivo, :estoque_antes, :estoque_atualizado, :loja_id
                    )
                ");
                $db->bind(':produto_id', $produto_id);
                $db->bind(':descricao_produto', $descricaoProduto);
                $db->bind(':tipo_movimentacao', 'Saida');
                $db->bind(':quantidade', $qtd);
                $db->bind(':documento', 'Consignação #' . $consignacaoId);
                $db->bind(':data_movimentacao', date('Y-m-d H:i:s'));
                $db->bind(':motivo', 'Consignação #' . $consignacaoId);
                $db->bind(':estoque_antes', $estoqueAntes);
                $db->bind(':estoque_atualizado', $estoqueAtualizado);
                $db->bind(':loja_id', $cd_id);

                if (!$db->execute()) {
                    $db->cancelTransaction();
                    return false;
                }
            }

            $db->endTransaction();
            return (int) $consignacaoId;
        } catch (\Exception $e) {
            if (isset($db)) {
                $db->cancelTransaction();
            }
            return false;
        }
    }

    // Listar clientes para o select
    public function listarClientes()
    {
        $db = new db();
        $db->query("
            SELECT 
                id, nome_pf, nome_fantasia_pj, cpf, cnpj_pj
            FROM 
                clientes
            ORDER BY 
                nome_pf ASC, nome_fantasia_pj ASC
        ");
        return $db->resultSet();
    }

    // Listar produtos para o select
    public function listarProdutos()
    {
        $db = new db();
        $db->query("
            SELECT 
                p.id, 
                p.descricao_etiqueta AS nome_produto, 
                p.em_reais AS preco, 
                e.quantidade AS estoque
            FROM 
                produtos p
            LEFT JOIN 
                estoque e ON p.id = e.produtos_id
            ORDER BY 
                p.descricao_etiqueta ASC
        ");
        return $db->resultSet();
    }
    // Editar uma consignação existente (entrada/devolução no CD)
    public function editar($id, $dados)
    {
        $db = new db();
        $cd_id = $this->getCDLojaId();

        $db->query("
            UPDATE consignacao
            SET 
                status = :status,
                valor = :valor,
                desconto_percentual = :desconto_percentual,
                bonificacao = :bonificacao
            WHERE id = :id
        ");
        $db->bind(':status', $dados['status']);
        $db->bind(':valor', $dados['valor']);
        $db->bind(':desconto_percentual', $dados['desconto_percentual']);
        $db->bind(':bonificacao', $dados['bonificacao']);
        $db->bind(':id', $id);

        if (!$db->execute()) {
            return false;
        }

        $db->query("SELECT id, produto_id, qtd_devolvido FROM consignacao_itens WHERE consignacao_id = :cid");
        $db->bind(':cid', $id);
        $itensAntigos = $db->resultSet();
        $devolvidoAnterior = [];
        foreach ($itensAntigos as $ia) {
            $devolvidoAnterior[$ia['id']] = (float)$ia['qtd_devolvido'];
        }

        foreach ($dados['itens'] as $item) {
            $novaDevolvida = (float)$item['qtd_devolvido'];
            $anteriorDevolvida = $devolvidoAnterior[$item['id']] ?? 0;
            $diferenca = $novaDevolvida - $anteriorDevolvida;

            $db->query("
                UPDATE consignacao_itens
                SET qtd_devolvido = :qtd_devolvido
                WHERE id = :id
            ");
            $db->bind(':qtd_devolvido', $novaDevolvida);
            $db->bind(':id', $item['id']);

            if (!$db->execute()) {
                return false;
            }

            if ($diferenca > 0) {
                $produto_id = (int)$item['produto_id'];

                $db->query("SELECT COALESCE(SUM(quantidade), 0) as total FROM estoque WHERE produtos_id = :pid");
                $db->bind(':pid', $produto_id);
                $r = $db->single();
                $estoqueAntes = $r ? (float)($r['total'] ?? 0) : 0;
                $estoqueAtualizado = $estoqueAntes + $diferenca;

                $db->query("
                    UPDATE estoque 
                    SET quantidade = quantidade + :quantidade
                    WHERE produtos_id = :produto_id
                ");
                $db->bind(':quantidade', $diferenca);
                $db->bind(':produto_id', $produto_id);
                $db->execute();

                if ($cd_id) {
                    $db->query("
                        INSERT INTO estoque_loja (loja_id, produto_id, quantidade, quantidade_minima)
                        VALUES (:loja_id, :produto_id, :quantidade, 0)
                        ON DUPLICATE KEY UPDATE quantidade = quantidade + VALUES(quantidade)
                    ");
                    $db->bind(':loja_id', $cd_id);
                    $db->bind(':produto_id', $produto_id);
                    $db->bind(':quantidade', $diferenca);
                    $db->execute();
                }

                $db->query("SELECT descricao_etiqueta FROM produtos WHERE id = :id LIMIT 1");
                $db->bind(':id', $produto_id);
                $p = $db->single();
                $descricaoProduto = $p ? ($p['descricao_etiqueta'] ?? '') : '';

                $db->query("
                    INSERT INTO movimentacao_estoque (
                        produto_id, descricao_produto, tipo_movimentacao, quantidade, documento,
                        data_movimentacao, motivo, estoque_antes, estoque_atualizado, loja_id
                    ) VALUES (
                        :produto_id, :descricao_produto, :tipo_movimentacao, :quantidade, :documento,
                        :data_movimentacao, :motivo, :estoque_antes, :estoque_atualizado, :loja_id
                    )
                ");
                $db->bind(':produto_id', $produto_id);
                $db->bind(':descricao_produto', $descricaoProduto);
                $db->bind(':tipo_movimentacao', 'Entrada');
                $db->bind(':quantidade', $diferenca);
                $db->bind(':documento', 'Consignação #' . $id);
                $db->bind(':data_movimentacao', date('Y-m-d H:i:s'));
                $db->bind(':motivo', 'Devolução Consignação #' . $id);
                $db->bind(':estoque_antes', $estoqueAntes);
                $db->bind(':estoque_atualizado', $estoqueAtualizado);
                $db->bind(':loja_id', $cd_id);
                $db->execute();
            }
        }

        return true;
    }

    public function deletar($id)
    {
        $db = new db();
        $cd_id = $this->getCDLojaId();

        $db->query("
            SELECT ci.produto_id, ci.quantidade, ci.qtd_devolvido 
            FROM consignacao_itens ci 
            WHERE ci.consignacao_id = :id
        ");
        $db->bind(":id", $id);
        $itens = $db->resultSet();

        foreach ($itens as $item) {
            $quantidadeDevolvida = (float)($item['quantidade'] ?? 0) - (float)($item['qtd_devolvido'] ?? 0);
            if ($quantidadeDevolvida > 0) {
                $produto_id = (int)$item['produto_id'];

                $db->query("
                    UPDATE estoque 
                    SET quantidade = quantidade + :quantidade 
                    WHERE produtos_id = :produto_id
                ");
                $db->bind(":quantidade", $quantidadeDevolvida);
                $db->bind(":produto_id", $produto_id);
                $db->execute();

                if ($cd_id) {
                    $db->query("
                        INSERT INTO estoque_loja (loja_id, produto_id, quantidade, quantidade_minima)
                        VALUES (:loja_id, :produto_id, :quantidade, 0)
                        ON DUPLICATE KEY UPDATE quantidade = quantidade + VALUES(quantidade)
                    ");
                    $db->bind(":loja_id", $cd_id);
                    $db->bind(":produto_id", $produto_id);
                    $db->bind(":quantidade", $quantidadeDevolvida);
                    $db->execute();
                }
            }
        }

        $db->query("DELETE FROM consignacao_itens WHERE consignacao_id = :id");
        $db->bind(":id", $id);
        $itensDeletados = $db->execute();

        $db->query("DELETE FROM consignacao WHERE id = :id");
        $db->bind(":id", $id);
        $consignacaoDeletada = $db->execute();

        return $itensDeletados && $consignacaoDeletada;
    }

    /**
     * Retorna os produtos em mãos de cada vendedora externa (consignações abertas).
     * Agrupa por cliente/vendedora, mostrando os itens ainda não devolvidos.
     */
    public function produtosPorVendedora($cliente_id = null)
    {
        $db = new db();
        $where = $cliente_id ? " AND c.cliente_id = '{$cliente_id}'" : "";

        $db->query("
            SELECT 
                c.id AS consignacao_id,
                c.data_consignacao,
                c.status,
                cl.id AS cliente_id,
                cl.nome_pf,
                cl.nome_fantasia_pj,
                cl.telefone,
                cl.whatsapp,
                ci.produto_id,
                p.descricao_etiqueta AS nome_produto,
                p.codigo_fabricante,
                ci.quantidade,
                ci.qtd_devolvido,
                (ci.quantidade - ci.qtd_devolvido) AS em_maos,
                ci.valor
            FROM consignacao c
            INNER JOIN clientes cl ON c.cliente_id = cl.id
            INNER JOIN consignacao_itens ci ON c.id = ci.consignacao_id
            LEFT JOIN produtos p ON ci.produto_id = p.id
            WHERE c.status = 'Aberta'
              AND (ci.quantidade - ci.qtd_devolvido) > 0
              {$where}
            ORDER BY cl.nome_pf ASC, c.data_consignacao DESC, p.descricao_etiqueta ASC
        ");

        return $db->resultSet();
    }
}
