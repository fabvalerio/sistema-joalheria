<?php

namespace App\Models\Consignacao;

use db; // Classe de conexão com o banco

class Controller
{
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
                cl.nome_pf, 
                cl.nome_fantasia_pj
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
                cl.nome_pf, 
                cl.nome_fantasia_pj,
                ci.id AS item_id
            FROM 
                consignacao c
            LEFT JOIN 
                clientes cl ON c.cliente_id = cl.id
            LEFT JOIN 
                consignacao_itens ci ON c.id = ci.consignacao_id
            WHERE 
                c.id = :id
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
                p.descricao_etiqueta AS nome_produto
            FROM 
                consignacao_itens ci
            LEFT JOIN 
                produtos p ON ci.produto_id = p.id
            WHERE 
                ci.consignacao_id = :consignacao_id
        ");
        $db->bind(':consignacao_id', $id);
        $itens = $db->resultSet();

        return [
            'consignacao' => $consignacao,
            'itens' => $itens
        ];
    }

    // Cadastrar uma nova consignação
    public function cadastro($dados)
    {
        $db = new db();

        // Inserir a consignação
        $db->query("
            INSERT INTO consignacao (
                cliente_id, data_consignacao, valor, status, observacao
            ) VALUES (
                :cliente_id, :data_consignacao, :valor, :status, :observacao
            )
        ");
        $db->bind(':cliente_id', $dados['cliente_id']);
        $db->bind(':data_consignacao', $dados['data_consignacao']);
        $db->bind(':valor', $dados['valor']);
        $db->bind(':status', $dados['status']);
        $db->bind(':observacao', $dados['observacao']);

        if ($db->execute()) {
            $consignacaoId = $db->lastInsertId();

            // Inserir os itens da consignação
            foreach ($dados['itens'] as $item) {
                if (!isset($item['produto_id'], $item['quantidade'], $item['valor'])) {
                    continue;
                }

                $db->query("
                    INSERT INTO consignacao_itens (
                        consignacao_id, produto_id, quantidade, valor, qtd_devolvido
                    ) VALUES (
                        :consignacao_id, :produto_id, :quantidade, :valor, :qtd_devolvido
                    )
                ");
                $db->bind(':consignacao_id', $consignacaoId);
                $db->bind(':produto_id', $item['produto_id']);
                $db->bind(':quantidade', $item['quantidade']);
                $db->bind(':valor', $item['valor']);
                $db->bind(':qtd_devolvido', $item['qtd_devolvido'] ?? 0);

                if (!$db->execute()) {
                    return false;
                }
                //subtrai quantidade dos produtos selecionados do estoque
                $db->query("
                    UPDATE estoque
                    SET quantidade = quantidade - :quantidade
                    WHERE produtos_id = :produto_id
                ");
                $db->bind(':quantidade', $item['quantidade']);
                $db->bind(':produto_id', $item['produto_id']);
                $db->execute();
            }

            return true;
        }

        return false;
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
            WHERE 
                tipo_cliente = 'PJ'
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
    // Editar uma consignação existente
    public function editar($id, $dados)
    {
        $db = new db();

        // Atualizar o status da consignação
        $db->query("
            UPDATE consignacao
            SET 
                status = :status,
                valor = :valor
            WHERE id = :id
        ");
        $db->bind(':status', $dados['status']);
        $db->bind(':valor', $dados['valor']);
        $db->bind(':id', $id);

        if (!$db->execute()) {
            echo "<pre>Erro ao atualizar status da consignação.</pre>";
            return false; // Retorna falso se a atualização do status falhar
        }

        // Atualizar os itens da consignação
        foreach ($dados['itens'] as $item) {
            $db->query("
                UPDATE consignacao_itens
                SET 
                    qtd_devolvido = :qtd_devolvido
                WHERE id = :id
            ");
            $db->bind(':qtd_devolvido', $item['qtd_devolvido']);
            $db->bind(':id', $item['id']); // Atualiza com o identificador correto do item

            if (!$db->execute()) {
                echo "<pre>Erro ao atualizar item com ID {$item['id']}.</pre>";
                return false; // Retorna falso se a atualização de um item falhar
            }
            //soma qtd_devolvido na quantidade do estoque
            $db->query("
                UPDATE estoque 
                SET quantidade = quantidade + :quantidade
                WHERE produtos_id = :produto_id
            ");
            $db->bind(':quantidade', $item['qtd_devolvido']);
            $db->bind(':produto_id', $item['produto_id']);
            $db->execute();
        }

        return true; // Retorna verdadeiro se todas as atualizações forem bem-sucedidas
    }

    public function deletar($id)
    {
        $db = new db();

        // Selecionar todos os itens da consignação antes de deletar
        $db->query("
            SELECT 
                ci.produto_id, 
                ci.quantidade, 
                ci.qtd_devolvido 
            FROM consignacao_itens ci 
            WHERE ci.consignacao_id = :id
        ");
        $db->bind(":id", $id);
        $itens = $db->resultSet();

        // Para cada item, calcular a quantidade a ser devolvida ao estoque
        foreach ($itens as $item) {
            $quantidadeDevolvida = $item['quantidade'] - $item['qtd_devolvido'];
            if ($quantidadeDevolvida > 0) {
                $db->query("
                    UPDATE estoque 
                    SET quantidade = quantidade + :quantidade 
                    WHERE produtos_id = :produto_id
                ");
                $db->bind(":quantidade", $quantidadeDevolvida);
                $db->bind(":produto_id", $item['produto_id']);
                $db->execute();
            }
        }

        // Deletar os itens associados à consignação
        $db->query("DELETE FROM consignacao_itens WHERE consignacao_id = :id");
        $db->bind(":id", $id);
        $itensDeletados = $db->execute();

        // Deletar a consignação
        $db->query("DELETE FROM consignacao WHERE id = :id");
        $db->bind(":id", $id);
        $consignacaoDeletada = $db->execute();

        // Verificar se ambas as operações foram realizadas com sucesso
        return $itensDeletados && $consignacaoDeletada;
    }
}
