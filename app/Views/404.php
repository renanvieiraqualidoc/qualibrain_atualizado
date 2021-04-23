<?php if(session('username')): ?>
<?=$this->extend('layouts/default_layout'); ?>
<?=$this->section('content'); ?>
<?php endif; ?>
<div class="container-fluid">
    <div class="text-center">
        <div class="error mx-auto" data-text="404">404</div>
        <p class="lead text-gray-800 mb-5">Página não encontrada!</p>
        <a href="<?php echo site_url('/');?>">&larr; Voltar ao Dashboard</a>
    </div>
</div>
<?php if(session('username')): ?>
<?=$this->endSection(); ?>
<?php endif; ?>
