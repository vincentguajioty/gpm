<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
require_once 'config/config.php';

function detectDelimiter($csvFile)
{
    $delimiters = array(
        ';'  => 0,
        ','  => 0,
        "\t" => 0,
        "|"  => 0
    );

    $handle = fopen($csvFile, "r");
    $firstLine = fgets($handle);
    fclose($handle); 
    foreach ($delimiters as $delimiter => &$count) {
        $count = count(str_getcsv($firstLine, $delimiter));
    }

    return array_search(max($delimiters), $delimiters);
}

if ($_SESSION['annuaire_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{

    // UPLOAD DU FICHIER CSV, vérification et insertion en BASE
	if($_FILES["urlTemplate"]["type"] != "application/vnd.ms-excel")
	{
		$_SESSION['returnMessage'] = 'Merci d\'utiliser un fichier conforme au modèle fourni précédemment.';
        $_SESSION['returnType'] = '2';
	}
	else
	{
		
		//Process the CSV file
		$handle = fopen($_FILES['urlTemplate']['tmp_name'], "r");
		$data = fgetcsv($handle, 1000, detectDelimiter($_FILES['urlTemplate']['tmp_name']));
		
		while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
		{
			

			$data[0] = ($data[0] == Null) ? Null : $data[0];
			$data[1] = ($data[1] == Null) ? Null : $data[1];
			$data[2] = ($data[2] == Null) ? Null : $data[2];
			$data[3] = ($data[3] == Null) ? Null : $data[3];
			$data[4] = ($data[4] == Null) ? Null : $data[4];
			$data[5] = ($data[5] == Null) ? Null : $data[5];

			if ($data[6] == 1 OR $data[6]=='Oui' OR $data[6]=='oui' OR $data[6]=='Vrai' OR $data[6]=='vrai')
			{
				$data[6] = 1;
			}
			else
			{
				$data[6] = 0;
			}
			

			$query = $db->prepare('
				INSERT INTO
					PERSONNE_REFERENTE_TEMP
				SET
		            identifiant    = :identifiant,
		            nomPersonne    = :nomPersonne,
		            prenomPersonne = :prenomPersonne,
		            mailPersonne   = :mailPersonne,
		            telPersonne    = :telPersonne,
		            fonction       = :fonction,
		            mailCreation   = :mailCreation
		        ;');
		    $query->execute(array(
		        'identifiant'    => $data[0],
		        'nomPersonne'    => $data[1],
		        'prenomPersonne' => $data[2],
		        'mailPersonne'   => $data[3],
		        'telPersonne'    => $data[4],
		        'fonction'       => $data[5],
		        'mailCreation'   => $data[6]
		    ));

		}
		
		$query = $db->query('DELETE FROM PERSONNE_REFERENTE_TEMP WHERE identifiant IS NULL OR identifiant = \'\';');

		$_SESSION['importStade'] = 3;

	}

	unlink($_FILES['urlTemplate']['tmp_name']);
	
    echo "<script>window.location = document.referrer;</script>";


}
?>