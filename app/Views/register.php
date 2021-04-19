<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Crie sua conta</title>
    </head>
    <body class="bg-gradient-primary">
        <div class="container">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                        <div class="col-lg-7">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Crie uma nova conta</h1>
                                </div>
                                <?php if(isset($validation)):?>
                                  <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
                                <?php endif;?>
                                <form action="/qualiuser/register" method="post">
                                    <div class="form-group">
                                        <input type="text" class="form-control" value="<?= set_value('username') ?>" required
                                            name="username" placeholder="Nome de usuário">
                                    </div>
                                    <div class="form-group">
                                        <select class="form-control" name="permission_group" required>
                                            <option value="">Selecione um grupo</option>
                                            <?php foreach($permissions as $row):?>
                                            <option value="<?php echo $row->id;?>"><?php echo $row->group_name;?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" class="form-control" required
                                            value="<?= set_value('email') ?>" name="email" placeholder="Endereço de email">
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input type="password" class="form-control" required
                                                name="password" placeholder="Senha">
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="password" class="form-control" required
                                                name="confpassword" placeholder="Confirme sua senha">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">Cadastrar</button>
                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="<?php echo site_url('qualiuser/forgot_password');?>">Esqueci minha senha</a>
                                </div>
                                <div class="text-center">
                                    <a class="small" href="<?php echo site_url('/');?>">Já tem uma conta? Entre!</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
