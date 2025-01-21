<?php

namespace App\Models\ComissaoVendedor;

use db;

class Controller
{
    // Listar todas as comissões
    public function listar()
    {
        $db = new db();
        $db->query("SELECT cv.*, gp.nome_grupo AS grupo_produto, u.nome_completo AS vendedor 
                    FROM comissao_vendedor cv
                    INNER JOIN grupo_produtos gp ON cv.grupo_produtos_id = gp.id
                    INNER JOIN usuarios u ON cv.usuarios_id = u.id
                    ORDER BY u.nome_completo, gp.nome_grupo ASC");
        return $db->resultSet();
    }

    // Salvar comissões em lote para um usuário
    public function salvarComissoesPorUsuario($usuarioId, $comissoes, $comissoes2)
{
    $db = new db();

    // Deletar comissões existentes para o usuário
    $db->query("DELETE FROM comissao_vendedor WHERE usuarios_id = :usuarios_id");
    $db->bind(":usuarios_id", $usuarioId);
    if (!$db->execute()) {
        return false;
    }

    // Inserir novas comissões
    $db->query("INSERT INTO comissao_vendedor (usuarios_id, grupo_produtos_id, comissao_v, comissao_a) 
                VALUES (:usuarios_id, :grupo_produtos_id, :comissao_v, :comissao_a)");
    
    foreach ($comissoes as $grupo_produtos_id => $comissao_v) {
        $comissao_a = $comissoes2[$grupo_produtos_id] ?? 0; // Obtém a segunda comissão correspondente ou usa 0

        $db->bind(":usuarios_id", $usuarioId);
        $db->bind(":grupo_produtos_id", $grupo_produtos_id);
        $db->bind(":comissao_v", $comissao_v ?: 0); // Valor padrão para a primeira comissão
        $db->bind(":comissao_a", $comissao_a ?: 0); // Valor padrão para a segunda comissão

        if (!$db->execute()) {
            return false;
        }
    }

    return true;
}


    // Salvar comissões em lote para um grupo de produtos
    public function salvarComissoesPorGrupo($grupoId, $comissoes, $comissoes2)
{
    $db = new db();

    // Deletar comissões existentes para o grupo
    $db->query("DELETE FROM comissao_vendedor WHERE grupo_produtos_id = :grupo_produtos_id");
    $db->bind(":grupo_produtos_id", $grupoId);
    if (!$db->execute()) {
        return false;
    }

    // Inserir novas comissões
    $db->query("INSERT INTO comissao_vendedor (grupo_produtos_id, usuarios_id, comissao_v, comissao_a) 
                VALUES (:grupo_produtos_id, :usuarios_id, :comissao_v, :comissao_a)");
    
    foreach ($comissoes as $usuarios_id => $comissao_v) {
        $comissao_a = $comissoes2[$usuarios_id] ?? 0; // Obtém a segunda comissão correspondente ou usa 0

        $db->bind(":grupo_produtos_id", $grupoId);
        $db->bind(":usuarios_id", $usuarios_id);
        $db->bind(":comissao_v", $comissao_v ?: 0); // Valor padrão para a primeira comissão
        $db->bind(":comissao_a", $comissao_a ?: 0); // Valor padrão para a segunda comissão

        if (!$db->execute()) {
            return false;
        }
    }

    return true;
}


    // Listar grupos de produtos
    public function listarGruposProdutos()
    {
        $db = new db();
        $db->query("SELECT id, nome_grupo FROM grupo_produtos ORDER BY nome_grupo ASC");
        return $db->resultSet();
    }

    // Listar usuários
    public function listarUsuarios()
    {
        $db = new db();
        $db->query("SELECT id, nome_completo FROM usuarios ORDER BY nome_completo ASC");
        return $db->resultSet();
    }

    // Listar comissões existentes por usuário
    public function listarComissoesPorUsuario($usuarioId)
    {
        $db = new db();
        $db->query("SELECT grupo_produtos_id, comissao_v, comissao_a FROM comissao_vendedor WHERE usuarios_id = :usuarios_id");
        $db->bind(":usuarios_id", $usuarioId);
        $result = $db->resultSet();
    
        $comissoes = [];
        foreach ($result as $row) {
            $comissoes[$row['grupo_produtos_id']] = [
                'comissao_v' => $row['comissao_v'], // Primeira comissão
                'comissao_a' => $row['comissao_a'], // Segunda comissão
            ];
        }
    
        return $comissoes;
    }
    
    public function listarComissoesPorGrupo($grupoId)
{
    $db = new db();
    $db->query("SELECT usuarios_id, comissao_v, comissao_a FROM comissao_vendedor WHERE grupo_produtos_id = :grupo_produtos_id");
    $db->bind(":grupo_produtos_id", $grupoId);
    $result = $db->resultSet();

    $comissoes = [];
    foreach ($result as $row) {
        $comissoes[$row['usuarios_id']] = [
            'comissao_v' => $row['comissao_v'], // Primeira comissão
            'comissao_a' => $row['comissao_a'], // Segunda comissão
        ];
    }

    return $comissoes;
}

    

}
