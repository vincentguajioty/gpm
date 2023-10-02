<?php
session_start();
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
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="logout.php"><i class="fa fa-sign-out"></i> Quitter</a>
                </li>
            </ul>
        </div>
    </nav>
</header>