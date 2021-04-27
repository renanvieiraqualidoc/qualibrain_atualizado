<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Qualibrain</title>
        <?php echo link_tag('vendor/fontawesome-free/css/all.min.css');?>
        <?php echo link_tag('https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i');?>
        <?php echo link_tag('css/sb-admin-2.min.css');?>
    </head>
    <body id="page-top">
        <div id="wrapper">
        <?php if(session('username')): ?>
        <?php echo view('components/sidebar');?>
        <?php endif; ?>
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <?php if(session('username')): ?>
                    <?php echo view('components/header');?>
                    <?php endif; ?>
                    <div class="container-fluid">
                        <div class="text-center">
                            <div class="error mx-auto" data-text="Acesso Negado">Acesso Negado!</div>
                            <p class="lead text-gray-800 mb-5"></p>
                            <p class="text-gray-500 mb-0">Você não possui acesso a essa página!</p>
                        </div>
                    </div>
                </div>
                <?php if(session('username')): ?>
                <?php echo view('components/footer');?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Scroll to Top Button-->
        <?php if(session('username')): ?>
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>
        <?php echo view('modals/logout');?>
        <?php endif; ?>
    </body>
</html>

<?php echo script_tag('vendor/jquery/jquery.min.js'); ?>
<?php echo script_tag('vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>
<?php echo script_tag('vendor/jquery-easing/jquery.easing.min.js'); ?>
<?php echo script_tag('js/sb-admin-2.min.js'); ?>
