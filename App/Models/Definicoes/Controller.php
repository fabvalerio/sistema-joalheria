<?php

namespace App\Models\Definicoes;

use db; // Classe de conexão com o banco

class Controller
{
    // Listar todos os registros
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM produto_definicoes ORDER BY id DESC");
        return $db->resultSet();
    }

    // Obter um registro pelo ID
    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM produto_definicoes WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    // Cadastrar um novo registro
    public function cadastro($dados)
    {
        $db = new db();
        $db->query("INSERT INTO produto_definicoes (nome, tipo) VALUES (:nome, :tipo)");
        $db->bind(":nome", $dados['nome']);
        $db->bind(":tipo", $dados['tipo']);
        return $db->execute();
    }

    // Editar um registro pelo ID
    public function editar($id, $dados)
    {
        $db = new db();
        $db->query("UPDATE produto_definicoes SET nome = :nome, tipo = :tipo WHERE id = :id");
        $db->bind(":nome", $dados['nome']);
        $db->bind(":tipo", $dados['tipo']);
        $db->bind(":id", $id);
        return $db->execute();
    }

    // Deletar um registro pelo ID
    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM produto_definicoes WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }

    // (Opcional) Listar apenas Modelos
    public function listarModelos()
    {
        $db = new db();
        $db->query("SELECT id, nome FROM produto_definicoes WHERE tipo = 'modelo' ORDER BY nome");
        return $db->resultSet();
    }

    // (Opcional) Listar apenas Pedras
    public function listarPedras()
    {
        $db = new db();
        $db->query("SELECT id, nome FROM produto_definicoes WHERE tipo = 'pedra' ORDER BY nome");
        return $db->resultSet();
    }
    public function listarPorTipo($tipo)
{
    $db = new db();
    $db->query("SELECT * FROM produto_definicoes WHERE tipo = :tipo ORDER BY id DESC");
    $db->bind(":tipo", $tipo);
    return $db->resultSet();
}

}
