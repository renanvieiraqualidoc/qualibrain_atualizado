<?=$this->extend('layouts/login_layout'); ?>
<?=$this->section('content'); ?>
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
                                <form action="<?php echo site_url('auth/login');?>" method="post">
                                    <div class="form-group">
                                        <input type="text" required class="form-control"
                                            name="username" placeholder="Nome de Usuário">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" required class="form-control"
                                            name="password" placeholder="Senha">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">Entrar</button>
                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="<?php echo site_url('qualiuser/forgot_password');?>">Esqueci minha senha</a>
                                </div>
                                <div class="text-center">
                                    <a class="small" href="<?php echo site_url('qualiuser');?>">Criar nova conta</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?=$this->endSection(); ?>
