<?php

	include ("mySql_residencias.php");

	$residb = conectarResi();
	
	// Esto es mejor hacerlo en la parte del cliente con JavaScript, pero de momento lo hacemos aquí
	if( $_POST["NombreResi"] == "" ) {
		$residb = null;
		echo "<script>
				alert('No se introdujo ningún nombre para la residencia');
				window.location.replace('altaResidencias.php');
			  </script>";
	} else {
		$nombre = $_POST["NombreResi"];
	}

	$comedor = (!isset($_POST["comedor"]) || $_POST["comedor"] == "off") ? 0 : 1; 
	
	if( isset($_POST["precio"])) {
		$precio = $_POST["precio"];
	} else {
		$precio = 900; // Por defecto
	}


	// Una vez cargados los datos empezamos a insertarlos en la base de datos
	if( isset($_REQUEST["subInfo"])) {
		$datos = $residb->prepare("update residencias set nomResidencia=:nombre, codUniversidad=:uni, precioMensual=:precio, comedor=:comedor where codResidencia=:resi");
		$datos->bindValue(":resi", $_REQUEST["subInfo"] );
	} else {
		$datos = $residb->prepare("insert into residencias values (null, :nombre, :uni, :precio, :comedor)");
	}
	

	$datos->bindValue(":nombre", $nombre);
	$datos->bindValue(":uni", $_POST["uni"]); 
	$datos->bindValue(":precio", $precio);
	$datos->bindValue(":comedor", $comedor);


	if (!$datos->execute() ) {
		$datos = null;
		echo "<script type='text/javascript'>
					alert('Error la insertar los datos');
				    window.location.replace('http://localhost/miweb/residencias.php');
			</script>";

	} else {
		$datos = null;

		if( !isset($_REQUEST["subInfo"])) { // Si no vamos a modificar, ejecutamos esto
			echo "<script type='text/javascript'>
						alert('Datos insertados correctamente');
						if( !confirm('¿ Desea introducir algún otro dato ?')) {
							window.location.replace('http://localhost/miweb/residencias.php');
						}
						else {
					   	 	window.location.replace('http://localhost/miweb/altaResidencias.php');
					   	}
				</script>";
		} else { // En caso contrario, simplemente vamos a la página principal
			header("Location: residencias.php");
			exit();
		}
	}

?>
