<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

$query = $db->prepare('SELECT * FROM CENTRE_COUTS WHERE idCentreDeCout = :idCentreDeCout');
$query->execute(array('idCentreDeCout' => $_GET['idCentreDeCout']));
$data = $query->fetch();

$query2 = $db->prepare('SELECT COALESCE(SUM(montantEntrant),0)-COALESCE(SUM(montantSortant),0) as total FROM CENTRE_COUTS_OPERATIONS WHERE idCentreDeCout = :idCentreDeCout');
$query2->execute(array('idCentreDeCout'=>$_GET['idCentreDeCout']));
$enCours = $query2->fetch();
$enCours = round($enCours['total'],2);


if($data['dateFermeture'] != Null AND $data['dateFermeture'] < date('Y-m-d') AND $enCours>0 AND centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['idCentreDeCout'])==1)
{ ?>
    
    <div class="modal fade" id="modalCoutSolder">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Transférer les fonds sur un autre centre de couts</h4>
                </div>
                <form role="form" action="centreCoutsOperationsSolder.php?idSource=<?=$_GET['idCentreDeCout']?>&montant=<?=$enCours?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Montant à solder (€)</label>
                            <input type="text" class="form-control" value="<?=$enCours?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Centre de cout: </label>
                            <select class="form-control select2" style="width: 100%;" name="idCible" required>
                                <optgroup label="Ouverts">
                                	<?php
                                    $query2 = $db->prepare('
                                    	SELECT
										    *
										FROM
										    CENTRE_COUTS
										WHERE
										    (
											    (
											    	dateFermeture IS NULL
											    	AND dateOuverture <= CURRENT_DATE
											    )
											    OR
											    (
											    	dateFermeture >= CURRENT_DATE
											    	AND
											    	dateOuverture <= CURRENT_DATE
											    )
											    OR
											    (
											    	dateOuverture IS NULL
											    	AND dateFermeture IS NULL
											    )
										    )
										    AND
										    idCentreDeCout <> :idSource
										ORDER BY
											libelleCentreDecout
                                    ');
                                    $query2->execute(array('idSource'=>$_GET['idCentreDeCout']));
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data2['idCentreDeCout']; ?>"><?php echo $data2['libelleCentreDecout']; ?></option>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                </optgroup>
                                
                                <optgroup label="Ouverture future">
                                	<?php
                                    $query2 = $db->prepare('
                                    	SELECT
										    *
										FROM
										    CENTRE_COUTS
										WHERE
										    (
											    (
											    	dateFermeture IS NULL
											    	AND dateOuverture > CURRENT_DATE
											    )
											    OR
											    (
											    	dateFermeture >= CURRENT_DATE
											    	AND
											    	dateOuverture > CURRENT_DATE
											    )
										    )
										    AND
										    idCentreDeCout <> :idSource
										ORDER BY
											libelleCentreDecout
                                    ');
                                    $query2->execute(array('idSource'=>$_GET['idCentreDeCout']));
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data2['idCentreDeCout']; ?>"><?php echo $data2['libelleCentreDecout']; ?></option>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                </optgroup>
                                
                                <optgroup label="Fermés">
                                	<?php
                                    $query2 = $db->prepare('
                                    	SELECT
										    *
										FROM
										    CENTRE_COUTS
										WHERE
										    dateFermeture < CURRENT_DATE
										    AND
										    idCentreDeCout <> :idSource
										ORDER BY
											libelleCentreDecout
                                    ');
                                    $query2->execute(array('idSource'=>$_GET['idCentreDeCout']));
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <option value="<?php echo $data2['idCentreDeCout']; ?>"><?php echo $data2['libelleCentreDecout']; ?></option>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right">Transférer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
