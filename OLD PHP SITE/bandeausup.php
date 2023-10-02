<?php
session_start();
require_once('logCheck.php');
require_once 'config/config.php';
?>
<header class="main-header">
    <!-- Logo -->
    <a href="index.php" class="logo">
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg spinnerAttenteClick"><b><?php echo $APPNAME; ?></b> <small><?php echo $VERSION; ?></small></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Aggrandir/Réduire</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                	<a href="user.php" title="Mon Compte"><i class="fa fa-user"></i> Mon compte</a>
                </li>
                <li class="dropdown user user-menu">
                    <?php if ($_SESSION['DELEGATION_ACTIVE']==0) { ?>
                    	<a href="logout.php" title="Se déconnecter"><i class="fa fa-sign-out"></i> Déconnexion</a>
                    <?php } else { ?>
                    	<a href="loginDelegateBack.php" title="Quitter la délégation"><i class="fa fa-user-times"></i> Quitter la délégation</a>
                    <?php } ?>
                </li>
            </ul>
        </div>
    </nav>
</header>