<?php

ob_start();
session_start();

include 'db/db.class.php';
include 'app/php/htaccess.php';

$_email = trim($_POST['email']);
// $_email = generateHash(trim($_POST['email']));
$_senha = md5(trim($_POST['senha']));

try {

	$LoginSql = "SELECT u.id
			    	FROM Usuarios as u
					WHERE u.email = '{$_email}'
					AND u.senha = '{$_senha}'
					AND u.status = 1
					";

	$Login = new db();
	$Login->query($LoginSql);
	$Login->execute();
	$resultLogin = $Login->object();

	if (!empty($resultLogin->id)) {

		//3600 dias * 24 horas
		setcookie("admin_user", $resultLogin->id, time() + ((3600 * 24) * 7));
		// setcookie("admin_nivel", $resultLogin->nivel, time() + ((3600 * 24) * 7));

		echo 'logado';
		header('location: ' . $url . '/');
	} else {
		echo 'erro';
		header('location: ' . $url . 'login.php?alert=error');
	}
} catch (PDOException $e) {
	throw new PDOException($e);
}
