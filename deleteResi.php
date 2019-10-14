<?php

	include ("mySql_residencias.php");


	// El array debe estar en el formato: a, b, c, ......
	$array = json_decode($_REQUEST['deleteArrayData']); // Me lo pasan como array

	if( $array != null ) {

		$residb = conectarResi();

		// Hay un problema con PDO usando arrays en las sentencias preparadas sin usar las sentencias anónimas
		$in  = str_repeat('?,', count($array) - 1) . '?';
		$delete = $residb->prepare("delete from residencias where codResidencia in ($in)");

		try {

			// Como es anónima, le tenemos que pasar el array como parámetro
			$delete->execute($array);

			$residb = null;
			echo "<script>
					alert('Los elementos se han borrado correctamente');
					window.location.replace('residencias.php'); // No nos interesa guardar en el historial el php
				</script>";

			exit();

		} catch( PDOException $e) {

			$residb = null;
			echo "<script>
					alert('Error al borrar los elementos seleccionados, quizás los valores están en otra tabla');
					window.location.replace('residencias.php');
				</script>";

			exit();
		}		
		
	}

	else {

			echo "<script>
					alert('No se han pasado los parámetros de la forma correcta');
					window.location.replace('residencias.php');
				</script>";		
	}

?>

