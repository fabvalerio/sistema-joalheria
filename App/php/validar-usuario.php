<?php

	#Validar User*
	if( empty( $_COOKIE['aluno_user'] ) ){
		echo '<meta http-equiv="refresh" content="0;URL='.$url.'aluno/login.php">';
		exit;
	}
