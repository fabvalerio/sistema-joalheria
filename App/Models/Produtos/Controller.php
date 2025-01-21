<?php

namespace App\Models\Produtos;

use db;

class Controller
{
    // Listar todos os produtos com joins para trazer os nomes relacionados
    public function listar()
    {
        $db = new db();
        $db->query("
            SELECT 
                p.id,
                p.descricao_etiqueta,
                p.modelo,
                p.macica_ou_oca,
                p.peso,
                p.preco_ql,
                f.nome_fantasia AS fornecedor,
                g.nome_grupo AS grupo,
                sg.nome_subgrupo AS subgrupo,
                c.nome AS cotacao,
                p.em_reais AS em_reais,
                p.capa AS capa
            FROM 
                produtos p
            LEFT JOIN fornecedores f ON p.fornecedor_id = f.id
            LEFT JOIN grupo_produtos g ON p.grupo_id = g.id
            LEFT JOIN subgrupo_produtos sg ON p.subgrupo_id = sg.id
            LEFT JOIN cotacoes c ON p.cotacao = c.id
            ORDER BY p.descricao_etiqueta
        ");
        return $db->resultSet();
    }

    // Ver um produto específico
    public function ver($id)
{
    $db = new db();
    $db->query("
        SELECT 
            p.id,
            p.descricao_etiqueta,
            p.modelo,
            p.macica_ou_oca,
            p.numeros,
            p.pedra,
            p.nat_ou_sint,
            p.peso,
            p.aros,
            p.cm,
            p.pontos,
            p.mm,
            p.unidade,
            p.estoque_princ,
            p.preco_ql,
            p.peso_gr,
            p.custo,
            p.margem,
            p.em_reais,
            p.capa,

            -- ESSENCIAIS PARA O SELECT FUNCIONAR
            p.fornecedor_id,
            p.grupo_id,
            p.subgrupo_id,
            p.cotacao,

            -- Caso queira manter o nome de cada um (para exibir em telas, etc):
            f.nome_fantasia AS fornecedor_nome,
            g.nome_grupo    AS grupo_nome,
            sg.nome_subgrupo AS subgrupo_nome,
            c.nome          AS cotacao_nome

        FROM 
            produtos p
        LEFT JOIN fornecedores f ON p.fornecedor_id = f.id
        LEFT JOIN grupo_produtos g ON p.grupo_id = g.id
        LEFT JOIN subgrupo_produtos sg ON p.subgrupo_id = sg.id
        LEFT JOIN cotacoes c ON p.cotacao = c.id
        WHERE p.id = :id
    ");
    $db->bind(":id", $id);
    return $db->single();
}


    // Cadastro de um novo produto
    public function cadastro($dados)
    {
        $db = new db();
        $db->query("
            INSERT INTO produtos (
                descricao_etiqueta, fornecedor_id, modelo, macica_ou_oca, numeros, pedra, 
                nat_ou_sint, peso, aros, cm, pontos, mm, grupo_id, subgrupo_id, unidade, 
                estoque_princ, cotacao, preco_ql, peso_gr, custo, margem, em_reais, capa
            ) VALUES (
                :descricao_etiqueta, :fornecedor_id, :modelo, :macica_ou_oca, :numeros, :pedra, 
                :nat_ou_sint, :peso, :aros, :cm, :pontos, :mm, :grupo_id, :subgrupo_id, :unidade, 
                :estoque_princ, :cotacao, :preco_ql, :peso_gr, :custo, :margem, :em_reais, :capa
            )
        ");

        foreach ($dados as $key => $value) {
            $db->bind(":$key", $value);
        }

        return $db->execute();
    }

    // Editar um produto existente
    public function editar($id, $dados)
{
    $db = new db();
    $db->query("
        UPDATE produtos SET
            descricao_etiqueta = :descricao_etiqueta,
            fornecedor_id = :fornecedor_id,
            modelo = :modelo,
            macica_ou_oca = :macica_ou_oca,
            numeros = :numeros,
            pedra = :pedra,
            nat_ou_sint = :nat_ou_sint,
            peso = :peso,
            aros = :aros,
            cm = :cm,
            pontos = :pontos,
            mm = :mm,
            grupo_id = :grupo_id,
            subgrupo_id = :subgrupo_id,
            unidade = :unidade,
            estoque_princ = :estoque_princ,
            cotacao = :cotacao,
            preco_ql = :preco_ql,
            peso_gr = :peso_gr,
            custo = :custo,
            margem = :margem,
            em_reais = :em_reais,
            capa = :capa
        WHERE id = :id
    ");

    foreach ($dados as $key => $value) {
        $db->bind(":$key", $value);
    }
    $db->bind(":id", $id);

    return $db->execute();
}


    // Excluir um produto
    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM produtos WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }

    // Listar fornecedores
    public function listarFornecedores()
    {
        $db = new db();
        $db->query("SELECT id, nome_fantasia FROM fornecedores ORDER BY nome_fantasia");
        return $db->resultSet();
    }

    // Listar grupos de produtos
    public function listarGrupos()
    {
        $db = new db();
        $db->query("SELECT id, nome_grupo FROM grupo_produtos ORDER BY nome_grupo");
        return $db->resultSet();
    }

    // Listar subgrupos de produtos
    public function listarSubgrupos()
    {
        $db = new db();
        $db->query("SELECT id, nome_subgrupo FROM subgrupo_produtos ORDER BY nome_subgrupo");
        return $db->resultSet();
    }

    // Listar cotações
    public function listarCotacoes()
    {
        $db = new db();
        $db->query("SELECT id, nome, valor FROM cotacoes ORDER BY nome");
        return $db->resultSet();
    }

    // Listar subgrupos de acordo com o grupo selecionado
    public function listarSubgruposPorGrupo($grupo_id)
    {
        $db = new db();
        $db->query("SELECT id, nome_subgrupo FROM subgrupo_produtos WHERE grupo_id = :grupo_id ORDER BY nome_subgrupo");
        $db->bind(":grupo_id", $grupo_id);
        return $db->resultSet();
    }
}
