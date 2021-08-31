<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

$_SESSION['modals'] = [100];
require_once 'modal.php';