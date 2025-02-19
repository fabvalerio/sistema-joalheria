<?php 
    $alerta = $_GET['alert'] ?? 0;
    session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                            <?php if ($alerta == 'error') { ?>
                                <div class="alert alert-danger">CPF ou senha inválidos!</div>
                            <?php } ?>

                            <div class="col-lg-6 d-flex align-items-center justify-content-center">
                                <img src="assets/logo.png" alt="Login" class="img-fluid">
                            </div>

                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 mb-4 text-white">Login</h1>
                                    </div>
                                    <form action="validar.php" method="post">
                                        <div class="form-group my-3">
                                            <input type="text" class="form-control" name="cpf" placeholder="CPF" required>
                                        </div>
                                        <div class="form-group my-3">
                                            <input type="password" class="form-control" name="senha" placeholder="Senha" required>
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

</body>
</html>
