<?php
	require_once('config/config.php');
?>

<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <a href="http://maj.guajioty.fr/gpm.zip"><b style="color:red;"><?php echo file_get_contents("https://maj.guajioty.fr/gpmMAJ.php?versionClient=".$VERSION); ?></b></a>
        GPM - Version <?php echo $VERSION; ?>
    </div>
    <a rel="license" href="https://creativecommons.org/licenses/by-nc-sa/4.0/">Licence</a>
</footer>