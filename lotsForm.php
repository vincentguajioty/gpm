<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/version.php'); ?>
<?php
session_start();
$_SESSION['page'] = 101;
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
                Gestion des lots
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="lots.php">Lots</a></li>
                <li class="active">Ajouter/Modifier un lot</li>
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
                        <h3 class="box-title">Ajout d'un lot</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" action="lotsAdd.php" method="POST">
                            <!-- text input -->
                            <div class="form-group">
                                <label>Libellé:</label>
                                <input type="text" class="form-control" placeholder="Entrez un nom pour ce lot"
                                       name="libelleLot" required>
                            </div>
                            <div class="form-group">
                                <label>Référentiel à respecter: </label>
                                <select class="form-control" name="libelleTypeLot">
                                    <option></option>
                                    <?php
                                    $query = $db->query('SELECT * FROM LOTS_TYPES ORDER BY libelleTypeLot;');
                                    while ($data = $query->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data['idTypeLot']; ?>"><?php echo $data['libelleTypeLot']; ?></option>
                                        <?php
                                    }
                                    $query->closeCursor(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Etat: </label>
                                <select class="form-control" name="libelleEtat">
                                    <option></option>
                                    <?php
                                    $query = $db->query('SELECT * FROM ETATS ORDER BY libelleEtat;');
                                    while ($data = $query->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data['idEtat']; ?>"><?php echo $data['libelleEtat']; ?></option>
                                        <?php
                                    }
                                    $query->closeCursor(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Lieu de stockage:</label>
                                <select class="form-control" name="libelleLieu">
                                    <option></option>
                                    <?php
                                    $query = $db->query('SELECT * FROM LIEUX ORDER BY libelleLieu;');
                                    while ($data = $query->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data['idLieu']; ?>"><?php echo $data['libelleLieu']; ?></option>
                                        <?php
                                    }
                                    $query->closeCursor(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Personne référente</label>
                                <select class="form-control" name="identifiant">
                                    <option></option>
                                    <?php
                                    $query = $db->query('SELECT * FROM PERSONNE_REFERENTE ORDER BY identifiant;');
                                    while ($data = $query->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data['idPersonne']; ?>"><?php echo $data['identifiant']; ?></option>
                                        <?php
                                    }
                                    $query->closeCursor(); ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Date du dernier inventaire:</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input class="input-datepicker form-control" name="dateDernierInventaire">
                                </div>
                                <!-- /.input group -->
                            </div>
                            <div class="form-group">
                                <label>Fréquence inventaire (jours):</label>
                                <input type="number" class="form-control" placeholder="Entrez un nouble de jours"
                                       name="frequenceInventaire">
                            </div>

                            <!-- textarea -->
                            <div class="form-group">
                                <label>Remarques</label>
                                <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails"
                                          name="commentairesLots"></textarea>
                            </div>
                            <div class="box-footer">
                                <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                                <button type="submit" class="btn btn-info pull-right">Ajouter</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-body -->

                </div>

                <?php
            }
            else {
                ?>

                <!-- general form elements disabled -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Modification d'un lot</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" action="lotsUpdate.php?id=<?=$_GET['id']?>" method="POST">
                            <?php
                            $query = $db->prepare('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot LEFT OUTER JOIN ETATS s on l.idEtat = s.idEtat LEFT OUTER JOIN LIEUX e ON l.idLieu = e.idLieu LEFT OUTER JOIN PERSONNE_REFERENTE p on l.idPersonne = p.idPersonne WHERE idLot =:idLot;');
                            $query->execute(array('idLot' => $_GET['id']));
                            $data = $query->fetch();
                            ?>
                            <form role="form" action="lotsAdd.php" method="POST">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Libellé:</label>
                                    <input type="text" class="form-control" value="<?=$data['libelleLot']?>"
                                           name="libelleLot" required>
                                </div>
                                <div class="form-group">
                                    <label>Référentiel à respecter: </label>
                                    <select class="form-control" name="libelleTypeLot">
                                        <option></option>
                                        <?php
                                        $query2 = $db->query('SELECT * FROM LOTS_TYPES ORDER BY libelleTypeLot;');
                                        while ($data2 = $query2->fetch())
                                        {
                                            ?>
                                            <option value ="<?php echo $data2['idTypeLot']; ?>" <?php if ($data2['idTypeLot'] == $data['idTypeLot']) { echo 'selected'; } ?> ><?php echo $data2['libelleTypeLot']; ?></option>
                                            <?php
                                        }
                                        $query2->closeCursor(); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Etat: </label>
                                    <select class="form-control" name="libelleEtat">
                                        <option></option>
                                        <?php
                                        $query2 = $db->query('SELECT * FROM ETATS ORDER BY libelleEtat;');
                                        while ($data2 = $query2->fetch())
                                        {
                                            ?>
                                            <option value ="<?php echo $data2['idEtat']; ?>" <?php if ($data2['idEtat'] == $data['idEtat']) { echo 'selected'; } ?> ><?php echo $data2['libelleEtat']; ?></option>
                                            <?php
                                        }
                                        $query2->closeCursor(); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Lieu de stockage:</label>
                                    <select class="form-control" name="libelleLieu">
                                        <option></option>
                                        <?php
                                        $query2 = $db->query('SELECT * FROM LIEUX ORDER BY libelleLieu;');
                                        while ($data2 = $query2->fetch())
                                        {
                                            ?>
                                            <option value ="<?php echo $data2['idLieu']; ?>" <?php if ($data2['idLieu'] == $data['idLieu']) { echo 'selected'; } ?> ><?php echo $data2['libelleLieu']; ?></option>
                                            <?php
                                        }
                                        $query2->closeCursor(); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Personne référente</label>
                                    <select class="form-control" name="identifiant">
                                        <option></option>
                                        <?php
                                        $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE ORDER BY identifiant;');
                                        while ($data2 = $query2->fetch())
                                        {
                                            ?>
                                            <option value ="<?php echo $data2['idPersonne']; ?>" <?php if ($data2['idPersonne'] == $data['idPersonne']) { echo 'selected'; } ?> ><?php echo $data2['identifiant']; ?></option>
                                            <?php
                                        }
                                        $query2->closeCursor(); ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Date du dernier inventaire:</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input class="input-datepicker form-control" name="dateDernierInventaire" value="<?=$data['dateDernierInventaire']?>">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <div class="form-group">
                                    <label>Fréquence inventaire (jours):</label>
                                    <input type="number" class="form-control" value="<?=$data['frequenceInventaire']?>"
                                           name="frequenceInventaire">
                                </div>

                                <!-- textarea -->
                                <div class="form-group">
                                    <label>Remarques</label>
                                    <textarea class="form-control" rows="3"
                                              name="commentairesLots"><?php echo $data['commentairesLots']; ?></textarea>
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
