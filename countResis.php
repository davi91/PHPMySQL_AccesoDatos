<?php

	include ("mySql_residencias.php");

	if( isset($_REQUEST["uni"]) && isset($_REQUEST["precio"])) {

		$resi = conectarResi();

		$consulta = $resi->prepare('Call sp_cuentaResidencias( :uni, :precio, @cantResi, @canResiPrecio)'); // Ahora llamamos a un procedimiento

		$consulta->bindValue(":uni", $_REQUEST["uni"]);
		$consulta->bindValue(":precio", $_REQUEST["precio"]);

		$consulta->execute();

		// Aquí cambia la cosa, puesto que los parámetros de salida se han guardado en variables
		$consulta = $resi->query('select @cantResi as cantidadResi, @canResiPrecio as cantidadResiPrecio'); 
		$data = $consulta->fetch(); // Accedemos a ese dato

		$ret = $data["cantidadResi"] . ";" . $data["cantidadResiPrecio"];
		
		$resi = null;
		echo $ret;
		
	}

	else {
		$resi = null;
		echo "-1";
	}
?>