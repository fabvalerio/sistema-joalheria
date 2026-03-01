<?php

/*
**  -----------------------------------------------------------------------------------------------------------
**  Onde tiver MODELO, altere para o nome da tabela do SQL para melhor funcionalidade e gestão do código
**  -----------------------------------------------------------------------------------------------------------
**  Obs.: Pode ser criado vários "public function NomeFuncao()" conforme a necessidade, respeitando o padrão
**  dentro do "class Controller"
**  -----------------------------------------------------------------------------------------------------------
*/

namespace App\Models\Loja;  // Altere para o nome "Modelo" para nome da Tabela do SQL
use db; // Importa a classe de conexão com o banco de dados

class Controller
{
    public function listar()
    {
        $lista = new db();
        $lista->query("SELECT * FROM loja ORDER BY id DESC");
        $resultados = $lista->resultSet(); // Supondo que o método resultSet() retorna os resultados
        return $resultados;
    }

    public function ver($id)
    {
        $ver = new db();
        $ver->query("SELECT * FROM loja WHERE id = :id");
        $ver->bind(":id", $id);
        $resultados = $ver->single();
        return $resultados ?: false;
    }

    public function cadastro($dados)
    {
        $lista = new db();

        // Cria os placeholders dinâmicos
        $campos = implode(", ", array_keys($dados));
        $placeholders = ":" . implode(", :", array_keys($dados));

        // Monta o SQL
        $sql = "INSERT INTO loja ($campos) VALUES ($placeholders)";
        $lista->query($sql);

        // Associa os valores dinâmicos
        foreach ($dados as $campo => $valor) {
            $lista->bind(":$campo", $valor);
        }

        // Executa e retorna o status
        return $lista->execute();
    }

    public function editar($id, $dados)
    {
        $editar = new db();

        // Cria os placeholders no formato campo = :campo
        $setPlaceholders = [];
        foreach ($dados as $campo => $valor) {
            $setPlaceholders[] = "$campo = :$campo";
        }

        // Monta a string final para o SET
        $setQuery = implode(", ", $setPlaceholders);

        // Monta o SQL
        $sql = "UPDATE loja SET $setQuery WHERE id = :id";
        $editar->query($sql);

        // Associa os valores dinâmicos
        foreach ($dados as $campo => $valor) {
            $editar->bind(":$campo", $valor);
        }
        $editar->bind(":id", $id);

        // Executa e retorna o status
        return $editar->execute();
        
    }

    /**
     * Deletar uma loja.
     * O CD com id=2 não pode ser excluído (CD principal do sistema).
     */
    public function deletar($id)
    {
        $id = (int) $id;

        // Proteção: CD id=2 não pode ser excluído
        if ($id === 2) {
            return false;
        }

        $db = new db();

        // Verificar se a loja existe e obter o tipo
        $db->query("SELECT id, tipo FROM loja WHERE id = :id");
        $db->bind(":id", $id);
        $loja = $db->single();

        if (!$loja) {
            return false;
        }

        try {
            $db->beginTransaction();

            // 1. Deletar estoque da loja
            $db->query("DELETE FROM estoque_loja WHERE loja_id = :id");
            $db->bind(":id", $id);
            $db->execute();

            // 2. Atualizar referências para NULL onde possível
            $db->query("UPDATE movimentacao_estoque SET loja_id = NULL WHERE loja_id = :id");
            $db->bind(":id", $id);
            $db->execute();

            $db->query("UPDATE pedidos SET loja_id = NULL WHERE loja_id = :id");
            $db->bind(":id", $id);
            $db->execute();

            $db->query("UPDATE usuarios SET loja_id = NULL WHERE loja_id = :id");
            $db->bind(":id", $id);
            $db->execute();

            $db->query("UPDATE consignacao SET loja_id = NULL WHERE loja_id = :id");
            $db->bind(":id", $id);
            $db->execute();

            // 3. Verificar transferências (origem ou destino)
            $db->query("SELECT COUNT(*) as total FROM transferencia_estoque WHERE loja_origem_id = :id OR loja_destino_id = :id");
            $db->bind(":id", $id);
            $count = $db->single()['total'] ?? 0;

            if ($count > 0) {
                $db->cancelTransaction();
                return false; // Não exclui se houver transferências
            }

            // 4. Deletar a loja
            $db->query("DELETE FROM loja WHERE id = :id");
            $db->bind(":id", $id);
            $result = $db->execute();

            $db->endTransaction();
            return $result;
        } catch (\Exception $e) {
            if (isset($db)) {
                $db->cancelTransaction();
            }
            return false;
        }
    }

}