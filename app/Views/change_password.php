<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Trocar Senha</h1>
    </div>
    <div class="row">
        <div class="col-lg-3"></div>
        <div class="col-lg-6">
            <div class="p-5">
                <?php if(isset($validation)):?>
                  <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
                <?php endif;?>
                <form action="<?php echo site_url('profile/change');?>" method="post">
                    <div class="form-group">
                        <input type="password" class="form-control form-control-user" name="old_password" placeholder="Digite sua senha atual">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control form-control-user" name="new_password" placeholder="Digite sua nova senha">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control form-control-user" name="new_password_confirm" placeholder="Confirme sua senha nova">
                    </div>
                    <button type="submit" class="btn btn-primary btn-user btn-block">Trocar Senha</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?=$this->endSection(); ?>
