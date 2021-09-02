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
                    <a href="baseDocumentaire.php" title="Base documentaire"><i class="fa fa-database"></i> Base documentaire</a>
                </li>
                
                <?php if ($_SESSION['messages_ajout']==1 OR $_SESSION['messages_suppression']==1){ ?>
                    <li class="dropdown user user-menu">
                        <a href="messages.php" title="Messages généraux"><i class="fa fa-bullhorn"></i> Messages généraux</a>
                    </li>
                <?php } ?>

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> Mon Compte</a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <p>
                                <?php echo $_SESSION['nomPersonne'];?> <?php echo $_SESSION['prenomPersonne'];?>
                                <small>Dernière connexion: <?php echo $_SESSION['derniereConnexion'];?></small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="user.php" class="btn btn-default btn-flat">Mon compte</a>
                            </div>
                            <div class="pull-right">
                            	<?php if ($_SESSION['DELEGATION_ACTIVE']==0) { ?>
                                	<a href="logout.php" class="btn btn-default btn-flat">Déconnexion</a>
                                <?php } else { ?>
                                	<a href="loginDelegateBack.php" class="btn btn-default btn-flat">Quitter la délégation</a>
                                <?php } ?>
                            </div>
                        </li>
                    </ul>
                </li>
                <li class="dropdown user user-menu">
                    <?php if ($_SESSION['DELEGATION_ACTIVE']==0) { ?>
                    	<a href="logout.php" title="Se déconnecter"><i class="fa fa-sign-out"></i> Se déconnecter</a>
                    <?php } else { ?>
                    	<a href="loginDelegateBack.php" title="Quitter la délégation"><i class="fa fa-user-times"></i> Quitter la délégation</a>
                    <?php } ?>
                </li>
            </ul>
        </div>
    </nav>
</header>