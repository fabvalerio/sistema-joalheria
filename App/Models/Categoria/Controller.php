<?php

namespace App\Models\Categoria;

use db; // Classe de conexão com o banco

class Controller
{
    // Listar todos os categoria
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM categoria ORDER BY id ASC");
        return $db->resultSet();
    }

    // Obter um nome pelo ID
    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM categoria WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    // Cadastrar um novo nome
    public function cadastro($dados)
    {
        $db = new db();
        $db->query("INSERT INTO categoria (nome) VALUES (:nome)");
        $db->bind(":nome", $dados['nome']);
        return $db->execute();
    }

    // Editar um nome pelo ID
    public function editar($id, $dados)
    {
        $db = new db();
        $db->query("UPDATE categoria SET nome = :nome WHERE id = :id");
        $db->bind(":nome", $dados['nome']);
        $db->bind(":id", $id);
        return $db->execute();
    }

    // Deletar um nome pelo ID
    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM categoria WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }
}
