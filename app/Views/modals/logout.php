<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logout"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logout">Deseja Sair?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Clique em "Sair" se você deseja encerrar a sessão.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" href="<?php echo site_url('/auth/logout');?>">Sair</a>
            </div>
        </div>
    </div>
</div>
