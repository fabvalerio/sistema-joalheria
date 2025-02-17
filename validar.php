<?php

ob_start();
session_start();

//print_r($_POST);

include 'db/db.class.php';
include 'app/php/htaccess.php';

function is_ip_blocked($ip)
{
	$db = new db();
	$sql = "SELECT attempts, last_attempt 
            FROM login_attempts 
            WHERE ip_address = :ip";
	$db->query($sql);
	$db->bind(":ip", $ip);
	$db->execute();
	$record = $db->single();

	if ($record) {
		$timeDiff = time() - strtotime($record->last_attempt);
		if ($record->attempts >= 5 && $timeDiff < 900) {
			return true; // Bloqueado por 15 minutos
		}
	}
	return false;
}

function increment_login_attempts($ip)
{
	$db = new db();
	$sql = "INSERT INTO login_attempts (ip_address, attempts, last_attempt) 
            VALUES (:ip, 1, NOW())
            ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()";
	$db->query($sql);
	$db->bind(":ip", $ip);
	$db->execute();
}

function login_user($cpf, $senha)
{
	$ip = $_SERVER['REMOTE_ADDR'];

	if (is_ip_blocked($ip)) {
		header("HTTP/1.1 429 Too Many Requests");
		die("Você excedeu o número de tentativas de login. Tente novamente mais tarde.");
	}

	try {
		$db = new db();
		$sql = "SELECT id, senha, nivel_acesso, nome_completo
                FROM usuarios 
                WHERE cpf = :cpf AND status = 1";
		$db->query($sql);
		$db->bind(":cpf", $cpf);
		$db->execute();
		$user = $db->single();

		if ($user && password_hash($senha, PASSWORD_DEFAULT)) {

			// Gerar cookies seguros
			setcookie("id", $user->id, time() + ((3600 * 24) * 7), "/", "", true, true);
			setcookie("nome", $user->nome_completo, time() + ((3600 * 24) * 7), "/", "", true, true);
			setcookie("nivel_acesso", $user->nivel_acesso, time() + ((3600 * 24) * 7), "/", "", true, true);

			header('location: ' . $url . '/');
			exit;
		}

		// Incrementar tentativas de login
		increment_login_attempts($ip);

		header('location: ' . $url . 'login.php?alert=error');
		exit;
	} catch (PDOException $e) {
		throw new PDOException("Erro ao conectar ao banco de dados: " . $e->getMessage());
	}
}

// Obter dados do POST
$_cpf = trim($_POST['cpf']);
$_senha = $_POST['senha'];



// Iniciar processo de login
login_user($_cpf, $_senha);
