<!DOCTYPE html>
<html>
<?php include('header.php'); ?>
<?php
session_start();
$_SESSION['page'] = 104;
require_once('logCheck.php');
?>
<body class="hold-transition skin-blue sidebar-mini fixed">



<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Gestion du matériel
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="materiels.php">Matériel</a></li>
                <li class="active">Ajouter/Modifier du matériel</li>
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
                        <h3 class="box-title">Ajout d'un matériel</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" action="materielsAdd.php" method="POST">
                            <!-- text input -->
                            <div class="form-group">
                                <label>Référence du catalogue:</label>
                                <select class="form-control" name="libelleMateriel">
                                    <?php
                                    $query = $db->query('SELECT * FROM MATERIEL_CATALOGUE ORDER BY libelleMateriel;');
                                    while ($data = $query->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data['idMaterielCatalogue']; ?>"><?php echo $data['libelleMateriel']; ?></option>
                                        <?php
                                    }
                                    $query->closeCursor(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Lot: </label>
                                <select class="form-control" name="libelleLot">
                                    <option></option>
                                    <?php
                                    $query = $db->query('SELECT * FROM LOTS_LOTS ORDER BY libelleLot;');
                                    while ($data = $query->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data['idLot']; ?>"><?php echo $data['libelleLot']; ?></option>
                                        <?php
                                    }
                                    $query->closeCursor(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Sac: </label>
                                <select class="form-control" name="libelleSac">
                                    <option></option>
                                    <?php
                                    $query = $db->query('SELECT * FROM MATERIEL_SAC s LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot ORDER BY libelleSac;');
                                    while ($data = $query->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data['idSac']; ?>" data-id="<?php echo $data['idLot']; ?>"><?php echo $data['libelleSac']; ?></option>
                                        <?php
                                    }
                                    $query->closeCursor(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Emplacement: </label>
                                <select class="form-control" name="libelleEmplacement">
                                    <option></option>
                                    <?php
                                    $query = $db->query('SELECT * FROM MATERIEL_EMPLACEMENT e LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot ORDER BY libelleEmplacement;');
                                    while ($data = $query->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data['idEmplacement']; ?>" data-id="<?php echo $data['idSac']; ?>"><?php echo $data['libelleEmplacement']; ?></option>
                                        <?php
                                    }
                                    $query->closeCursor(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Fournisseur:</label>
                                <select class="form-control" name="nomFournisseur">
                                    <option></option>
                                    <?php
                                    $query = $db->query('SELECT * FROM FOURNISSEURS ORDER BY nomFournisseur;');
                                    while ($data = $query->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data['idFournisseur']; ?>"><?php echo $data['nomFournisseur']; ?></option>
                                        <?php
                                    }
                                    $query->closeCursor(); ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Quantité:</label>
                                <input type="text" class="form-control" placeholder="Quantité présente"
                                       name="quantite">
                            </div>
                            <div class="form-group">
                                <label>Quantité d'Alerte:</label>
                                <input type="text" class="form-control" placeholder="Permet de déclencher une alerte une fois ce seuil atteint"
                                       name="quantiteAlerte">
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input checked type="checkbox" value="1" name="boolPeremption" onClick="montrer_cacher(this,'perem')"> Le matériel a une date de péremption
                                </label>
                            </div>

                            <div class="form-group" id="perem">
                                <label>Date de péremption:</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="input-datepicker form-control" name="peremption">
                                </div>
                                <br/>
                                <label>Jours d'anticipation de l'alerte de péremption:</label>
                                <input type="text" class="form-control" placeholder="ex: Saisissez 5 pour recevoir une alerte à J-5 de la péremption"
                                       name="delaisPeremption">
                                <!-- /.input group -->
                            </div>

                            <div class="form-group">
                                <label>Commentaires</label>
                                <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails"
                                          name="commentairesElement"></textarea>
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
                        <h3 class="box-title">Modification d'un matériel</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form role="form" action="materielsUpdate.php?id=<?=$_GET['id']?>" method="POST">
                            <?php
                            $query = $db->prepare('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement = e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN FOURNISSEURS f ON m.idFournisseur = f.idFournisseur WHERE idElement = :idElement;');
                            $query->execute(array('idElement' => $_GET['id']));
                            $data = $query->fetch();
                            ?>
                            <div class="form-group">
                                <label>Référence du catalogue:</label>
                                <select class="form-control" name="libelleMateriel">
                                    <?php
                                    $query2 = $db->query('SELECT * FROM MATERIEL_CATALOGUE ORDER BY libelleMateriel;');
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data2['idMaterielCatalogue']; ?>"<?php if ($data2['idMaterielCatalogue'] == $data['idMaterielCatalogue']) { echo 'selected'; } ?> ><?php echo $data2['libelleMateriel']; ?></option>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Lot: </label>
                                <select class="form-control" name="libelleLot">
                                    <option></option>
                                    <?php
                                    $query2 = $db->query('SELECT * FROM LOTS_LOTS ORDER BY libelleLot;');
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data2['idLot']; ?>"<?php if ($data2['idLot'] == $data['idLot']) { echo 'selected'; } ?> ><?php echo $data2['libelleLot']; ?></option>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Sac: </label>
                                <select class="form-control" name="libelleSac">
                                    <option></option>
                                    <?php
                                    $query2 = $db->query('SELECT * FROM MATERIEL_SAC s LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot ORDER BY libelleSac;');
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data2['idSac']; ?>" data-id="<?php echo $data2['idLot']; ?>" <?php if ($data2['idSac'] == $data['idSac']) { echo 'selected'; } ?> data-id="<?php echo $data['libelleLot']; ?>"><?php echo $data2['libelleSac']; ?></option>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Emplacement: </label>
                                <select class="form-control" name="libelleEmplacement">
                                    <option></option>
                                    <?php
                                    $query2 = $db->query('SELECT * FROM MATERIEL_EMPLACEMENT e LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot ORDER BY libelleEmplacement;');
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data2['idEmplacement']; ?>" data-id="<?php echo $data2['idSac']; ?>" <?php if ($data2['idEmplacement'] == $data['idEmplacement']) { echo 'selected'; } ?> data-id="<?php echo $data['libelleSac']; ?>"><?php echo $data2['libelleEmplacement']; ?></option>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Fournisseur:</label>
                                <select class="form-control" name="nomFournisseur">
                                    <option></option>
                                    <?php
                                    $query2 = $db->query('SELECT * FROM FOURNISSEURS ORDER BY nomFournisseur;');
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data2['idFournisseur']; ?>" <?php if ($data2['idFournisseur'] == $data['idFournisseur']) { echo 'selected'; } ?> ><?php echo $data2['nomFournisseur']; ?></option>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Quantité:</label>
                                <input type="text" class="form-control"  value="<?=$data['quantite']?>"
                                       name="quantite">
                            </div>
                            <div class="form-group">
                                <label>Quantité d'Alerte:</label>
                                <input type="text" class="form-control"  value="<?=$data['quantiteAlerte']?>"
                                       name="quantiteAlerte">
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input
                                            <?php
                                                if($data['peremption'] != Null)
                                                {
                                                    echo 'checked';
                                                }
                                            ?>
                                            type="checkbox" value="1" name="boolPeremption" onClick="montrer_cacher(this,'perem')"> Le matériel a une date de péremption
                                </label>
                            </div>

                            <div class="form-group" id="perem">
                                <label>Date de péremption:</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input class="input-datepicker form-control" name="peremption" value="<?=$data['peremption']?>">
                                </div>
                                <br/>
                                <label>Jours d'anticipation de l'alerte de péremption:</label>
                                <input type="text" class="form-control" value="<?php echo (strtotime($data['peremption']) - strtotime($data['peremptionNotification']))/86400; ?>"
                                       name="delaisPeremption">
                                <!-- /.input group -->
                            </div>

                            <div class="form-group">
                                <label>Commentaires</label>
                                <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails"
                                          name="commentairesElement"><?=$data['commentairesElement']?></textarea>
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

<script>
    $(function () {
        function listenSelect(listenedSelector, listenerSelector) {
            $(listenedSelector).change(function () {
                $(listenerSelector).val('');
                $(listenerSelector).change();

                $(listenerSelector).children().hide();
                $(listenerSelector).children('[value=""]').show();
                $(listenerSelector).children('[data-id="'+$(listenedSelector).val()+'"]').show();
            });
        }

        listenSelect('select[name="libelleLot"]', 'select[name="libelleSac"]');
        listenSelect('select[name="libelleSac"]', 'select[name="libelleEmplacement"]');

        $('select[name="libelleSac"], select[name="libelleEmplacement"]').children('option').hide();

    });
</script>