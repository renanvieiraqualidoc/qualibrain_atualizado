<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Esqueci a senha</title>

    <!-- Custom fonts for this template-->
    <?php echo link_tag('vendor/fontawesome-free/css/all.min.css');?>
    <?php echo link_tag('https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i');?>

    <!-- Custom styles for this template-->
    <?php echo link_tag('css/sb-admin-2.min.css');?>
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Esqueceu sua senha?</h1>
                                        <p class="mb-4">Insira seu endereço de e-mail abaixo
                                            e nós te enviaremos um link para resetar sua senha</p>
                                    </div>
                                    <form class="user">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="E-mail">
                                        </div>
                                        <a href="login.html" class="btn btn-primary btn-user btn-block">
                                            Resetar Senha
                                        </a>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="register.html">Criar uma conta</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="login.html">Já tenho uma conta</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
    <?php echo script_tag('vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>

    <!-- Core plugin JavaScript-->
    <?php echo script_tag('vendor/jquery-easing/jquery.easing.min.js'); ?>

    <!-- Custom scripts for all pages-->
    <?php echo script_tag('js/sb-admin-2.min.js'); ?>

</body>

</html>
