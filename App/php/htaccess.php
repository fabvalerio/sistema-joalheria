<?php

// paginas htaccess ----------------------------------------------------

// Define a variável $url para sempre apontar para a raiz do site
if ($_SERVER['SERVER_NAME'] == 'localhost') {
   // Quando rodando localmente, incluindo a porta se necessário
   $port = ($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443') ? ':' . $_SERVER['SERVER_PORT'] : '';
   $url = "http://" . $_SERVER['SERVER_NAME'] . $port . '/';
} else {
   // Quando rodando em produção
   $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['SERVER_NAME'] . '/';
}

//URL Completa
$url_completa = $url . $_SERVER['REQUEST_URI'];

$url_site = $url; // O mesmo valor de $url é atribuído a $url_site


$verificarUrl = @explode('/', $url);
$verificarUrlWWW =  @explode('.', $verificarUrl[2]);


//explode link por "/" come�ando com o "0" .."1" .. "2" ... ... "20"
if( !empty($_GET['page']) ){
$link = explode('/', $_GET['page']);
}else{
   $link = [];
}

if( !empty($link) ){
   foreach( $link AS $key => $val ){
      define('url'.$key, $val);
   }
}


if( empty($link[0]) ){

   $paginaExibi = "pages/home.php";

}elseif( $link[0] == '!' ){

		
	if( is_dir("pages/".$link[1]) ){
		$paginaExibi = "pages/".$link[1]."/".$link[2].".php";
	}else{
        $paginaExibi = "404.html";
	}
	
}else{

    if( is_file("pages/".$link[0]."/".$link[1].".php") ){
      $paginaExibi = "pages/".$link[0]."/".$link[1].".php";
    }else{
       $paginaExibi = "404.html";
    }

}
//--------------------------------------------------------------