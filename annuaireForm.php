<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/version.php'); ?>
<?php
session_start();
$_SESSION['page'] = 401;
require_once('logCheck.php');
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Gestion des utilisateurs
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="annuaire.php">Annuaire</a></li>
                <li class="active">Ajouter/Modifier un utilisateur</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php
            if ($_GET['id'] == 0) {
                ?>
                <!-- general form elements disabled -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Ajout d'un utilisateur</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" action="annuaireAdd.php" method="POST">
                            <!-- text input -->
                            <div class="form-group">
                                <label>Identifiant de connexion:</label>
                                <input type="text" class="form-control" placeholder="prenom.nom"
                                       name="identifiant" required>
                            </div>
                            <div class="form-group">
                                <label>Nom:</label>
                                <input type="text" class="form-control" placeholder="Nom de famille"
                                       name="nomPersonne">
                            </div>
                            <div class="form-group">
                                <label>Prénom:</label>
                                <input type="text" class="form-control" placeholder="Prénom"
                                       name="prenomPersonne">
                            </div>
                            <div class="form-group">
                                <label>Adresse mail:</label>
                                <input type="text" class="form-control" placeholder="xx.xx@xx.xx"
                                       name="mailPersonne">
                            </div>
                            <div class="form-group">
                                <label>Téléphone</label>
                                <input type="text" class="form-control" placeholder="Téléhpone fixe ou mobile"
                                       name="telPersonne">
                            </div>
                            <div class="form-group">
                                <label>Fonction:</label>
                                <input type="text" class="form-control" placeholder="Fonction au sein de l'organisme"
                                       name="fonction">
                            </div>
                            <div class="form-group">
                                <label>Profil d'habilitation: </label>
                                <select class="form-control" name="libelleProfil">
                                    <option></option>
                                    <?php
                                    $query = $db->query('SELECT * FROM PROFILS;');
                                    while ($data = $query->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data['idProfil']; ?>"><?php echo $data['libelleProfil']; ?></option>
                                        <?php
                                    }
                                    $query->closeCursor(); ?>
                                </select>
                            </div>
                            <div class="box-footer">
                                <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                                <button type="submit" class="btn btn-info pull-right">Ajouter</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-body -->

                </div>
                <center><i>Lors de la création d'un utlisateur, le mot de passe par défaut qui est instauré est identique à son identifiant. Donc par exemple, si l'utilisateur créé a pour identifiant bob.lebricoleur , alors son mot de passe sera bob.lebricoleur</i></center>
                <center><i>Lors de sa première connexion, l'utilisateur sera invité à modifier son mot de passe.</i></center>
                <?php
            }
            else {
                ?>

                <!-- general form elements disabled -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Modification d'un utilisateur</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" action="annuaireUpdate.php?id=<?=$_GET['id']?>" method="POST">
                            <?php
                            $query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE u LEFT OUTER JOIN PROFILS p ON u.idProfil = p.idProfil WHERE idPersonne=:idPersonne;');
                            $query->execute(array('idPersonne' => $_GET['id']));
                            $data = $query->fetch();
                            ?>

                            <div class="form-group">
                                <label>Identifiant de connexion:</label>
                                <input type="text" class="form-control" value="<?=$data['identifiant']?>"
                                       name="identifiant" required>
                            </div>
                            <div class="form-group">
                                <label>Nom:</label>
                                <input type="text" class="form-control" value="<?=$data['nomPersonne']?>"
                                       name="nomPersonne">
                            </div>
                            <div class="form-group">
                                <label>Prénom:</label>
                                <input type="text" class="form-control" value="<?=$data['prenomPersonne']?>"
                                       name="prenomPersonne">
                            </div>
                            <div class="form-group">
                                <label>Adresse mail:</label>
                                <input type="text" class="form-control" value="<?=$data['mailPersonne']?>"
                                       name="mailPersonne">
                            </div>
                            <div class="form-group">
                                <label>Téléphone</label>
                                <input type="text" class="form-control" value="<?=$data['telPersonne']?>"
                                       name="telPersonne">
                            </div>
                            <div class="form-group">
                                <label>Fonction:</label>
                                <input type="text" class="form-control" value="<?=$data['fonction']?>"
                                       name="fonction">
                            </div>
                            <div class="form-group">
                                <label>Profil d'habilitation: </label>
                                <select class="form-control" name="libelleProfil">
                                    <option></option>
                                    <?php
                                    $query2 = $db->query('SELECT * FROM PROFILS;');
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data2['idProfil']; ?>" <?php if ($data2['idProfil'] == $data['idProfil']) { echo 'selected'; } ?> ><?php echo $data2['libelleProfil']; ?></option>
                                        <?php
                                    }
                                    $query->closeCursor();
                                    $query2->closeCursor();?>
                                </select>
                            </div>

                            <div class="box-footer">
                                <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                                <button type="submit" class="btn btn-info pull-right">Modifier</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-body -->

                </div>

                <?php
            }
            ?>

        </section>
        <!-- /.content -->
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
