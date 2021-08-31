<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/version.php';
?>
<header class="main-header">
    <!-- Logo -->
    <a href="index.php" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>A</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b><?php echo $APPNAME; ?></b> <small><?php echo $VERSION; ?></small></span>
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

                <?php if ($_SESSION['messages_ajout']==1 OR $_SESSION['messages_suppression']==1){ ?>
                    <li class="dropdown user user-menu">
                        <a href="messages.php"><i class="fa fa-bullhorn"></i></a>
                    </li>
                <?php } ?>

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Mon Compte</a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <p>
                                <?php echo $_SESSION['nomPersonne'];?> <?php echo $_SESSION['prenomPersonne'];?>
                                <small><?php echo $_SESSION['libelleProfil'];?></small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="user.php" class="btn btn-default btn-flat">Mon compte</a>
                            </div>
                            <div class="pull-right">
                                <a href="logout.php" class="btn btn-default btn-flat">Déconnexion</a>
                            </div>
                        </li>
                    </ul>
                </li>
                <li class="dropdown user user-menu">
                    <a href="logout.php"><i class="fa fa-sign-out"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>