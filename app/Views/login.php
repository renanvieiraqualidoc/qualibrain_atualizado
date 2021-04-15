<!DOCTYPE html>
<html lang="en">
    <head>
        <title>QualiBrain - Login</title>
    </head>

    <body class="bg-gradient-primary">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-12 col-md-9">
                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col-lg-6 d-none d-lg-block"><img src="<?php echo base_url('img/logo.png'); ?>" width=100%></div>
                                <div class="col-lg-6">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">Bem Vindo!</h1>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" required class="form-control form-control-user"
                                                id="input_email" aria-describedby="emailHelp"
                                                placeholder="E-mail">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" required class="form-control form-control-user"
                                                id="input_password" placeholder="Senha">
                                        </div>
                                        <button id="btn_login" type="submit" class="btn btn-primary btn-user btn-block">Login</button>
                                        <hr>
                                        <div class="text-center">
                                            <a class="small" href="<?php echo site_url('login/forgot_password');?>">Esqueci minha senha</a>
                                        </div>
                                        <div class="text-center">
                                            <a class="small" href="register.php">Criar nova conta</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            $('#btn_login').click(function(){
                axios.post("<?php echo site_url('auth');?>", {
                    email: $('#input_email').val(),
                    password: $('#input_password').val()
                }).then((response) => {
                    console.log(response)
                }).catch((error) => {
                    throw error
                })
            })
            $(document).ready(function() {
                // alert();
            })
        </script>
    </body>
</html>
