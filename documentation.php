<!DOCTYPE html>
<html>
<?php
session_start();
$_SESSION['page'] = 503;
require_once('logCheck.php');
?>
<?php include('header.php'); ?>
<body class="hold-transition skin-blue sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Documentation
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Documentation</li>
            </ol>
            <?php include('confirmationBox.php'); ?>
        </section>

        <section class="content">

        <!-- Main content -->
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Introduction</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl>
                            <dd>Apollon est un produit permettant une meilleure gestion d'un parc matériel. Développé pour les associations de secourisme ou autres organismes utilisant du matériel de secours, Apollon se veut être une solution modelable afin de répondre aux attentes les plus contraignantes.
                                <br/>
                                Afin de tirer le maximum d'avantages d'Apollon, il est recommandé de prendre le temps de bien configurer l'outil. L'efficacité des fonctionnalités visuelles et des notifications est liée au bon paramétrage des différents éléments.</dd>
                        </dl>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>


            <!-- ============================================================= -->

            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Fonctionnement général</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl>
                            <dd>Le tronc central d'Apollon est le catalogue de matériel. Ce catalogue permet de saisir les différents matériels de votre parc, de manière générique. On ne va en effet pas saisir "Compresses du Lot 1", mais juste "Compresses". C'est sur la base de ce catalogue que vous pourrez créer vos lots et référentiels.</dd>
                            <dd>Tous les champs du catalogue sont importants. Par exemple, si le champ "Stérilité" est mal rempli, des alertes non-pertinantes pourraient partir par la suite.</dd>
                            <br/>
                            <dd>Une fois le catalogue renseigné, vous pouvez passer à la création des référentiels, ou à la création des lots.</dd>
                            <br/>
                            <dd>Un référentiel, c'est une liste de matériel que doit contenir tout lot qui lui est rattaché. Il ne s'agit pas forcément d'un référentiel nationnal conforme à ceux dictés dans le Référentiel Nationnal des Dispositif de Premiers Secours. Vous êtes libres de concocter vos propres référentiels. Il est toute fois conseiller de se baser sur le RNDPS. Les lots se verront ensuite rattaché aux référentiels et Apollon vérifiera si vos lots sont conformes à leurs référentiels.</dd>
                            <br/>
                            <dd>Un lot, c'est un ensemble de sacs. Un sac c'est un ensemble d'emplacements. Et un emplacement contient un ensemble de matériels. Donc pour créer un lot, il faut commencer par déclarer le lot dans l'onglet <i>Lots</i>, puis créer les sacs qui composent ce lot en les affectant au lot créé (opération à faire dans l'onglet <i>Sacs</i>), puis créer les emplacement dans les sacs (onglet <i>Emplacements</i>). Une fois que cette structure est crée, vous n'avez plus qu'à ajouter du matériel dans les emplacements via l'onglet <i>Matériel</i>. Afin d'avoir une vue globale du matériel et de sa disponisition dans un lot, allez dans l'onglet <i>Lots</i> et cliquez sur le dossier bleu afin d'ouvrir la fiche détaillée du lot sur laquelle apparaitra la structure du lot (sacs, emplacements, matériels).</dd>
                        </dl>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <!-- ============================================================= -->

            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Suppression de données</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl>
                            <dd>La suppression de données est facile dans Apollon. Il suffit de cliquer sur les petites corbeilles rouges et d'accepter le message d'avertissement. ATTENTION A CE QUE VOUS SUPPRIMEZ ! Voici un tableau non-exhaustif des conséquences des suppressions:</dd>
                            <div class="box-body">
                                <dl>
                                    <dt>Suppression d'un lot:</dt>
                                    <dd>Tous les sacs contenus dans le lot supprimés se verront affectés à aucun lot.<br/>La composition des sacs ne sera pas modifiée.<br/>Tous les inventaires du lots seront supprimés.</dd>

                                    <dt>Suppression d'un inventaire:</dt>
                                    <dd>L'inventaire et ses détails seront supprimés.<br/>La date de dernier inventaire du lot ne sera pas changée.</dd>

                                    <dt>Suppression d'un sac:</dt>
                                    <dd>Tous les emplacements contenus dans le sac se verront sans sac.<br/>La composition des emplacements et matériels ne sera pas changée.<br/>Le matériel contenu dans les emplacements ne sera plus rattaché au lot.</dd>

                                    <dt>Suppression d'un emplacement:</dt>
                                    <dd>Tous les éléments de matériels liés à l'emplacement se verront sans emplacements.<br/>Si l'emplacement était lié à un sac et à un lot, le matériel en question ne sera plus lié au lot non-plus.</dd>

                                    <dt>Suppression d'un matériel:</dt>
                                    <dd>Le matériel sera supprimé.<br/>Aucun impact sur les inventaires passés.<br/>Le matériel ne fera plus partie de la liste du matériel lors des prochains inventaires.</dd>

                                    <dt>Suppression d'un élément dans le catalogue:</dt>
                                    <dd>Tous les items matériels liés à cet élément du catalogue seront supprimés.<br/>Toutes les apparissions de ce matériel dans les inventaires seront supprimées.<br/>Toutes les apparissions de ce matériel dans les référentiels seront supprimées.</dd>

                                    <dt>Suppression d'un référentiel:</dt>
                                    <dd>Les lots rattachés au référentiel supprimé ne seront plus rattachés à aucun référentiel et sortiront donc de la boucle de vérification de conformité.</dd>

                                    <dt>Suppression d'une catégorie de matériel:</dt>
                                    <dd>Tous les éléments matériels liés à la catégorie supprimée se veront sans catégorie.</dd>

                                    <dt>Suppression d'un état de lots:</dt>
                                    <dd>Tous les lots liés à l'état supprimés se veront sans état.</dd>

                                    <dt>Suppression d'un lieu de stockage:</dt>
                                    <dd>Tous les lots liés au lieu de stockage supprimé se veront sans lieu de stockage.</dd>

                                    <dt>Suppression d'un fournisseur:</dt>
                                    <dd>Tous les sacs et éléments de matériel qui étaient liés au fournisseur supprimé se veront sans fournisseur.</dd>

                                    <dt>Suppression d'un utilisateur:</dt>
                                    <dd>Tous les lots dont l'utilisateur était responsable vont se retrouver sans responsables.<br/>Tous les messages généraux postés par l'utilisateur n'auront plus de rédacteur.<br/>Tous les messages échangés avec l'utilisateur dans le chat seront supprimés.</dd>

                                    <dt>Suppression d'un profil:</dt>
                                    <dd>Les utilisateurs liés au profil se trouveront sans profil et ne pourrons plus se connecter.</dd>

                                    <dt>Suppression d'un message général:</dt>
                                    <dd>Le message disparaitera pour tous les utilisateurs.</dd>
                                </dl>
                                <dd>CONSEIL: Paramétrez convenablement les profils pour que les droits de modification et de suppressions ne soient pas donnés à tous. En régime de croisière, il est même recommandé de ôter le droit de suppression à tout le monde et de ne le ré-activer que sur demande ponctuelle.</dd>
                                <dd>REMARQUE: Toute suppression laisse une trace dans les logs.</dd>
                                <dd>REMARQUE: Il est impossible de supprimer une entrée dans les logs.</dd>
                                <dd>REMARQUE: Les logs passés ne seront pas impactés par les suppressions.</dd>
								<dd>REMARQUE: Les logs de plus de 90 jours sont automatiquement supprimés par une tache journalière.</dd>
                            </div>
                        </dl>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <!-- ============================================================= -->

            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Erreurs de saisies</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl>
                            <dd>La saisie des données dans Apollon est facilité par une panoplie de formulaires afin de vous guider dans les renseignements demandés. Si certaines données sont mal saisies et qu'elles sont indispensables, un message d'erreur apparaitra lorsque vous validerez le formulaire. Si les données manquantes ou erronnées ne sont pas indispensables, Apollon ne vous alertera pas et ne stockera pas les données qui n'ont aucun sens. Si des données critiques manquent (par exemple vous avez coché la case péremption mais n'avez pas saisi de date de péremption), Apollon inserera une donnée alertante à la place (pour notre exemple de date de péremption manquante, la date par défaut sera en l'an 0).</dd>
                        </dl>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>


            <!-- ============================================================= -->

            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Légende des icones</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt><a class="btn btn-xs btn-success"><i class="fa fa-print"></i></a></dt>
                            <dd>Permet de lancer l'impression de la fiche détaillée d'un élément</dd>

                            <dt><a class="btn btn-xs btn-info"><i class="fa fa-folder-open"></i></a></dt>
                            <dd>Permet d'ouvrir la fiche détaillée d'un élément</dd>

                            <dt><a class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a></dt>
                            <dd>Permet de modifier un élément</dd>

                            <dt><a class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a></dt>
                            <dd>Permet de supprimer un élément</dd>

                            <dt><a class="btn btn-xs btn-info"><i class="fa fa-lock"></i></a></dt>
                            <dd>Réinitialiser un mot de passe</dd>
                        </dl>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>


            <!-- ============================================================= -->
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Gestion des doits d'accès - Mots de passe</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl>
                            <dd>Chaque utilisateur est rattaché à un profil. Les différents droits d'accès aux fonctionnalités d'Apollon sont gérés dans les profils et s'appliquent donc aux utilisteurs rattachés au profil. Les droits sont personnalisables et permettent de restreindre l'accès à la quasi-totalité de la plateforme. L'aspect visuel d'Apollon s'adapte aux droits de chaque utilisateurs: si un utilisateur voit un bouton et peu cliquer dessus, c'est qu'il est autorisé à utiliser cette fonction. S'il n'a pas le droit de l'utiliser, le bouton n'apparaitera pas. S'il essaie toute fois de forcer l'accès à une page qu'il n'a pas l'autorisation de consulter, une erreur aparaitra.</dd>
                            <dd>Tout utilisateur a le droit de changer son propre mot de passe. Ceci n'est pas un droit supprimable.</dd>
                            <br/>
                            <dd>Lors de la création d'un utilisateur, le mot de passe associé à son compte est identique à son identifiant. Lors de sa première connexion l'utilisateur est automatiquement invité à changer son mot de passe. Tant que le mot de passe n'est pa changé, l'utilisateur n'a pas accès à la plateforme.</dd>
                            <br/>
                            <dd>Lors de la ré-initialisation d'un mot de passe, c'est également l'idenfiant de l'utilisateur qui est remis en place en tant que mot de passe. L'utilisateur sera à nouveau invité à changer son mot de passe dès sa prochaine connexion.</dd>
                            <br/>
                            <dd>Aucun mot de passe n'est stocké en clair dans la base de données, tout est chiffré. Il est donc inutile d'essayer de forcer les accès ou d'accéder aux mots de passe.</dd>
                            <dd>Apollon est équipé d'un système d'enregistrement des actions menées sur la plateforme. Toute action de tout utilisateur est reporté dans une base. L'accès à ces logs peut être autorisé/interdit via les profils. La suppression d'une entrée dans les logs n'est pas possible depuis l'interface web d'Apollon, personne ne peut se voir attribué ce droit.</dd>
                            <br/>
                            <dd>Apollon est équipé d'un système de surveillance des adresses IP. Ceci permet d'interdire la connexion à des adresses IP donc le comportement semble frauduleux. Une adresse IP se verra bloquée automatiquement après 3 tentatives de connexion infructueuse. Aucun déblocage automatique n'est possible, seul une personne ayant le profil adéquat peut débloquer une adresse IP bloquée. Dès qu'une adresse IP est bloquée, toute les connexion déjà établies en provenance de la même adresse IP sont révoquées.</dd>
                        </dl>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <!-- ============================================================= -->

            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Conditions régissant les analyses/colorations du site</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl>
                            <dt>Conformité des lots:</dt>
                            <dd>L'outil de verification de conformité des lots analyse chaque lot qui est associé à un référentiel. Si un lot n'est pas associé à un référentiel, aucune alerte n'apparait et le lot n'est pas analysé. Ceci permet de désactiver cette analyse si besoin.
                                <br>
                                Pour analyser un lot, Apollon prend le référentiel auquel le lot est lié et le déroule. Pour chaque élément du référentiel, le processus vérifie que la quantité demandée dans le référentiel est bien présente dans le lot. De même, si l'item est marqué comme étant stérile dans sa description, Apollon vérifie également que sa péremption n'est pas passée. Important: seuls les élément marqués comme OBLIGATOIRES dans les référentiels sont analysés.
                                <br/>
                                Si un lot est équipé avec un matériel périmé, mais que ce matériel ne figure pas dans le référentiel, Apollon ne signale pas pour autant que le lot n'est pas conforme: en effet, l'item n'étant pas requis dans le référentiel, il ne représente aucun interet dans la conformité du lot.
                                <br/>
                                La coloration des labels "Lot conforme" et "Lot non-conforme" est binaire: soit le lot est conforme, et le label vert "Lot conforme" apparait. Soit le lot ne respecte pas le référentiel et le label rouge "Lot non-conforme" apparait.
                                <br/>
                                L'analyse de conformité est faite en temps réel. Il suffit d'actualiser les pages pour que les analyses se fassent.</dd>
                            <br/><br/>
                            <dt>Alertes de quantité de matériel:</dt>
                            <dd>Les quantités spécifiées dans les descriptifs des matériels font l'objet d'une analyse par la plateforme Apollon. Si aucune quantité n'est renseignée, les alertes de quantité se déclenchent et le label orange "0" apparait dans les colonnes de quantité.
                                <br/>
                                Si les quantités sont suppérieures aux seuils d'alerte, alors les quantités apparaissent dans un label vert et aucune alerte n'est déclcenchée.
                                <br/>
                                Si les quantités sont égales aux seuils d'alerte, alors les quantités apparaissent dans un label orange et les alertes sont déclenchées.
                                <br/>
                                Si les quantités passent en-dessous des seuils d'alerte, alors les quantités apparaissent dans un label rouge et les alertes sont déclenchées.</dd>
                            <br/><br/>
                            <dt>Alertes sur les péremptions:</dt>
                            <dd>Les dates de péremtion sont analysées par Apollon. Si aucune date n'est renseignée mais que la case "Aucune date de péremption" n'est pas cochée, Apollon considère qu'il y a eu un oubli de saisi et matériel passe en alerte.
                                <br/>
                                Si aucune anticipation d'alerte n'est renseignée sur la fiche du matériel, alors l'alerte est binaire: soit la date de péremption est ultiérieure et aucune alerte n'est déclenchée, soit la date est inférieure ou égale à la date du jour et l'alerte se déclenche. (Colorations vertes ou rouges en fonction)
                                <br/>
                                Si une anticiaption d'alerte est renseignée, le matériel passe en alerte avant sa date de péremption, conformément à l'anticiaption demandée. Il figurera donc en Orange dans la liste du matériel ainsi que dans les emails de notification, mais ne fera pas passer un lot en Non-Conforme, et ne fera pas non-plus basculer l'indicateur général de la page d'accueil.</dd>
                        </dl>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>


            <!-- ============================================================= -->
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Notifications par email</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl>
                            <dd>Un système de mail permet de notifier les utilisateurs des alertes sur Apollon. Dans les emails, les 3 alertes suivantes aparaissent: alerte de quantité de matériel insuffisante, alerte de péremption, alerte de non-conformité des lots.
                                <br/>
                                Si aucune alerte n'est présente, la notification est quand même envoyée. Ceci permet de s'assurer que le système fonctionne.
                                <br/>
                                Pour qu'un utilisateur puisse recevoir les notifications, il faut qu'il soit rattaché à un profil pour lequel les notifications soient activées. Il faut également que son adresse mail soit correctement renseignée.
                                <br/>
                                Les emails sont envoyés de l'adresse contact@guajioty.fr qui porte le nom APOLLON. Il est possible que les emails arrivent dans les courriers indésirables, marqués comme SPAM.
                            </dd>
                        </dl>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <!-- ============================================================= -->

            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Communications</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl>
                            <dt>Fonctionnalité de messages généraux</dt>
                            <dd>Apollon propose une fonctionnalité de messages généraux. L'objectif est de permettre (aux utilisateurs qui sont autorisés à rédiger les messages) d'afficher sur la page d'accueil de tous les autres utilisteurs un message général. Attention, tous les utilisateurs sans exception peuvent voir les messages généraux laissés. La rédaction et la suppression de ces messages sont soumis à autorisation (cf profils). Les messages généraux sont gardés sans limite de temps, jusqu'à ce que quelqu'un les supprime manuellement. Attention, tout utilisateur qui a un droit de suppression des messages généraux peut potentiellement supprimer tous les messages, et pas uniquement ceux qu'il a rédigé lui-même.</dd>
                            <br/><br/>
                            <dt>Chat</dt>
                            <dd>Il s'agit ici d'une fonctionnalité de messages privé. Une fois de plus, cette fonctionalité est soumise à un droit d'accès. Les messages ne sont accessibles que par l'emetteur et le recepteur du message et ne peuvent pas être supprimés manuellement. Apollon supprime automatiquement les messages de plus de 10 jours. Il s'agit d'une fonctionnalité de dialogue ponctuel. Il s'agit de la seule fonctionnalité dont l'utilisation ne laisse aucune trace dans l'historique de l'application.</dd>
                        </dl>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>



            <div class="row"></div>
        </section>
    </div><!-- /.content -->
</div><!-- /.content-wrapper -->
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
