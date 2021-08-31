<!DOCTYPE html>
<html>
<?php include('header.php'); ?>
<?php
session_start();
$_SESSION['page'] = 502;
require_once('logCheck.php');
require_once 'config/version.php';
?>
<body class="hold-transition skin-blue sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                A propos
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">A propos</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-8">
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <i class="fa fa-clock-o"></i>

                            <h3 class="box-title">Evolution d'Apollon</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="timeline">
                                        <li class="time-label">
                                              <span class="bg-purple">
                                                Version 1.6
                                              </span>
                                        </li>
                                        <li>
                                            <i class="fa bg-green"></i>
                                            <div class="timeline-item">
                                                <span class="time">1 octobre 2017</span>
                                                <h3 class="timeline-header">Livraison en production de la version 1.6</h3>
                                            </div>
                                        </li>
                                        <li>
                                            <i class="fa bg-green"></i>
                                            <div class="timeline-item">
                                                <span class="time">27 Septembre 2017</span>
                                                <h3 class="timeline-header">Livraison en recette de la version 1.6</h3>
                                                <div class="timeline-body">
                                                    Nouveautés par rapport à la version précédente:
                                                    <ul>
                                                        <li>Blocage des adresses IP après 3 echecs de connexion</li>
                                                    </ul>
                                                    Corrections apportées:
                                                    <ul>
                                                        <li>Affichage du numéro de version de l'application sur la page d'identification</li>
                                                        <li>Mise à jour de la documentation</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="time-label">
                                              <span class="bg-purple">
                                                Version 1.5
                                              </span>
                                        </li>
										<li>
                                            <i class="fa bg-green"></i>
                                            <div class="timeline-item">
                                                <span class="time">31 Aout 2017</span>
                                                <h3 class="timeline-header">Livraison en production de la version 1.5</h3>
                                            </div>
                                        </li>
                                        <li>
                                            <i class="fa bg-green"></i>
                                            <div class="timeline-item">
                                                <span class="time">1 juillet 2017</span>
                                                <h3 class="timeline-header">Livraison en recette de la version 1.5</h3>
                                                <div class="timeline-body">
                                                    Corrections apportées:
                                                    <ul>
                                                        <li>Bandeau suppérieur restauré pour les versions mobiles</li>
                                                        <li>MAJ de la documentation</li>
                                                        <li>Icone dans les favoris et dans l'onglet du site</li>
														<li>Boucle de nettoyage journalier des logs de plus de 90 jours</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="time-label">
                                              <span class="bg-purple">
                                                Version 1.4
                                              </span>
                                        </li>
                                        <li>
                                            <i class="fa bg-green"></i>
                                            <div class="timeline-item">
                                                <span class="time">09 Juin 2017</span>
                                                <h3 class="timeline-header">Livraison en recette de la version 1.4</h3>
                                                <div class="timeline-body">
                                                    Nouveautés par rapport à la version précédente:
                                                    <ul>
                                                        <li>Bandeau de menu à gauche figé verticalement</li>
                                                        <li>Possibilité d'ajouter et de supprimer des utilisateurs depuis la fiche d'un profil</li>
                                                        <li>Possibilité de choisir entre aucune notification mail, notification sur alerte uniquement, notification journalière permanente</li>
                                                    </ul>
                                                    Corrections apportées:
                                                    <ul>
                                                        <li>Sur la page d'accueil, revue du design des 4 indicateurs généraux</li>
                                                        <li>Travaux sur la documentation.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="time-label">
                                              <span class="bg-purple">
                                                Version 1.3
                                              </span>
                                        </li>
                                        <li>
                                            <i class="fa bg-green"></i>
                                            <div class="timeline-item">
                                                <span class="time">06 Juin 2017</span>
                                                <h3 class="timeline-header">Livraison en recette de la version 1.3</h3>
                                                <div class="timeline-body">
                                                    Corrections apportées:
                                                    <ul>
                                                        <li>Augmentation de la fiabilité lors des pré-chargements de formulaires + supression d'erreurs de pré-chargements</li>
                                                        <li>Amélioration de la confidentialité des adresse mails dans les notifications</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="time-label">
                                              <span class="bg-purple">
                                                Version 1.2
                                              </span>
                                        </li>
                                        <li>
                                            <i class="fa bg-green"></i>
                                            <div class="timeline-item">
                                                <span class="time">02 Juin 2017</span>
                                                <h3 class="timeline-header">Livraison en recette de la version 1.2</h3>
                                                <div class="timeline-body">
                                                    Nouveautés par rapport à la version précédente:
                                                    <ul>
                                                        <li>Inventaires: possibilité de lancer un inventaire avec historisation des quantites et peremptions</li>
                                                    </ul>
                                                    Corrections apportées:
                                                    <ul>
                                                        <li>Facilitation de la navigation sur la plateforme avec recablage des boutons de retour et de validation des formulaires</li>
                                                        <li>Recherche dans les logs</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="time-label">
                                              <span class="bg-purple">
                                                Version 1.1
                                              </span>
                                        </li>
                                        <li>
                                            <i class="fa bg-green"></i>
                                            <div class="timeline-item">
                                                <span class="time">01 Juin 2017</span>
                                                <h3 class="timeline-header">Livraison en recette de la version 1.1</h3>
                                                <div class="timeline-body">
                                                    Nouveautés par rapport à la version précédente:
                                                    <ul>
                                                        <li>Notifications par mail</li>
                                                        <li>Impression d'inventaires des lots</li>
                                                        <li>Sur la fiche détaillée d'un lot, affichage explicit de l'analyse de conformité par rapport au référentiel</li>
                                                        <li>Création de la page A Propos avec descriptif des releases</li>
                                                        <li>Obligation de changer son mot de passe à la première connexion ou après une ré-initialisation de mot de passe</li>
                                                        <li>Création d'une section documentation</li>
                                                    </ul>
                                                    Corrections apportées:
                                                    <ul>
                                                        <li>Corrections de design sur la page d'accueil</li>
                                                        <li>Augmentation de la taille des boutons</li>
                                                        <li>Revue du design des liens d'ajout</li>
                                                        <li>Correction de l'algorithme de vérification de conformité des lots</li>
                                                        <li>Correction d'éventuels problèmes sur l'affichage des bas de page</li>
                                                        <li>Modification de la page des logs avec mise en place d'un tableau interractif</li>
                                                        <li>Menu général revu pour que les élements soient regroupables et que la page en cours soit en surbrillance</li>
                                                        <li>Simplification de remplissage des formulaires avec listes dynamiques</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="time-label">
                                              <span class="bg-purple">
                                                Version 1.0
                                              </span>
                                        </li>
                                        <li>
                                            <i class="fa bg-green"></i>
                                            <div class="timeline-item">
                                                <span class="time">29 Mai 2017</span>
                                                <h3 class="timeline-header">Livraison en recette de la version 1.0</h3>
                                                <div class="timeline-body">
                                                    Déploiement sur un environnement de recette de l'interface web. Fonctionnalités disponibles:
                                                    <ul>
                                                        <li>Gestion des éléments du parc matériel</li>
                                                        <li>Gestion des emplacements qui constituent les sacs opérationnels</li>
                                                        <li>Gestion des sacs opérationnels qui constituent les lots</li>
                                                        <li>Gestion des lots opérationnels</li>
                                                        <li>Gestion des référentiels</li>
                                                        <li>Gestion du catalogue de matériel</li>
                                                        <li>Gestion des fournisseurs</li>
                                                        <li>Gestion des status des lots</li>
                                                        <li>Gestion des catégories de matériel</li>
                                                        <li>Gestion des lieux de stockage</li>
                                                        <li>Gestion des utilisateurs d'Apollon</li>
                                                        <li>Gestion des mots de passe avec mise en place d'un hash</li>
                                                        <li>Gestion des profils des utilisateurs</li>
                                                        <li>Gestion des messages généraux affichables sur la page d'accueil</li>
                                                        <li>Gestion des logs</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="time-label">
                                              <span class="bg-purple">
                                                ORIGINE
                                              </span>
                                        </li>
                                        <li>
                                            <i class="fa bg-green"></i>

                                            <div class="timeline-item">
                                                <span class="time">1er Mai 2017</span>
                                                <h3 class="timeline-header">Naissance du projet</h3>
                                                <div class="timeline-body">
                                                    Constat simple établi: les associations de secours ainsi que tout autre corps du secourisme ont besoin d'un outil performant pour gérer leur parc matériel. L'objectif est d'arréter de perdre du temps, d'optimiser les taches, d'avoir un vrai suivi des consommables, des dates de péremption, des stocks ... Dans le meilleur des cas, les associations ont déjà une base de données, un fichier Excel, ou une autre solution pour stocker les données relatives à leur parc matériel mais ce qui leur fait défaut, c'est un bon outil pour les exploiter.
                                                    <br/><br/>
                                                    Contrainte utilisateur: l'outil doit être facile à utiliser, sur tous les appareils (PC, SmartPhone ...). L'outil doit être consultable à tout moment et doit être constamment à jour.
                                                    <br/><br/>
                                                    Contrainte technique: il ne faut pas que l'organisme utilisateur ait à acheter/déployer une infrastructure trop importante pour supporter la solution.
                                                    <br/><br/>
                                                    Bilan: concevoir une base de données avec une interface web qui permet de l'exploiter.
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <i class="fa fa-clock-o bg-gray"></i>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <i class="fa fa-wrench"></i>

                            <h3 class="box-title">Idées pour les futurs développements</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <ul>
                                <li>Possiblité de réinitialiser un mot de passe oublié par mail</li>
                                <li>Sur la page matériel, au-dessus du tableau récap, mettre un calendrier en vue mensuelle avec un point rouge sur toutes les dates de péremption des éléments</li>
                                <li>Nouvelle fonctionnalité: gestion de la reserve de matériel. Pour le moment c'est faisable en assimilant la réserve à un lot. Idée: avoir un système spécifique à la réserve</li>
                                <li>Nouvelle fonctionnalité: demandes d'achat de consommables. Possibilité de créer la demande, affecter la gestion de l'achat à un user, marquer la demande comme étant commandée, réceptionnée, ...</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="box box-info">

                        <div class="box-header with-border">
                            <i class="fa fa-bank"></i>

                            <h3 class="box-title">Copyright et Version</h3>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            Soluton Apollon: <strong>Copyright &copy; Vincent Guajioty.</strong> All rights reserved. - Version <?php echo $VERSION; ?>
                            <br/>
                            Thème HTML: <strong>Copyright &copy; 2014-2016 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights reserved. - Version 2.3.8
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->
    <?php include('footer.php'); ?>


    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<?php include('scripts.php'); ?>
</body>
</html>
