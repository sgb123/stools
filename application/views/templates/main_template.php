<?php
    $logged_in = $this->display_lib->is_loggedin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" type="text/css" />
    <link rel="stylesheet" href="<?=site_url('js/bootstrap/css/bootstrap.min.css');?>" type="text/css" />
    <link rel="stylesheet" href="<?=site_url('js/jGrowl/css/jquery.jgrowl.css');?>" type="text/css"/>
    <link rel="stylesheet" href="<?=site_url('js/DataTables/css/jquery.dataTables.css');?>" type="text/css" />
    <link rel="stylesheet" href="<?=site_url('css/style.css');?>" type="text/css" media="screen, projection" />
    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?=site_url('js/bootstrap/js/bootstrap.min.js');?>"></script>
    <script type="text/javascript" src="<?=site_url('js/jGrowl/js/jquery.jgrowl.js');?>"></script>
    <script type="text/javascript" src="<?=site_url('js/DataTables/js/jquery.dataTables.min.js');?>"></script>
    <script type="text/javascript" src="<?=site_url('js/functions.js');?>"></script>
    <script type="text/javascript" src="<?=site_url('js/main.js');?>"></script>
</head>
<body>
<div class="container">
    <header></header>
    <div class="tabbable">
        <div class="auth-container">
            <?php if(!$logged_in):?>
                <a href="<?=site_url('main/login');?>" class="auth-link"><i class="icon-user"></i>Login</a>
            <?php else:?>
                <a href="<?=site_url('main/logout');?>" class="auth-link"><i class="icon-off"></i>Log out</a>
            <?php endif;?>
        </div>
        <div class="navbar">
            <div class="navbar-inner">
                <?php
                    $uri_segment_action = $this->uri->segment(2);
                    $page_rank_class = ($uri_segment_action && $uri_segment_action == 'page_rank') ? 'active' : '';
                    $billing_class = ($uri_segment_action && $uri_segment_action == 'billing') ? 'active' : '';
                    $account_class = (!$uri_segment_action) ? 'active' : '';
                ?>
                <ul class="nav">
                    <li class="<?=$account_class;?>"><a href="<?=site_url('main');?>">Account</a></li>
                    <li class="<?=$page_rank_class;?>"><a href="<?=site_url('main/page_rank');?>">Page Rank</a></li>
                    <li class="<?=$billing_class;?>"><a href="<?=site_url('main/billing');?>">Billing</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane active content" id="tab-content">
            <?=$content;?>
        <div>
    </div>
    <div id="progress-modal" class="modal progress-modal hide">
        <div class="progress progress-striped active">
            <div class="bar" style="width: 100%;"></div>
        </div>
    </div>
    <footer>
        <div class="span5">Page rendered in <strong>{elapsed_time}</strong> seconds</div>
    </footer>
</body>
</html>