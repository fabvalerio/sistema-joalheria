<?php

ob_start();
session_start();

include 'db/db.class.php';
include 'app/php/htaccess.php';

$_cpf = trim($_POST['cpf']);
$_senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

try {

	$LoginSql = "SELECT u.id, nivel_acesso
			    	FROM usuarios as u
					WHERE u.cpf = '{$_cpf}'
					AND u.senha = '{$_senha}'
					AND u.status = 1
					";

	$Login = new db();
	$Login->query($LoginSql);
	$Login->execute();
	$resultLogin = $Login->object();

	if (!empty($resultLogin->id)) {

		//3600 dias * 24 horas
		setcookie("id", $resultLogin->id, time() + ((3600 * 24) * 7));
		setcookie("nivel_acesso", $resultLogin->nivel, time() + ((3600 * 24) * 7));

		header('location: ' . $url . '/');
	} else {
		echo 'erro';
		header('location: ' . $url . 'login.php?alert=error');
	}
} catch (PDOException $e) {
	throw new PDOException($e);
}
