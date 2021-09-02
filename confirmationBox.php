<?php

if($_SESSION['DELEGATION_ACTIVE']==1)
{
    echo '<div class="alert alert-info">';
    echo '<center>Session en délégation - '.$_SESSION['identifiant_OLD'].' connecté en tant que '.$_SESSION['identifiant'].'</center>';
    echo '</div>';
}

if ($_SESSION['returnMessage'])
{
    if ($_SESSION['returnType'] == 1)
    {
        echo '<div class="alert alert-success alert-dismissible">';
        echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
        echo '<i class="icon fa fa-check"></i>' . $_SESSION['returnMessage'];
        echo '</div>';
        unset($_SESSION['returnMessage']);
        unset($_SESSION['returnType']);
    }
    else
    {
        echo '<div class="alert alert-warning alert-dismissible">';
        echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
        echo '<i class="icon fa fa-warning"></i>' . $_SESSION['returnMessage'];
        echo '</div>';
        unset($_SESSION['returnMessage']);
        unset($_SESSION['returnType']);
    }
}
?>