<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 000;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['messages_ajout']==0 AND $_SESSION['messages_suppression']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
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
                Messages généraux
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Messages</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <?php
                if ($_SESSION['messages_ajout']==1) {
                    ?>
                    <div class="box box-success">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <form role="form" action="messagesAdd.php" method="POST">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Titre:</label>
                                    <input type="text" class="form-control" placeholder="Titre du message"
                                           name="titreMessage" required>
                                </div>
                                <!-- textarea -->
                                <div class="form-group">
                                    <label>Message:</label>
                                    <textarea class="form-control" rows="3" placeholder="Corps du message"
                                              name="corpsMessage"></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info pull-right">Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php
                }
            ?>

            <div class="box box-warning">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Rédacteur</th>
                            <th>Titre</th>
                            <th>Message</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM MESSAGES m LEFT OUTER JOIN PERSONNE_REFERENTE p ON m.idPersonne = p.idPersonne;');
                        while ($data = $query->fetch())
                        {
                            ?>
                            <tr>
                                <td><?php echo $data['idMessage']; ?></td>
                                <td><?php echo $data['identifiant']; ?></td>
                                <td><?php echo $data['titreMessage']; ?></td>
                                <td><?php echo $data['corpsMessage']; ?></td>
                                <td>
                                    <?php if ($_SESSION['messages_suppression']==1) {?>
                                        <a href="messagesDelete.php?id=<?=$data['idMessage']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-trash"></i></a>
                                    <?php }?>
                                </td>
                            </tr>
                            <?php
                        }
                        $query->closeCursor(); ?>
                        </tbody>

                    </table>
                </div>
            </div>

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



