<?php

namespace App\Models\SubGrupoProdutos;
use db;

class Controller
{
    // Listar todos os subgrupos
    public function listar()
    {
        $db = new db();
        $db->query("SELECT sg.id, sg.nome_subgrupo, g.nome_grupo FROM subgrupo_produtos sg 
                     JOIN grupo_produtos g ON sg.grupo_id = g.id 
                     ORDER BY sg.id ASC");
        return $db->resultSet();
    }

    // Ver um subgrupo específico
    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT sg.id, sg.nome_subgrupo, sg.grupo_id, g.nome_grupo 
                    FROM subgrupo_produtos sg 
                    JOIN grupo_produtos g ON sg.grupo_id = g.id 
                    WHERE sg.id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    // Cadastrar um subgrupo
    public function cadastro($dados)
    {
        $db = new db();
        $db->query("INSERT INTO subgrupo_produtos (nome_subgrupo, grupo_id) VALUES (:nome_subgrupo, :grupo_id)");
        $db->bind(":nome_subgrupo", $dados['nome_subgrupo']);
        $db->bind(":grupo_id", $dados['grupo_id']);
        return $db->execute();
    }

    // Editar um subgrupo
    public function editar($id, $dados)
    {
        $db = new db();
        $db->query("UPDATE subgrupo_produtos SET nome_subgrupo = :nome_subgrupo, grupo_id = :grupo_id WHERE id = :id");
        $db->bind(":nome_subgrupo", $dados['nome_subgrupo']);
        $db->bind(":grupo_id", $dados['grupo_id']);
        $db->bind(":id", $id);
        return $db->execute();
    }

    // Deletar um subgrupo
    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM subgrupo_produtos WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }

    // Listar os grupos de produtos para o select
    public function listarGrupos()
    {
        $db = new db();
        $db->query("SELECT id, nome_grupo FROM grupo_produtos ORDER BY nome_grupo ASC");
        return $db->resultSet();
    }
}
?>
