<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <span class="sidebar-brand d-flex align-items-center justify-content-center">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-first-aid"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Qualibrain <sup>2</sup></div>
    </span>
    <hr class="sidebar-divider my-0">
    <li class="nav-item active">
        <span class="nav-link">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </span>
    </li>
    <hr class="sidebar-divider">
    <?php foreach($categories as $row):?>
    <?php if($row['show']):?>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="<?= "#collapse_".$row['id'];?>"
            aria-expanded="true" aria-controls="<?= "collapse_".$row['id'];?>">
            <i class="fas fa-fw <?=$row['icon'];?>"></i>
            <span><?=$row['functionality_name'];?></span>
        </a>
        <div id="<?= "collapse_".$row['id'];?>" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <?php foreach($row['subcategories'] as $sub):?>
                <?php if($sub['hasPermission']):?>
                <a class="collapse-item" href="<?php echo ($sub['page'] != '#') ? site_url($sub['page']) : '#';?>"><?=$sub['functionality_name'];?></a>
                <?php endif;?>
                <?php endforeach;?>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider">
    <?php endif;?>
    <?php endforeach;?>
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
