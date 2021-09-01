<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 405;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['todolist_lecture']==0)
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
                Gestion des ToDoList
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">ToDoList</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
			<div class="row">
	            <div class="col-sm-6">
	                <?php
	                    $query = $db->query('SELECT DISTINCT pe.* FROM PERSONNE_REFERENTE pe LEFT OUTER JOIN PROFILS_PERSONNES po ON pe.idPersonne = po.idPersonne WHERE idProfil IS NOT NULL;');
	                    while($data = $query->fetch())
	                    { ?>
	                        <div class="col-md-12">
	                            <div class="box box-success">
	                                <div class="box-header">
	                                    <i class="ion ion-clipboard"></i>
	                                    <h3 class="box-title"><?php echo $data['nomPersonne'] . ' ' . $data['prenomPersonne'] ; ?></h3>
	                                    <div class="box-tools pull-right">
	                                        <?php if ($_SESSION['todolist_modification']==1) {?><a href="todolistForm.php?idCreateur=<?= $_SESSION['idPersonne'] ?>&idExecutant=<?= $data['idPersonne'] ?>" class="btn btn-sm modal-form" title="Ajouter un tache"><i class="fa fa-plus"></i></a><?php } ?>
	                                    </div>
	                                </div>
	                                <!-- /.box-header -->
	                                <div class="box-body">
	                                    <ul class="todo-list">
	                                        <?php
	                                            $query2 = $db->prepare('SELECT * FROM TODOLIST_PERSONNES tp JOIN TODOLIST tdl ON tp.idTache = tdl.idTache WHERE idExecutant = :idExecutant AND realisee = 0');
	                                            $query2->execute(array('idExecutant'=>$data['idPersonne']));
	                                            while ($data2 = $query2->fetch())
	                                            { ?>
	                                                <li>
	                                                    <!-- todo text -->
	                                                    <span class="text"><?= $data2['titre'] ?></span>
	                                                    <!-- Emphasis label -->
	                                                    <small class="label label-info"><?= $data2['priorite'] ?></small><small class="label label-success"><?= $data2['dateExecution'] ?></small>
	                                                    <!-- General tools such as edit or delete-->
	                                                    <div class="tools">
	                                                        <a href="todolistForm.php?id=<?= $data2['idTache'] ?>" class="modal-form"><i class="fa fa-edit"></i></a>
	                                                    </div>
	                                                </li>
	                                            <?php }
	                                        ?>
	                                    </ul>
	                                </div>
	                                <!-- /.box-body -->
	                            </div>
	                        </div>
	                    <?php }
	                ?>
	            </div>
	            <div class="col-sm-6">
	                <div class="col-md-12">
	                    <div class="box box-warning">
	                        <div class="box-header with-border">
	                            <i class="fa fa-check-square-o"></i>
	                            <h3 class="box-title">Taches non-affectées</h3>
	                        </div>
	        
	                        <!-- /.box-header -->
	                        <div class="box-body">
	                            <table class="table table-bordered">
	                                <tr>
	                                    <th>Titre</th>
	                                    <th>Priorité</th>
	                                    <th></th>
	                                </tr>
	                                <?php
	                                $query = $db->query('SELECT tdl.*, tp.idExecutant FROM TODOLIST tdl LEFT OUTER JOIN TODOLIST_PERSONNES tp ON tdl.idTache = tp.idTache WHERE idExecutant IS NULL;');
	                                while ($data = $query->fetch())
	                                {
	                                    ?>
	                                    <tr>
	                                        <td><?php echo $data['titre']; ?></td>
	                                        <td><?php echo $data['priorite']; ?></td>
	                                        <td>
	                                            <?php if ($_SESSION['todolist_modification']==1) {?>
	                                                <a href="todolistForm.php?id=<?= $data['idTache'] ?>" class="btn btn-xs btn-info modal-form" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
	                                            <?php }?>
	                                        </td>
	                                    </tr>
	                                    <?php
	                                }
	                                $query->closeCursor(); ?>
	                            </table>
	                        </div>
	                    </div>
	                </div>
	                <div class="col-md-12">
	                    <div class="box box-warning">
	                        <div class="box-header with-border">
	                            <i class="fa fa-check-square-o"></i>
	                            <h3 class="box-title">Taches terminées</h3>
	                        </div>
	        
	                        <!-- /.box-header -->
	                        <div class="box-body">
	                            <table class="table table-bordered">
	                                <tr>
	                                    <th>Titre</th>
	                                    <th>Priorité</th>
	                                    <th></th>
	                                </tr>
	                                <?php
	                                $query = $db->query('SELECT * FROM TODOLIST WHERE realisee = 1;');
	                                while ($data = $query->fetch())
	                                {
	                                    ?>
	                                    <tr>
	                                        <td><?php echo $data['titre']; ?></td>
	                                        <td><?php echo $data['priorite']; ?></td>
	                                        <td>
	                                            <?php if ($_SESSION['todolist_modification']==1) {?>
	                                            <a href="todolistSetKo.php?id=<?= $data['idTache'] ?>" class="btn btn-xs btn-success modal-form" title="Recycler la tache"><i class="fa fa-refresh"></i></a>
	                                                <a href="todolistForm.php?id=<?= $data['idTache'] ?>" class="btn btn-xs btn-info modal-form" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
	                                            <?php }?>
	                                        </td>
	                                    </tr>
	                                    <?php
	                                }
	                                $query->closeCursor(); ?>
	                            </table>
	                        </div>
	                    </div>
	                </div>
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
