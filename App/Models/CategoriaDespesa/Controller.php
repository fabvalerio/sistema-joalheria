<?php

namespace App\Models\CategoriaDespesa;
use db;

class Controller
{
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM categoria_despesa ORDER BY descricao ASC");
        return $db->resultSet();
    }

    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM categoria_despesa WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    public function cadastro($dados)
    {
        $db = new db();
        $db->query("INSERT INTO categoria_despesa (descricao) VALUES (:descricao)");
        $db->bind(":descricao", $dados['descricao']);
        return $db->execute();
    }

    public function editar($id, $dados)
    {
        $db = new db();
        $db->query("UPDATE categoria_despesa SET descricao = :descricao WHERE id = :id");
        $db->bind(":descricao", $dados['descricao']);
        $db->bind(":id", $id);
        return $db->execute();
    }

    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM categoria_despesa WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }
}
?>
