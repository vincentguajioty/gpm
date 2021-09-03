<?php
	require_once('config/config.php');
?>

<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <a href="https://github.com/vincentguajioty/gpm"><b style="color:red;"><?php echo file_get_contents("https://maj.guajioty.fr/gpmMAJ.php?versionClient=".$VERSION); ?></b></a>
        GPM - Version <?php echo $VERSION; ?>
    </div>
    <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/"><img alt="Creative Commons License" src="https://i.creativecommons.org/l/by-nc-sa/4.0/80x15.png" /></a>
</footer>