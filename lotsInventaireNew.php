<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 101;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['lots_modification']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    <?php require_once 'checkLotsConf.php'; ?>

    <?php
    $query = $db->prepare('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne WHERE idLot = :idLot;');
    $query->execute(array(
        'idLot' => $_GET['id']
    ));
    $data = $query->fetch();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Nouvel inventaire du lot: <?php echo $data['libelleLot']; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="lots.php">Lots</a></li>
                <li class="active"><?php echo $data['libelleLot']; ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <form role="form" action="lotsInventaireNewAdd.php?id=<?=$_GET['id']?>" method="POST">
                <div class="box">
                    <div class="box-body">
                        <div class="form-group">
                            <label>Personne ayant fait l'inventaire:</label>
                            <select class="form-control select2" style="width: 100%;" name="identifiant" required>
                                <?php
                                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE ORDER BY identifiant;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idPersonne']; ?>" <?php if ($data2['identifiant'] == $_SESSION['identifiant']) { echo 'selected'; } ?> ><?php echo $data2['identifiant']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group" id="dateInventaire">
                            <label>Date de l'inventaire:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="input-datepicker form-control" name="dateInventaire" value="<?php echo date('Y-m-d');?>" required>
                            </div>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input checked type="checkbox" value="1" name="boolDernier" checked> Marquer cet inventaire comme étant le plus récent
                            </label>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="box-body">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Sac</th>
                                <th>Emplacement</th>
                                <th>Libelle du matériel</th>
                                <th>Quantité</th>
                                <th>Péremption</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $query2 = $db->prepare('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement = e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE idLot = :idLot ORDER BY libelleSac, libelleEmplacement, libelleMateriel;');
                            $query2->execute(array(
                                'idLot' => $_GET['id']
                            ));
                            while ($data2 = $query2->fetch()) { ?>

                                <tr>
                                    <td><?php echo $data2['idElement']; ?></td>
                                    <td><?php echo $data2['libelleSac']; ?></td>
                                    <td><?php echo $data2['libelleEmplacement']; ?></td>
                                    <td><?php echo $data2['libelleMateriel']; ?></td>
                                    <td><input type="text" class="form-control" value="<?php echo $data2['quantite']; ?>"name="formArray[<?php echo $_GET['id']; ?>][<?php echo $data2['idSac']; ?>][<?php echo $data2['idEmplacement']; ?>][<?php echo $data2['idElement']; ?>][qtt]"></td>
                                    <td><input type="text" class="input-datepicker form-control" value="<?php echo $data2['peremption']; ?>"name="formArray[<?php echo $_GET['id']; ?>][<?php echo $data2['idSac']; ?>][<?php echo $data2['idEmplacement']; ?>][<?php echo $data2['idElement']; ?>][per]" <?php if ($data2['peremption'] != Null) echo 'required';?>></td>
                                </tr>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box">
                    <div class="box-body">
                        <div class="form-group">
                            <label>Remarques</label>
                            <textarea class="form-control" rows="3" name="commentairesInventaire"></textarea>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="box-body">
                        <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                        <button type="submit" class="btn btn-info pull-right">Ajouter</button>
                    </div>
                </div>
            </form>

            <div class="row"></div>
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