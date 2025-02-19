<?php
// Inicia a saída do buffer para evitar problemas de envio de cabeçalho
ob_start();
session_start();

include 'php/htaccess.php';
include 'db/db.class.php';

// Função para logout do usuário
function logout_user() {
    global $url; // Garantir que a variável $url esteja disponível

    // Se a variável $url não estiver definida, definir um valor padrão
    if (!isset($url)) {
        $url = "/"; // Redireciona para a raiz do site caso $url não esteja configurado corretamente
    }

    // Verifica se há cookies de sessão configurados
    if (isset($_COOKIE['session_token'])) {
        $sessionToken = $_COOKIE['session_token'];

        try {
            // Conexão com o banco de dados
            $db = new db();

            // Remover sessão do banco de dados
            $sql = "DELETE FROM sessions WHERE session_token = :session_token";
            $db->query($sql);
            $db->bind(':session_token', $sessionToken);
            $db->execute();

            // Expirar o cookie "session_token"
            setcookie('session_token', '', time() - 3600, "/", "", false, true);
        } catch (PDOException $e) {
            // Log de erro (opcional)
            error_log("Erro ao remover sessão do banco: " . $e->getMessage());
        }
    }

    // Remover cookies de autenticação
    $cookies = ['id', 'nome', 'nivel_acesso', 'admin_user', 'admin_nivel'];
    foreach ($cookies as $cookie) {
        if (isset($_COOKIE[$cookie])) {
            setcookie($cookie, '', time() - 3600, '/', '', false, true);
        }
    }

    // Destruir a sessão ativa
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }

    // Mensagem de saída para depuração (pode ser removida em produção)
    error_log("Usuário deslogado com sucesso!");

    // Redirecionar para a página de login
    header("Location: {$url}login.php");
    exit;
}

// Chamar a função de logout
logout_user();

ob_end_flush();
