<?php
require_once 'config/config.php';
?>

    <?php include("headerContent.php"); ?>
    
    <?php if($RECAPTCHA_ENABLE){?> <script src="https://www.google.com/recaptcha/api.js?render=<?=$RECAPTCHA_SITEKEY?>"></script> <?php } ?>
    
