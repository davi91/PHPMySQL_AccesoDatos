<?php

	include ("mySql_residencias.php");

	if( isset($_REQUEST["dni"]) && !empty($_REQUEST["dni"])) {

		$resi = conectarResi();

		// Para llamar a una función usamos el select y luego usamos esa variable
		$stmt = $resi->prepare("select fn_tiempoResidencias( :dni ) as time");
		$stmt->bindValue(":dni", $_REQUEST["dni"]);

		$stmt->execute();
		$data = $stmt->fetch();

		$resi = null;
		
		// En este caso, devolvemos -1 si no hay ningún dato de ese alumno
		if( empty($data["time"]) ) { // Significa que no ha devuelto nada
			echo "-1";
		} else {
			echo $data["time"];	
		}
	}

	else {

		$resi = null;
		echo "-1";
	}

?>