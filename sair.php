<?php

// Inicia a saída do buffer para evitar problemas de envio de cabeçalho
ob_start();
session_start();

include 'php/htaccess.php';
include 'db/db.class.php';

function logout_user() {
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
            setcookie('session_token', '', time() - 3600, "/", "", true, true);
        } catch (PDOException $e) {
            // Log de erro (opcional)
            error_log("Erro ao remover sessão: " . $e->getMessage());
        }
    }

    // Remove outros cookies definidos
    if (isset($_COOKIE['admin_user'])) {
        setcookie('admin_user', '', time() - 3600, '/'); // Expirar o cookie em toda a aplicação
    }

    if (isset($_COOKIE['admin_nivel'])) {
        setcookie('admin_nivel', '', time() - 3600, '/'); // Expirar o cookie em toda a aplicação
    }

    // Destrói a sessão ativa
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }

    // Exibir mensagem de saída (opcional)
    echo '<h2>Saindo...</h2>';

    // Redirecionar para a página de login
    global $url; // Certifique-se de que a variável $url esteja configurada corretamente
    header("Location: {$url}admin/login.php");
    exit;
}

// Chamar a função de logout
logout_user();

ob_end_flush();
