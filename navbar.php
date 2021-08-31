<?php
session_start();
require_once('logCheck.php');
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li <?php
            if ($_SESSION['page'] == 000)
            {
                echo 'class="active"';
            }
            ?>
            ><a href="index.php"><i class="fa fa-home"></i> <span>Accueil</span></a></li>

            <li <?php
            if (((int)($_SESSION['page']/100))==1)
            {
                echo 'class="active treeview menu-open"';
            }
            else
            {
                echo 'class="treeview"';
            }
            ?>
            >
                <a href="#">
                    <span>GESTION DES LOTS</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($_SESSION['lots_lecture']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 101)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="lots.php"><i class="fa fa-ambulance"></i> <span>Lots</span></a></li>
                    <?php } ?>
                    <?php if ($_SESSION['sac_lecture']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 102)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="sacs.php"><i class="fa fa-medkit"></i> <span>Sacs</span></a></li>
                    <?php } ?>
                    <?php if ($_SESSION['sac2_lecture']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 103)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="emplacements.php"><i class="fa fa-plus-square"></i> <span>Emplacements</span></a></li>
                    <?php } ?>
                    <?php if ($_SESSION['materiel_lecture']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 104)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="materiels.php"><i class="fa fa-stethoscope"></i> <span>Matériel</span></a></li>
                    <?php } ?>
                </ul>
            </li>
            <li <?php
            if (((int)($_SESSION['page']/100))==2)
            {
                echo 'class="active treeview menu-open"';
            }
            else
            {
                echo 'class="treeview"';
            }
            ?>
            >
                <a href="#">
                    <span>REFERENTIELS</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($_SESSION['typesLots_lecture']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 201)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="referentiels.php"><i class="fa fa-bank"></i> <span>Référentiels</span></a></li>
                    <?php } ?>
                </ul>
            </li>
            <li <?php
            if (((int)($_SESSION['page']/100))==3)
            {
                echo 'class="active treeview menu-open"';
            }
            else
            {
                echo 'class="treeview"';
            }
            ?>
            >
                <a href="#">
                    <span>GESTION DE L'OUTIL</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($_SESSION['catalogue_lecture']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 301)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="catalogue.php"><i class="fa fa-book"></i> <span>Catalogue</span></a></li>
                    <?php } ?>
                    <?php if ($_SESSION['categories_lecture']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 302)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="categories.php"><i class="fa fa-table"></i> <span>Catégories matériels</span></a></li>
                    <?php } ?>
                    <?php if ($_SESSION['etats_lecture']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 303)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="etats.php"><i class="fa fa-dashboard"></i> <span>Etats des lots</span></a></li>
                    <?php } ?>
                    <?php if ($_SESSION['lieux_lecture']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 304)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="lieux.php"><i class="fa fa-map-signs"></i> <span>Lieux de stockage</span></a></li>
                    <?php } ?>
                    <?php if ($_SESSION['fournisseurs_lecture']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 305)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="fournisseurs.php"><i class="fa fa-shopping-cart"></i> <span>Fournisseurs</span></a></li>
                    <?php } ?>
                </ul>
            </li>
            <li <?php
            if (((int)($_SESSION['page']/100))==4)
            {
                echo 'class="active treeview menu-open"';
            }
            else
            {
                echo 'class="treeview"';
            }
            ?>
            >
                <a href="#">
                    <span>ANNUAIRE</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($_SESSION['annuaire_lecture']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 401)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="annuaire.php"><i class="fa fa-user"></i> <span>Utilisateurs</span></a></li>
                    <?php } ?>
                    <?php if ($_SESSION['profils_lecture']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 402)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="profils.php"><i class="fa fa-users"></i> <span>Profils</span></a></li>
                    <?php } ?>
                </ul>
            </li>
            <li <?php
            if (((int)($_SESSION['page']/100))==5)
            {
                echo 'class="active treeview menu-open"';
            }
            else
            {
                echo 'class="treeview"';
            }
            ?>
            >
                <a href="#">
                    <span>AUTRES</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($_SESSION['logs_lecture']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 501)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="logs.php?dateSup=<?php echo date('Y-m-d', strtotime(date('Y-m-d') . ' +' . ' 1 days')); ?>&dateInf=<?php echo date('Y-m-d', strtotime(date('Y-m-d') . ' -' . ' 30 days')); ?>"><i class="fa fa-history"></i> <span>Logs de la base</span></a></li>
                    <?php } ?>
                    <li <?php
                    if ($_SESSION['page'] == 503)
                    {
                        echo 'class="active"';
                    }
                    ?>
                    ><a href="documentation.php"><i class="fa fa-book"></i> <span>Documentation</span></a></li>
                    <li <?php
                    if ($_SESSION['page'] == 502)
                    {
                        echo 'class="active"';
                    }
                    ?>
                    ><a href="faq.php"><i class="fa fa-question"></i> <span>A Propos</span></a></li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>