<?php

    #conexao
    $userConexDb = "SELECT u.nome, u.email, u.id
                    FROM alunos as u
                    WHERE u.id = '{$_COOKIE['aluno_user']}'
                    ";

    $userConex = new db();	   
    $userConex->query($userConexDb);
    $userConex->execute();
    $user = $userConex->object();

    if( empty( $user->id ) ){
		echo '<meta http-equiv="refresh" content="0;URL='.$url.'aluno/login.php">';
		exit;
	}