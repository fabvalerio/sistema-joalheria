<?php

namespace App\Models\Material;

use db; // Classe de conexão com o banco

class Controller
{
    // Listar todos os material
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM material ORDER BY id ASC");
        return $db->resultSet();
    }

    // Obter um nome pelo ID
    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM material WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    // Cadastrar um novo nome
    public function cadastro($dados)
    {
        $db = new db();
        $db->query("INSERT INTO material (nome) VALUES (:nome)");
        $db->bind(":nome", $dados['nome']);
        return $db->execute();
    }

    // Editar um nome pelo ID
    public function editar($id, $dados)
    {
        $db = new db();
        $db->query("UPDATE material SET nome = :nome WHERE id = :id");
        $db->bind(":nome", $dados['nome']);
        $db->bind(":id", $id);
        return $db->execute();
    }

    // Deletar um nome pelo ID
    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM material WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }
}
