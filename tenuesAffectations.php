<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 1102;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['tenues_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Affectations des tenues
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Affectation des tenues</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <?php if ($_SESSION['tenues_ajout']==1) {?>
	                <div class="box-header">
						<h3 class="box-title"><a href="tenuesAffectationsForm.php" class="btn btn-sm btn-success modal-form">Nouvelle affectation</a></h3>
	                </div>
                <?php }?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Nom Prénom</th>
                                <th class="not-mobile">Affectation</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            //Ceux qui sont dans la DB en tant que user
                            $query = $db->query('SELECT DISTINCT idPersonne FROM TENUES_AFFECTATION WHERE idPersonne IS NOT NULL;');
                            while ($data = $query->fetch())
                            {
                               
                            ?>
                                <tr <?php if ($_SESSION['tenues_lecture']==1) {?>data-href="tenuesAffectationsContenu.php?case=int&id=<?=$data['idPersonne']?>"<?php }?>>
                                    <?php
                                        $query2 = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne  = :idPersonne;');
                                        $query2->execute(array('idPersonne'=>$data['idPersonne']));
                                        $data2 = $query2->fetch();
                                    ?>
                                    <td><?php echo $data2['idPersonne']; ?></td>
                                    <td><?php echo $data2['nomPersonne'].' '.$data2['prenomPersonne']; ?></td>
                                    <td>
                                       <?php
                                         $query2 = $db->prepare('SELECT ta.*, c.libelleCatalogueTenue, c.tailleCatalogueTenue FROM TENUES_AFFECTATION ta LEFT OUTER JOIN TENUES_CATALOGUE c ON ta.idCatalogueTenue = c.idCatalogueTenue WHERE ta.idPersonne  = :idPersonne ORDER BY libelleCatalogueTenue;');
                                        $query2->execute(array('idPersonne'=>$data['idPersonne']));
                                        while ($data2 = $query2->fetch())
                                        {
                                            echo $data2['libelleCatalogueTenue']; if(isset($data2['tailleCatalogueTenue']) AND $data2['tailleCatalogueTenue'] != Null){ echo ' ('.$data2['tailleCatalogueTenue'].')'; }
                                            echo '<br/>';
                                        }?>
                                    </td>
                                    
                                    <td>
                                        <?php if ($_SESSION['tenues_lecture']==1) {?>
                                            <a href="tenuesAffectationsContenu.php?case=int&id=<?=$data['idPersonne']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                        <?php }?>
                                    </td>
                                </tr>
                                <?php
                                $query2->closeCursor();
                            }
                            $query->closeCursor(); ?>

                            <?php
                            //Ceux qui ne sont pas dans la DB en tant que user
                            $query = $db->query('SELECT DISTINCT personneNonGPM FROM TENUES_AFFECTATION WHERE idPersonne IS NULL;');
                            while ($data = $query->fetch())
                            {
                               
                            ?>
                                <tr <?php if ($_SESSION['tenues_lecture']==1) {?>data-href="tenuesAffectationsContenu.php?case=ext&personneNonGPM=<?=$data['personneNonGPM']?>"<?php }?>>
                                    <td>Ext</td>
                                    <td><?php echo $data['personneNonGPM']; ?></td>
                                    <td>
                                       <?php
                                         $query2 = $db->prepare('SELECT ta.*, c.libelleCatalogueTenue, c.tailleCatalogueTenue FROM TENUES_AFFECTATION ta LEFT OUTER JOIN TENUES_CATALOGUE c ON ta.idCatalogueTenue = c.idCatalogueTenue WHERE ta.personneNonGPM  = :personneNonGPM ORDER BY libelleCatalogueTenue;');
                                        $query2->execute(array('personneNonGPM'=>$data['personneNonGPM']));
                                        while ($data2 = $query2->fetch())
                                        {
                                            echo $data2['libelleCatalogueTenue']; if(isset($data2['tailleCatalogueTenue']) AND $data2['tailleCatalogueTenue'] != Null){ echo ' ('.$data2['tailleCatalogueTenue'].')'; }
                                            echo '<br/>';
                                        }?>
                                    </td>
                                    
                                    <td>
                                        <?php if ($_SESSION['tenues_lecture']==1) {?>
                                            <a href="tenuesAffectationsContenu.php?case=ext&personneNonGPM=<?=$data['personneNonGPM']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                        <?php }?>
                                    </td>
                                </tr>
                                <?php
                                $query2->closeCursor();
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



