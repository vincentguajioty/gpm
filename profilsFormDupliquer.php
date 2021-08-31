<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

$_SESSION['modals'] = [14];
require_once 'modal.php';