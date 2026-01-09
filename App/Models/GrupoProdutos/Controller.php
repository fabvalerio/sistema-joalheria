<?php

namespace App\Models\GrupoProdutos;
use db;

class Controller
{
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM grupo_produtos ORDER BY nome_grupo ASC");
        return $db->resultSet();
    }

    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM grupo_produtos WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    public function cadastro($dados)
    {
        $db = new db();
        $db->query("INSERT INTO grupo_produtos (nome_grupo, tempo) VALUES (:nome_grupo, :tempo)");
        $db->bind(":nome_grupo", $dados['nome_grupo']);
        $db->bind(":tempo", $dados['tempo']);
        return $db->execute();
    }

    public function editar($id, $dados)
    {
        $db = new db();
        $db->query("UPDATE grupo_produtos SET nome_grupo = :nome_grupo, tempo = :tempo WHERE id = :id");
        $db->bind(":nome_grupo", $dados['nome_grupo']);
        $db->bind(":tempo", $dados['tempo']);
        $db->bind(":id", $id);
        return $db->execute();
    }

    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM grupo_produtos WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }
}
?>
