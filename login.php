<?php 

 $alerta = $_GET['alert'] ?? 0;

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="assets/css/sb-admin-2.css" rel="stylesheet">

    <style>
        body, html {
            height: 100%;
        }
    </style>

</head>

<body class="bg-gradient-white h-100">

    <div class="container h-100">

        <div class="row d-flex align-items-center justify-content-center h-100">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card bg-dark shadow-lg">
                    <div class="card-body">
                    
                        <div class="row">

                            <?php if( $alerta == 'error' ){ ?>
                                <div class="alert alert-danger" role="alert">
                                    CPF ou senha inválidos!
                                </div>
                            <?php } ?>

                            <div class="col-lg-6 d-flex align-items-center justify-content-center">
                                <img src="assets/logo.png" alt="Login" class="img-fluid">
                            </div>

                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4  mb-4 text-white">Login</h1>
                                    </div>
                                    <form class="user" action="validar.php">
                                        <div class="form-group my-3">
                                            <input type="text" class="form-control" id="cpf" name="cpf" aria-describedby="informe seu cpf" placeholder="CPF">
                                        </div>
                                        <div class="form-group my-3">
                                            <input type="password" class="form-control" id="senha" name="senha" placeholder="Senha">
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Acessar</button>
                                    </form>
                                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="assets/js/sb-admin-2.min.js"></script>

</body>

</html>