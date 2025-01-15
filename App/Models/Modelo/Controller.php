<?php

/*
**  -----------------------------------------------------------------------------------------------------------
**  Onde tiver MODELO, altere para o nome da tabela do SQL para melhor funcionalidade e gestão do código
**  -----------------------------------------------------------------------------------------------------------
**  Obs.: Pode ser criado vários "public function NomeFuncao()" conforme a necessidade, respeitando o padrão
**  dentro do "class Controller"
**  -----------------------------------------------------------------------------------------------------------
*/

namespace App\Models\Modelo;  // Altere para o nome "Modelo" para nome da Tabela do SQL
use db; // Importa a classe de conexão com o banco de dados

class Controller
{
    // Listar todos os registros
    public function listar()
    {
        $lista = new db();
        $lista->query("SELECT * FROM Modelo ORDER BY id DESC");
        $resultados = $lista->resultSet(); // Supondo que o método resultSet() retorna os resultados
        return $resultados;
    }

    // ver todos os registros em uma pagina conforme o $ID
    public function ver($id)
    {
        $ver = new db();
        $ver->query("SELECT * FROM Modelo WHERE id = :id");
        $ver->bind(":id", $id);
        $resultados = $ver->single();
        return $resultados ?: false;
    }

    // Cadastrar um novo registro, coforme o Array $dados enviado
    public function cadastro($dados)
    {
        $lista = new db();

        // Cria os placeholders dinâmicos
        $campos = implode(", ", array_keys($dados));
        $placeholders = ":" . implode(", :", array_keys($dados));

        // Monta o SQL
        $sql = "INSERT INTO Modelo ($campos) VALUES ($placeholders)";
        $lista->query($sql);

        // Associa os valores dinâmicos
        foreach ($dados as $campo => $valor) {
            $lista->bind(":$campo", $valor);
        }

        // Executa e retorna o status
        return $lista->execute();
    }

    // Editar um registro conforme o $ID e o Array $dados enviado
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
        $sql = "UPDATE Modelo SET $setQuery WHERE id = :id";
        $editar->query($sql);

        // Associa os valores dinâmicos
        foreach ($dados as $campo => $valor) {
            $editar->bind(":$campo", $valor);
        }
        $editar->bind(":id", $id);

        // Executa e retorna o status
        return $editar->execute();
        
    }

    // Deletar um registro conforme o $ID
    public function deletar($id)
    {
        $ver = new db();
        $ver->query("DELETE FROM Modelo WHERE id = :id");
        $ver->bind(":id", $id);
        $resultados = $ver->single();
        return $resultados ?: false;
    }
}