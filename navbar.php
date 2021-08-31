<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
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
<li class="header">MODULES</li>
			<?php
			if ($_SESSION['lots_lecture']==1 OR $_SESSION['lots_ajout']==1 OR $_SESSION['lots_modification']==1 OR $_SESSION['lots_suppression']==1 OR $_SESSION['sac_lecture']==1 OR $_SESSION['sac_ajout']==1 OR $_SESSION['sac_modification']==1 OR $_SESSION['sac_suppression']==1 OR $_SESSION['sac2_lecture']==1 OR $_SESSION['sac2_ajout']==1 OR $_SESSION['sac2_modification']==1 OR $_SESSION['sac2_suppression']==1 OR $_SESSION['materiel_lecture']==1 OR $_SESSION['materiel_ajout']==1 OR $_SESSION['materiel_modification']==1 OR $_SESSION['materiel_suppression']==1)
			{
			?>
	            <li <?php
	            if (((int)($_SESSION['page']/100))==1)
	            {
	                echo 'class="active treeview"';
	            }
	            else
	            {
	                echo 'class="treeview"';
	            }
	            ?>
	            >
	                <a href="#">
	                    <i class="fa fa-medkit"></i> <span>LOTS OPERATIONNELS</span>
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
	                        ><a href="lots.php"><i class="fa fa-h-square"></i> <span>Lots</span></a></li>
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
	                        ><a href="emplacements.php"><i class="fa fa-cubes"></i> <span>Emplacements</span></a></li>
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
	        <?php
			}
			?>
			
			<?php
			if ($_SESSION['reserve_lecture']==1 OR $_SESSION['reserve_ajout']==1 OR $_SESSION['reserve_modification']==1 OR $_SESSION['reserve_suppression']==1)
			{
			?>
	            <li <?php
	            if (((int)($_SESSION['page']/100))==7)
	            {
	                echo 'class="active treeview"';
	            }
	            else
	            {
	                echo 'class="treeview"';
	            }
	            ?>
	            >
	                <a href="#">
	                    <i class="fa fa-archive"></i> <span>RESERVE</span>
	                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
	                </a>
	                <ul class="treeview-menu">
	                    <?php if ($_SESSION['reserve_lecture']==1){ ?>
	                        <li <?php
	                        if ($_SESSION['page'] == 701)
	                        {
	                            echo 'class="active"';
	                        }
	                        ?>
	                        ><a href="reserveConteneurs.php"><i class="fa fa-cube"></i> <span>Conteneurs</span></a></li>
	                    <?php } ?>
	                    <?php if ($_SESSION['reserve_lecture']==1){ ?>
	                        <li <?php
	                        if ($_SESSION['page'] == 702)
	                        {
	                            echo 'class="active"';
	                        }
	                        ?>
	                        ><a href="reserveMateriel.php"><i class="fa fa-stethoscope"></i> <span>Materiel</span></a></li>
	                    <?php } ?>
	                </ul>
	            </li>
	        <?php
			}
			?>
            
            <?php
			if ($_SESSION['commande_lecture']==1 OR $_SESSION['commande_ajout']==1 OR $_SESSION['commande_valider']==1 OR $_SESSION['commande_etreEnCharge']==1 OR $_SESSION['commande_abandonner']==1 OR $_SESSION['cout_lecture']==1 OR $_SESSION['cout_ajout']==1 OR $_SESSION['cout_etreEnCharge']==1 OR $_SESSION['cout_supprimer']==1)
			{
			?>
	            <li <?php
	            if (((int)($_SESSION['page']/100))==6)
	            {
	                echo 'class="active treeview"';
	            }
	            else
	            {
	                echo 'class="treeview"';
	            }
	            ?>
	            >
	                <a href="#">
	                    <i class="fa fa-shopping-cart"></i> <span>COMMANDES</span>
	                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
	                </a>
	                <ul class="treeview-menu">
	                    <?php if ($_SESSION['commande_ajout']==1){ ?>
	                        <li><a href="commandesForm.php"><i class="fa fa-plus"></i> <span>Nouvelle commande</span></a></li>
	                    <?php } ?>
	                    <?php if ($_SESSION['commande_lecture']==1){ ?>
	                        <li <?php
	                        if ($_SESSION['page'] == 601)
	                        {
	                            echo 'class="active"';
	                        }
	                        ?>
	                        ><a href="commandesToutes.php"><i class="fa fa-reorder"></i> <span>Toutes les commandes</span></a></li>
	                    <?php } ?>
	                    <?php if ($_SESSION['commande_lecture']==1){ ?>
	                        <?php
	                        $query = $db->prepare('SELECT COUNT(*) as nb FROM COMMANDES WHERE idEtat < 7;');
	                        $query->execute(array(
	                            'idPersonne' => $_SESSION['idPersonne']
	                        ));
	                        $data = $query -> fetch();
	                        ?>
	                        <li <?php
	                        if ($_SESSION['page'] == 607)
	                        {
	                            echo 'class="active"';
	                        }
	                        ?>
	                        ><a href="commandesNonCloses.php"><i class="fa fa-spinner"></i> <span>Non closes</span><?php if ($data['nb']>0) { ?><span class="pull-right-container"><small class="label pull-right bg-blue"><?php echo $data['nb'];?></small></span> <?php } ?></a></li>
	                    <?php } ?>
	                    <?php if ($_SESSION['commande_valider']==1){ ?>
	                        <?php
	                        $query = $db->prepare('SELECT COUNT(*) as nb FROM COMMANDES WHERE idValideur = :idPersonne AND idEtat = 2;');
	                        $query->execute(array(
	                            'idPersonne' => $_SESSION['idPersonne']
	                        ));
	                        $data = $query -> fetch();
	                        ?>
	                        <li <?php
	                        if ($_SESSION['page'] == 602)
	                        {
	                            echo 'class="active"';
	                        }
	                        ?>
	                        ><a href="commandesValidations.php"><i class="fa fa-map-signs"></i> <span>Je dois valider</span><?php if ($data['nb']>0) { ?><span class="pull-right-container"><small class="label pull-right bg-red"><?php echo $data['nb'];?></small></span> <?php } ?></a></li>
	                    <?php } ?>
	                    <?php if ($_SESSION['commande_etreEnCharge']==1){ ?>
	                        <?php
	                        $query = $db->prepare('SELECT COUNT(*) as nb FROM COMMANDES WHERE (idDemandeur = :idPersonne OR idObservateur = :idPersonne OR idAffectee = :idPersonne) AND (idEtat = 1 OR idEtat = 3);');
	                        $query->execute(array(
	                            'idPersonne' => $_SESSION['idPersonne']
	                        ));
	                        $data = $query -> fetch();
	                        ?>
	                        <li <?php
	                        if ($_SESSION['page'] == 603)
	                        {
	                            echo 'class="active"';
	                        }
	                        ?>
	                        ><a href="commandesTraiter.php"><i class="fa fa-file"></i> <span>Je dois traiter</span><?php if ($data['nb']>0) { ?><span class="pull-right-container"><small class="label pull-right bg-red"><?php echo $data['nb'];?></small></span> <?php } ?></a></li>
	                    <?php } ?>
	                    <?php if ($_SESSION['commande_etreEnCharge']==1){ ?>
	                        <?php
	                        $query = $db->prepare('SELECT COUNT(*) as nb FROM COMMANDES WHERE (idDemandeur = :idPersonne OR idObservateur = :idPersonne OR idAffectee = :idPersonne) AND (idEtat = 4 OR idEtat = 5 OR idEtat = 6);');
	                        $query->execute(array(
	                            'idPersonne' => $_SESSION['idPersonne']
	                        ));
	                        $data = $query -> fetch();
	                        ?>
	                        <li <?php
	                        if ($_SESSION['page'] == 604)
	                        {
	                            echo 'class="active"';
	                        }
	                        ?>
	                        ><a href="commandesSuivi.php"><i class="fa fa-truck"></i> <span>Je dois suivre</span><?php if ($data['nb']>0) { ?><span class="pull-right-container"><small class="label pull-right bg-blue"><?php echo $data['nb'];?></small></span> <?php } ?></a></li>
	                    <?php } ?>
	                    <?php if ($_SESSION['cout_lecture']==1){ ?>
	                        <li <?php
	                        if ($_SESSION['page'] == 606)
	                        {
	                            echo 'class="active"';
	                        }
	                        ?>
	                        ><a href="centreCouts.php"><i class="fa fa-euro"></i> <span>Centres de coûts</span></a></li>
	                    <?php } ?>
	                </ul>
	            </li>
	        <?php
			}
	        ?>
	        
	        <?php
			if ($_SESSION['reserve_cmdVersReserve']==1 OR $_SESSION['reserve_ReserveVersLot']==1)
			{
			?>
	            <li <?php
	            if (((int)($_SESSION['page']/100))==8)
	            {
	                echo 'class="active treeview"';
	            }
	            else
	            {
	                echo 'class="treeview"';
	            }
	            ?>
	            >
	                <a href="#">
	                    <i class="fa fa-exchange"></i> <span>TRANSFERTS</span>
	                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
	                </a>
	                <ul class="treeview-menu">
	                    <?php if ($_SESSION['reserve_lecture']==1){ ?>
	                        <li <?php
	                        if ($_SESSION['page'] == 801)
	                        {
	                            echo 'class="active"';
	                        }
	                        ?>
	                        ><a href="transfertCmdRes.php"><i class="fa fa-stethoscope"></i> <span>Commandes <i class="fa fa-arrow-right"></i> Reserve</span></a></li>
	                    <?php } ?>
	                    <?php if ($_SESSION['reserve_lecture']==1){ ?>
	                        <li <?php
	                        if ($_SESSION['page'] == 802)
	                        {
	                            echo 'class="active"';
	                        }
	                        ?>
	                        ><a href="transfertResLots.php"><i class="fa fa-stethoscope"></i> <span>Reserve <i class="fa fa-arrow-right"></i> Lots</span></a></li>
	                    <?php } ?>
	                </ul>
	            </li>
	        <?php
			}
			?>

            <?php
            if ($_SESSION['vhf_canal_lecture']==1 OR $_SESSION['vhf_canal_ajout']==1 OR $_SESSION['vhf_canal_modification']==1 OR $_SESSION['vhf_canal_suppression']==1 OR $_SESSION['vhf_plan_lecture']==1 OR $_SESSION['vhf_plan_ajout']==1 OR $_SESSION['vhf_plan_modification']==1 OR $_SESSION['vhf_plan_suppression']==1 OR $_SESSION['vhf_equipement_lecture']==1 OR $_SESSION['vhf_equipement_ajout']==1 OR $_SESSION['vhf_equipement_modification']==1 OR $_SESSION['vhf_equipement_suppression']==1)
            {
                ?>
                <li <?php
                if (((int)($_SESSION['page']/100))==9)
                {
                    echo 'class="active treeview"';
                }
                else
                {
                    echo 'class="treeview"';
                }
                ?>
                >
                    <a href="#">
                        <i class="fa fa-wifi"></i> <span>TRANSMISSIONS</span>
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ($_SESSION['vhf_canal_lecture']==1){ ?>
                            <li <?php
                            if ($_SESSION['page'] == 901)
                            {
                                echo 'class="active"';
                            }
                            ?>
                            ><a href="vhfCanaux.php"><i class="fa fa-tty"></i> <span>Canaux</span></a></li>
                        <?php } ?>
                        <?php if ($_SESSION['vhf_plan_lecture']==1){ ?>
                            <li <?php
                            if ($_SESSION['page'] == 902)
                            {
                                echo 'class="active"';
                            }
                            ?>
                            ><a href="vhfPlans.php"><i class="fa fa-sort-numeric-asc"></i> <span>Plans de fréquences</span></a></li>
                        <?php } ?>
                        <?php if ($_SESSION['vhf_equipement_lecture']==1){ ?>
                            <li <?php
                            if ($_SESSION['page'] == 903)
                            {
                                echo 'class="active"';
                            }
                            ?>
                            ><a href="vhfEquipements.php"><i class="fa fa-mobile"></i> <span>Equipements radio</span></a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php
            }
            ?>
            <?php
            if ($_SESSION['vehicules_lecture']==1 OR $_SESSION['vehicules_ajout']==1 OR $_SESSION['vehicules_modification']==1 OR $_SESSION['vehicules_suppression']==1)
            {
                ?>
                <li <?php
                if (((int)($_SESSION['page']/100))==10)
                {
                    echo 'class="active treeview"';
                }
                else
                {
                    echo 'class="treeview"';
                }
                ?>
                >
                    <a href="#">
                        <i class="fa fa-ambulance"></i> <span>VEHICULES</span>
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ($_SESSION['vehicules_lecture']==1){ ?>
                            <li <?php
                            if ($_SESSION['page'] == 1001)
                            {
                                echo 'class="active"';
                            }
                            ?>
                            ><a href="vehicules.php"><i class="fa fa-ambulance"></i> <span>Véhicules</span></a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php
            }
            ?>
	        
	        
	        
<li class="header">PARAMETRES</li>


            <?php
			if ($_SESSION['typesLots_lecture']==1 OR $_SESSION['typesLots_ajout']==1 OR $_SESSION['typesLots_modification']==1 OR $_SESSION['typesLots_suppression']==1)
			{
			?>
	            <li <?php
	            if (((int)($_SESSION['page']/100))==2)
	            {
	                echo 'class="active treeview"';
	            }
	            else
	            {
	                echo 'class="treeview"';
	            }
	            ?>
	            >
	                <a href="#">
	                    <i class="fa fa-bank"></i> <span>REFERENTIELS</span>
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
	        <?php
			}
			?>
			
			
            <?php
			if ($_SESSION['catalogue_lecture']==1 OR $_SESSION['catalogue_ajout']==1 OR $_SESSION['catalogue_modification']==1 OR $_SESSION['catalogue_suppression']==1 OR $_SESSION['categories_lecture']==1 OR $_SESSION['categories_ajout']==1 OR $_SESSION['categories_modification']==1 OR $_SESSION['categories_suppression']==1 OR $_SESSION['lieux_lecture']==1 OR $_SESSION['lieux_ajout']==1 OR $_SESSION['lieuxl_modification']==1 OR $_SESSION['lieux_suppression']==1 OR $_SESSION['fournisseurs_lecture']==1 OR $_SESSION['fournisseurs_ajout']==1 OR $_SESSION['fournisseurs_modification']==1 OR $_SESSION['fournisseurs_suppression']==1)
			{
			?>
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
	                    <i class="fa fa-table"></i> <span>CHAMPS ET LIBELLES</span>
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
	                    <?php if ($_SESSION['lieux_lecture']==1){ ?>
	                        <li <?php
	                        if ($_SESSION['page'] == 304)
	                        {
	                            echo 'class="active"';
	                        }
	                        ?>
	                        ><a href="lieux.php"><i class="fa fa-map-pin"></i> <span>Lieux de stockage</span></a></li>
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
	                    <?php if ($_SESSION['vehicules_types_lecture']==1){ ?>
	                        <li <?php
	                        if ($_SESSION['page'] == 306)
	                        {
	                            echo 'class="active"';
	                        }
	                        ?>
	                        ><a href="vehiculesTypes.php"><i class="fa fa-ambulance"></i> <span>Types de véhicules</span></a></li>
	                    <?php } ?>
	                </ul>
	            </li>
	        <?php
			}
			?>
            
            <?php
			if ($_SESSION['annuaire_lecture']==1 OR $_SESSION['annuaire_ajout']==1 OR $_SESSION['annuaire_modification']==1 OR $_SESSION['annuaire_suppression']==1 OR $_SESSION['annuaire_mdp']==1 OR $_SESSION['profils_lecture']==1 OR $_SESSION['profils_ajout']==1 OR $_SESSION['profils_modification']==1 OR $_SESSION['profils_suppression']==1)
			{
			?>
	            <li <?php
	            if (((int)($_SESSION['page']/100))==4)
	            {
	                echo 'class="active treeview"';
	            }
	            else
	            {
	                echo 'class="treeview"';
	            }
	            ?>
	            >
	                <a href="#">
	                    <i class="fa fa-users"></i> <span>ANNUAIRE</span>
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
	       <?php
			}
	       ?>
	       
            
            
            <li <?php
            if (((int)($_SESSION['page']/100))==5)
            {
                echo 'class="active treeview"';
            }
            else
            {
                echo 'class="treeview"';
            }
            ?>
            >
                <a href="#">
                    <i class="fa fa-wrench"></i> <span>AUTRES</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($_SESSION['verrouIP']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 504)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="verrouIP.php"><i class="fa fa-shield"></i> <span>Verrouillage IP</span></a></li>
                    <?php } ?>
                    <?php if ($_SESSION['appli_conf']==1){ ?>
                        <li <?php
                        if ($_SESSION['page'] == 505)
                        {
                            echo 'class="active"';
                        }
                        ?>
                        ><a href="appliConf.php"><i class="fa fa-wrench"></i> <span>Configuration générale</span></a></li>
                    <?php } ?>
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
                    ><a href="https://gpm.guajioty.fr" target="_blank"><i class="fa fa-book"></i> <span>Documentation</span></a></li>
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