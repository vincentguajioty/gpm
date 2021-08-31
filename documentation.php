<!DOCTYPE html>
<html>
<?php
session_start();
$_SESSION['page'] = 503;
require_once('logCheck.php');
?>
<?php include('header.php'); require_once('config/config.php'); ?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Documentation GPM - Gestionnaire de Parc Matériel
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
                            <dd>GPM est un produit permettant une meilleure gestion d'un parc matériel. Développé pour les associations de secourisme ou autres organismes utilisant du matériel de secours, GPM se veut être une solution modelable afin de répondre aux attentes les plus contraignantes.
                                <br/>
                                Afin de tirer le maximum d'avantages de GPM, il est recommandé de prendre le temps de bien configurer l'outil. L'efficacité des fonctionnalités visuelles et des notifications est liée au bon paramétrage des différents éléments.</dd>
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
                            <dd>Le tronc central de GPM est le catalogue de matériel. Ce catalogue permet de saisir les différents matériels de votre parc, de manière générique. On ne va en effet pas saisir "Compresses du Lot 1", mais juste "Compresses". C'est sur la base de ce catalogue que vous pourrez créer vos lots et référentiels.</dd>
                            <dd>Tous les champs du catalogue sont importants. Par exemple, si le champ "Stérilité" est mal rempli, des alertes non-pertinentes pourraient partir par la suite.</dd>
                            <br/>
                            <dd>Une fois le catalogue renseigné, vous pouvez passer à la création des référentiels, ou à la création des lots.</dd>
                            <br/>
                            <dd>Un référentiel, c'est une liste de matériel que doit contenir tout lot qui lui est rattaché. Il ne s'agit pas forcément d'un référentiel national conforme à ceux dictés dans le Référentiel Nationnal des Dispositifs de Premiers Secours. Vous êtes libres de concocter vos propres référentiels. Il est toutefois conseiller de se baser sur le RNDPS. Les lots se verront ensuite rattachés aux référentiels et GPM vérifiera si vos lots sont conformes à leurs référentiels.</dd>
                            <br/>
                            <dd>Un lot, c'est un ensemble de sacs. Un sac c'est un ensemble d'emplacements. Et un emplacement contient un ensemble de matériels. Donc pour créer un lot, il faut commencer par déclarer le lot dans l'onglet <i>Lots</i>, puis créer les sacs qui composent ce lot en les affectant au lot créé (opération à faire dans l'onglet <i>Sacs</i>), puis créer les emplacements dans les sacs (onglet <i>Emplacements</i>). Une fois que cette structure est crée, vous n'avez plus qu'à ajouter du matériel dans les emplacements via l'onglet <i>Matériel</i>. Afin d'avoir une vue globale du matériel et de sa disposition dans un lot, allez dans l'onglet <i>Lots</i> et cliquez sur le dossier bleu afin d'ouvrir la fiche détaillée du lot sur laquelle apparaitra la structure du lot (sacs, emplacements, matériels).</dd>
                            <br/>
                            <dd>Le module de gestion des commandes permet de suivi des commandes de consommables. GPM ne passe pas tout seul les commandes auprès des fournisseurs. La vocation de ce module est de centraliser les informations quant aux commandes passées et d'instaurer un processus de commande efficace. Le processus est détaillé plus bas sur cette page.</dd>
                            <br/>
                            <dd>Le module de reserve permet de faire l'intermédiaire entre les commandes et les lots opérationnels. La réserve est répartie en conteneurs. Ces conteneurs contiennent les différents matériels. Le module de transfert de matériel permet d'intégrer dans la réserve le matériel en provenance des commandes, puis d'intégrer dans les lots opérationnels les matériels en provenance de la réserve.</dd>
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
                            <dd>La suppression de données est facile dans GPM. Il suffit de cliquer sur les petites corbeilles rouges et d'accepter le message d'avertissement. ATTENTION A CE QUE VOUS SUPPRIMEZ ! Voici un tableau non-exhaustif des conséquences des suppressions:</dd>
                            <div class="box-body">
                                <dl>
                                    <dt>Suppression d'un lot:</dt>
                                    <dd>Tous les sacs contenus dans le lot supprimé se verront affectés à aucun lot.<br/>La composition des sacs ne sera pas modifiée.<br/>Tous les inventaires du lot seront supprimés.</dd>

                                    <dt>Suppression d'un inventaire:</dt>
                                    <dd>L'inventaire et ses détails seront supprimés.<br/>La date de dernier inventaire du lot ne sera pas changée.</dd>

                                    <dt>Suppression d'un sac:</dt>
                                    <dd>Tous les emplacements contenus dans le sac se verront sans sac.<br/>La composition des emplacements et matériels ne sera pas changée.<br/>Le matériel contenu dans les emplacements ne sera plus rattaché au lot.</dd>

                                    <dt>Suppression d'un emplacement:</dt>
                                    <dd>Tous les éléments de matériels liés à l'emplacement se verront sans emplacements.<br/>Si l'emplacement était lié à un sac et à un lot, le matériel en question ne sera plus lié au lot non-plus.</dd>

                                    <dt>Suppression d'un matériel:</dt>
                                    <dd>Le matériel sera supprimé.<br/>Aucun impact sur les inventaires passés.<br/>Le matériel ne fera plus partie de la liste du matériel lors des prochains inventaires.</dd>

                                    <dt>Suppression d'un élément dans le catalogue:</dt>
                                    <dd>Tous les items matériels liés à cet élément du catalogue seront supprimés.<br/>Toutes les apparitions de ce matériel dans les inventaires seront supprimées.<br/>Toutes les apparitions de ce matériel dans les référentiels seront supprimées.<br/>Toutes les apparitions de ce matériel dans les commandes seront supprimées impactant ainsi le contenu et le montant des commandes.</dd>

                                    <dt>Suppression d'un référentiel:</dt>
                                    <dd>Les lots rattachés au référentiel supprimé ne seront plus rattachés à aucun référentiel et sortiront donc de la boucle de vérification de conformité.</dd>

                                    <dt>Suppression d'une catégorie de matériel:</dt>
                                    <dd>Tous les éléments matériels liés à la catégorie supprimée se verront sans catégorie.</dd>

                                    <dt>Suppression d'un lieu de stockage:</dt>
                                    <dd>Tous les lots liés au lieu de stockage supprimé se verront sans lieu de stockage.<br/>Toutes les commandes utilisant ce lieu comme lieu de livraison n'auront plus de lieu de livraison.</dd>

                                    <dt>Suppression d'un fournisseur:</dt>
                                    <dd>Tous les sacs et éléments de matériel qui étaient liés au fournisseur supprimé se verront sans fournisseur.<br/>Toutes les commandes utilisant ce fournisseur n'auront plus de fournisseur.</dd>

                                    <dt>Suppression d'un utilisateur:</dt>
                                    <dd>Tous les lots dont l'utilisateur était responsable vont se retrouver sans responsables.<br/>Tous les messages généraux postés par l'utilisateur n'auront plus de rédacteur.<br/>Tous les messages échangés avec l'utilisateur dans le chat seront supprimés.<br/>Toutes les apparitions de l'utilisateur dans les commandes seront supprimées (donc plus de demandeur, plus de valideur, ...).<br/>Tous les centres de coûts gérés par cet utilisateur n'auront plus de personne référente.</dd>

                                    <dt>Suppression d'un profil:</dt>
                                    <dd>Les utilisateurs liés au profil se trouveront sans profil et ne pourront plus se connecter.</dd>

                                    <dt>Suppression d'un message général:</dt>
                                    <dd>Le message disparaitra pour tous les utilisateurs.</dd>
                                </dl>
                                <dd>CONSEIL: Paramétrez convenablement les profils pour que les droits de modification et de suppressions ne soient pas donnés à tous. En régime de croisière, il est même recommandé d'ôter le droit de suppression à tout le monde et de ne le ré-activer que sur demande ponctuelle.</dd>
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
                            <dd>La saisie des données dans GPM est facilitée par une panoplie de formulaires afin de vous guider dans les renseignements demandés. Si certaines données sont mal saisies et qu'elles sont indispensables, un message d'erreur apparaitra lorsque vous validerez le formulaire. Si les données manquantes ou erronées ne sont pas indispensables, GPM ne vous alertera pas et ne stockera pas les données qui n'ont aucun sens. Si des données critiques manquent (par exemple vous avez coché la case péremption mais n'avez pas saisi de date de péremption), GPM insèrera une donnée alarmante à la place (pour notre exemple de date de péremption manquante, la date par défaut sera en l'an 0).</dd>
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
                            <dd>Chaque utilisateur est rattaché à un profil. Les différents droits d'accès aux fonctionnalités de GPM sont gérés dans les profils et s'appliquent donc aux utilisateurs rattachés au profil. Les droits sont personnalisables et permettent de restreindre l'accès à la quasi-totalité de la plateforme. L'aspect visuel de GPM s'adapte aux droits de chaque utilisateur: si un utilisateur voit un bouton et peut cliquer dessus, c'est qu'il est autorisé à utiliser cette fonction. S'il n'a pas le droit de l'utiliser, le bouton n'apparaitra pas. S'il essaie toutefois de forcer l'accès à une page qu'il n'a pas l'autorisation de consulter, une erreur apparaitra.</dd>
                            <dd>Tout utilisateur a le droit de changer son propre mot de passe.</dd>
                            <br/>
                            <dd>Lors de la création d'un utilisateur, le mot de passe associé à son compte est identique à son identifiant. Lors de sa première connexion l'utilisateur est automatiquement invité à changer son mot de passe. Tant que le mot de passe n'est pas changé, l'utilisateur n'a pas accès à la plateforme.</dd>
                            <br/>
                            <dd>Lors de la réinitialisation d'un mot de passe, c'est également l'identifiant de l'utilisateur qui est remis en place en tant que mot de passe. L'utilisateur sera à nouveau invité à changer son mot de passe dès sa prochaine connexion.</dd>
                            <br/>
                            <dd>Aucun mot de passe n'est stocké en clair dans la base de données, tout est chiffré. Il est donc inutile d'essayer de forcer les accès ou d'accéder aux mots de passe.</dd>
                            <dd>GPM est équipé d'un système d'enregistrement des actions menées sur la plateforme. Toute action de tout utilisateur est reportée dans une base. L'accès à ces logs peut être autorisé/interdit via les profils. La suppression d'une entrée dans les logs n'est pas possible depuis l'interface web de GPM, personne ne peut se voir attribuer ce droit.</dd>
                            <br/>
                            <dd>GPM est équipé d'un système de surveillance des adresses IP. Ceci permet d'interdire la connexion à des adresses IP donc le comportement semble frauduleux. Une adresse IP se verra bloquée automatiquement après 3 tentatives de connexion infructueuse. Aucun déblocage automatique n'est possible, seule une personne ayant le profil adéquat peut débloquer une adresse IP bloquée. Dès qu'une adresse IP est bloquée, toutes les connexions déjà établies en provenance de la même adresse IP sont révoquées.</dd>
                            <br/>
                            <dd>CONSEIL: N'utilisez pas de comptes génériques (un seul compte pour plusieurs personnes), mais bien des comptes nominatifs. Ceci permet de suivre beaucoup plus précisemment les actions menées par chacun, surtout en cas de conflit.</dd>
                            <dd>CONSEIL: Lors de la mise en place de nouveaux profils, testez les avant d'y rattacher des utilisateurs réels! Ceci évite les mauvaises surprises quant aux profils incohérents (par exemple droit en modification mais pas en lecture sur les lots => l'utilisateur ne pouvant pas consulter les lots, il ne pourra pas les modifier non-plus).</dd>
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
                            	<dd>L'outil de vérification de conformité des lots analyse chaque lot qui est associé à un référentiel. Si un lot n'est pas associé à un référentiel, aucune alerte n'apparait et le lot n'est pas analysé. Ceci permet de désactiver cette analyse si besoin.
                                <br>
                                Pour analyser un lot, GPM prend le référentiel auquel le lot est lié et le déroule. Pour chaque élément du référentiel, le processus vérifie que la quantité demandée dans le référentiel est bien présente dans le lot. De même, si l'item est marqué comme étant stérile dans sa description, GPM vérifie également que sa péremption n'est pas passée. Important: seuls les éléments marqués comme OBLIGATOIRES dans les référentiels sont analysés.
                                <br/>
                                Si un lot est équipé avec un matériel périmé, mais que ce matériel ne figure pas dans le référentiel, GPM ne signale pas pour autant que le lot n'est pas conforme: en effet, l'item n'étant pas requis dans le référentiel, il ne représente aucun interet dans la conformité du lot.
                                <br/>
                                La coloration des labels "Lot conforme" et "Lot non-conforme" est binaire: soit le lot est conforme, et le label vert "Lot conforme" apparait. Soit le lot ne respecte pas le référentiel et le label rouge "Lot non-conforme" apparait.
                                <br/>
                                L'analyse de conformité est faite en temps réel. Il suffit d'actualiser les pages pour que les analyses se fassent.</dd>
                            <br/><br/>
                            <dt>Alertes de quantité de matériel:</dt>
                            	<dd>Les quantités spécifiées dans les descriptifs des matériels font l'objet d'une analyse par la plateforme GPM. Si aucune quantité n'est renseignée, les alertes de quantité se déclenchent et le label orange "0" apparait dans les colonnes de quantité.
                                <br/>
                                Si les quantités sont supérieures aux seuils d'alerte, alors les quantités apparaissent dans un label vert et aucune alerte n'est déclcenchée.
                                <br/>
                                Si les quantités sont égales aux seuils d'alerte, alors les quantités apparaissent dans un label orange et les alertes sont déclenchées.
                                <br/>
                                Si les quantités passent en-dessous des seuils d'alerte, alors les quantités apparaissent dans un label rouge et les alertes sont déclenchées.</dd>
                            <br/><br/>
                            <dt>Alertes sur les péremptions:</dt>
                            	<dd>Les dates de péremption sont analysées par GPM. Si aucune date n'est renseignée mais que la case "Aucune date de péremption" n'est pas cochée, GPM considère qu'il y a eu un oubli de saisi et matériel passe en alerte.
                                <br/>
                                Si aucune anticiption d'alerte n'est renseignée sur la fiche du matériel, alors l'alerte est binaire: soit la date de péremption est ultérieure et aucune alerte n'est déclenchée, soit la date est inférieure ou égale à la date du jour et l'alerte se déclenche. (Colorations vertes ou rouges en fonction)
                                <br/>
                                Si une anticiaption d'alerte est renseignée, le matériel passe en alerte avant sa date de péremption, conformément à l'anticiaption demandée. Il figurera donc en Orange dans la liste du matériel ainsi que dans les emails de notification, mais ne fera pas passer un lot en Non-Conforme, et ne fera pas non-plus basculer l'indicateur général de la page d'accueil.</dd>
                            <br/><br/>
                            <dt>Alertes sur l'annuaire:</dt>
                            	<dd>Sur la page de l'annuaire, les identifiants peuvent apparaitre entourés en rouge. Ceci veut dire que l'utilisateur ne s'est pas connecté sur les deux mois précédents alors qu'il a un profil qui lui donne le droit de se connecter. Il s'agirait donc potentiellement d'un compte actif qui n'est plus utilisé. Il serait sage de le désactiver.</dd>
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
                            <dd>Un système de mail permet de notifier les utilisateurs des alertes sur GPM. Dans les emails, les 3 alertes suivantes apparaissent: alerte de quantité de matériel insuffisante, alerte de péremption, alerte de non-conformité des lots.
                                <br/><br/>
                                En fonction des profils, il est possible de ne recevoir aucune notification journalière, de recevoir une notification uniquement en cas d'alerte, ou de revoir systématiquement un mail permettant d'être informé quotidiennement de la présence ou pas d'alertes.
                                <br/><br/>
                                Pour qu'un utilisateur puisse recevoir les notifications, il faut qu'il soit rattaché à un profil pour lequel les notifications soient activées. Il faut également que son adresse mail soit correctement renseignée.
                                <br/><br/>
                                Les emails sont envoyés de l'adresse paramétrée par les administrateurs. Le nom donné à l'instance de GPM apparaitra dans l'objet des mails. Il est possible que les emails arrivent dans les courriers indésirables, marqués comme SPAM.
                                <br/><br/>
                                Pour qu'un lot soit analysé par le sustème d'alerte, il est OBLIGATOIRE que ce lot soit à l'état "Opérationnel". Si le lot n'a pas d'état ou est "Au rebus", il ne figurera pas dans les alertes. De même, les éléments de matériel qui figurent dans les alertes de péremption et de quantité snt uniquement des élément rattachés à un lot qui est à l'état "Opérationnel". Autrement dit, tout ce qui est Au rebus ou sans état ne fera pas l'objet d'une alerte.
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
                        <h3 class="box-title">Messages généraux</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl>
                            <dd>GPM propose une fonctionnalité de messages généraux. L'objectif est de permettre (aux utilisateurs qui sont autorisés à rédiger les messages) d'afficher sur la page d'accueil de tous les autres utilisateurs un message général. Attention, tous les utilisateurs sans exception peuvent voir les messages généraux laissés. La rédaction et la suppression de ces messages sont soumises à autorisation (cf profils). Les messages généraux sont gardés sans limite de temps, jusqu'à ce que quelqu'un les supprime manuellement. Attention, tout utilisateur qui a un droit de suppression des messages généraux peut potentiellement supprimer tous les messages, et pas uniquement ceux qu'il a rédigés lui-même.</dd>
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
                        <h3 class="box-title">Commandes de consommables</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl>                            
                            <dd>Le module de commande permet la gestion des commandes de consommables. Ne peuvent être insérés dans les commandes que les éléments déjà présents dans le catalogue. Une commande est obligatoirement reliée à un et un seul fournisseur. L'affichage d'une commande évolue au fil de son cycle de vie. Par exemple, tant qu'aucune demande de validation n'est faite, l'onglet Validation n'est pas visible.</dd>
                            <br/>
                            <dt>Les onglets</dt>
                            <dd>Les onglets "Toutes les commandes" et "Abandonnées" ne prennent pas en compte l'identité de l'utilisateur connecté sur GPM. Ils permettent d'accéder à toutes les commandes. En revanche, les onglets "Je dois valider" , "Je dois traiter" , "Je dois suivre" n'affichent que les commandes qui concernent l'utilisateur actuel.</dd>
                            <br/>
                            <dt>Processus de commande</dt>
                            <dd><ul>
	                            <li>La commande est créée par un demandeur. Elle est alors à l'état "Nouveau". C'est l'état qui permet de travailler sur la commande, y ajouter des produits, en retirer, changer les informations telles que le fournisseur, les intervenants...
	                            <li>Une fois la commande prête, cliquer sur "Soumettre à validation". Ceci verrouille la commande, plus rien ne peut être modifié. La personne désignée comme "Valideur" a la main sur l'onglet validation et peut alors accepte ou refuser la commande. Si la commande est refusée, elle est renvoyée à l'état "Nouveau" et est donc déverrouillée pour être retravaillée. Si la commande est acceptée, elle reste verrouillée et passe au stade "Validation OK".
	                            <li>La personne en charge de la commande peut donc passer la commande après du fournisseur. L'onglet "Passage de la commande" apparait et permet de renseigner des informations relatives à la commande chez le fournisseur. Une fois la commande passée, cliquer sur "Commande passée > En attente de livraison".
	                            <li>Dès que la commande est réceptionnée, renseigner l'onglet "Livraison" qui est apparu. La validation de cet onglet se fait soit par "Commande reçue > OK" ce qui valide la réception de la commande, soit par "Commande reçue > SAV" ce qui engage une procédure de SAV. Durant toute la procédure de SAV, la case "Note" permet d'ajouter des notes au dossier et de faire ainsi le suivi du SAV. Une fois le SAV terminé, le bouton "SAV terminé > Commande OK" permet de ramener la commande dans le même état que si sa réception s'était bien déroulée dès le début.
	                            <li>Une fois la commande en état "Livraison OK", il ne reste plus qu'à clôturer la commande. L'état "Livraison OK" permet de garder la commande accessible le temps de ranger le matériel reçu ou de l'affecter à un lot par exemple. Une fois la commande clôturée, elle apparaitra toujours dans l'onglet "Toutes les commandes" mais ne figurera plus dans les trois onglets personnels.
                            </ul></dd>
                            <br/>
                            <dt>Rôles des intervenants</dt>
                            <dd>Quatre personnes interviennent le long d'une commande:
                                <ul>
                                    <li>Le demandeur: il initie la commande. Il est en charge de la formuler complètement et d'y ajouter les éléments à commander (onglet Contenu)
                                    <li>Le réalisateur: la commande lui est affectée. Il fait le suivi de la commande et est en charge de la passer au moment voulu auprès du fournisseur et de traiter sa réception.
                                    <li>Le valideur: il est responsable des commandes passées ou refusées. Il a le pouvoir de refuser une commande, tout comme il a le pouvoir de la valider. Il prend la responsabilité financière et organisationnelle de la commande. Son aval est obligatoire dans le process de commande.
                                    <li>L'observateur: il est spectateur de la commande. Il n'a aucune action à mener.
                                </ul>
                                Une même personne peut assumer plusieurs rôles dans une commande. Ceci dépend de l'organisation voulue.
                                <br/>
                                Pour qu'un utilisateur de GPM apparaisse dans les commandes (en tant que demandeur, réalisateur, valideur, observateur), il faut que son profil le lui permette.
                                <br/>
                                En fonction de l'état d'avancement de la commande et des droits de l'utilisateur connecté, les formulaires et les boutons permettant de faire avancer les commandes sont accessibles ou bloqués.
                                <br/>
                                Tous les prix dans le module de commande doivent être renseignés en € TTC.
                                <br/><br/>
                            <dt>Mails de notification</dt>
                            <dd>Voici les différents cas de figure pour lesquels des notifications par mail sont envoyées:
                                <ul>
                                    <li>La commande passe au stade de demande de validation: mail envoyé au valideur et à l'observateur
                                    <li>La validation de la commande est donnée (qu'elle soit positive ou négative): mail envoyé au demandeur, au réalisateur et à l'observateur
                                    <li>La commande est clôturée: mail envoyé au demandeur, au valideur et à l'observateur
                                    <li>La commande est abandonnée: mail envoyé au demandeur, au réalisateur, au valideur et à l'observateur
                                </ul>
                                Pour pouvoir recevoir les mails de notification, les comptes utilisateurs des intéressés doivent avoir une adresse mail valide.
                                <br/>
                                Le paramètre de réception de mail des profils n'impacte pas les mails reçus pour les commandes (ce paramètre sert uniquement pour les notifications journalières).
                                <br/>
                                Pour savoir si un mail a bien été envoyé ou s'il y a eu une erreur lors de l'envoi du mail, se reporter à l'onglet AUTRES > Logs de la base
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
                        <h3 class="box-title">Module de transfert</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl>
                            <dd>Le module de transfert de matériel permet d'intégrer dans la réserve le matériel reçu d'une commande, et permets ensuite d'intégrer dans les lots opérationnels les matériels présents dans la réserve.</dd>
                            <br/>
                            <dd>Au même titre que tout autre module, l'accès à ce module de transfert de matériel est géré au niveau des profils, dans la rubrique RESERVE (un droit disponible par type de transfert).</dd>
                            <br/>
                            <dd>Lors du transfert de matériel en provenance d'une commande vers un module de réserve, un premier écran vous permet de selectionner la commande à traiter. Sur cet écran ne figurent que les commandes qui sont au stade LIVRAISON OK, ce qui exclus les commandes closes, les commandes en SAV et toutes les commandes qui n'ont pas encore atteint le stade de livraison. Une fois la commande selectionnée, le deuxième écran vous permet de selectionner le matériel à transférer parmis les éléments commandés. Le troisième écran vous permet de selectionner le conteneur de destination. Seuls les conteneurs ayant déjà une entrée pour le-dit matériel apparaissent, il s'agit bien d'un ré-approvisionnement de réserve, et non d'une création d'élément dans la réserve. Un écran de résumé vous permet de visualisation le transfert avant de le valider.</dd>
                            <br/>
                            <dd>Lors du transfert de matériel en provenance de la réserve et à destination des lots, un premier écran vous permet de selectionner le matériel à réapprovisionner (liste des éléments du catalogue). Un deuxième écran vous permet de choisir le conteneur de réserve. Seuls les conteneurs qui contiennent une quantité non-nulles de ce matériel apparaissent. Un troisième écran vous permet de selectionner le lot>sac>emplacement cible. Seuls les lots>sacs>emplacements> qui contiennent le-dit matériel apparaissent, il s'agit bien d'un ré-approvisionnement de lot, et non d'une création d'élément dans un lot. Un quatrième écran vous permet de visualiser le transfert avant de le valider.</dd>
                            <br/>
                            <dd>Comportement des dates (quelque soit la méthode de transfert): si le matériel source n'a pas de date de péremption, aucune modification n'est faite côté cible. Donc si la cible avait une date de péremption, elle la garde, sinon aucune date n'est mise en place. Si la matériel source a une date de péremption, une analyse est faite par rapport à la cible: si la cible n'a pas de date de péremption ou a une date ultérieure au matériel source, c'est la date du matériel source qui est mise en place sur la cible. Si la source a une date ultérieure que la cible, la cible n'est pas changée. En résumé, on garde toujours le pire des cas: la date de péremption la plus proche.</dd>
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
                        <h3 class="box-title">Equipements de transmission</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl>
                            <dd>Ce module permet la gestion d'un parc de matériel radio ainsi que la gestion de la programmation des équipements.</dd>
                            <br/>
                            <dd>Au contraire de tous les autres modules, celui-ci ne s'articule pas avec le CATALOGUE. Ce module de transmission est complètement indépendant.</dd>
                            <br/>
                            <dd>Une première fonctionnalité permet d'enregisrer des canaux (un canal étant défini par plusieurs fréquences, CTCSS ...). Une fois les canaux saisis, il est possible de créer un plan de fréquence, donc d'associer les canaux programmés à un numéro de canal. Pour terminer, on associe à chaque équipement radio le plan de fréquence souhaité.</dd>
                            <br/>
                            <dd>Il est possible d'enregistrer des pièces jointes aux canaux, aux plans et aux équipements radio. Ceci permet entre autres de stocker les documentations sur les canaux et les exports de plans de fréquences à des fins de sauvegarde.</dd>
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
