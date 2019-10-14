<!DOCTYPE html>
<html>
<head>
	<title>Alta residencias escolares</title>
	<meta charset="utf-8">
</head>

<body>

	<h3>DAVID FERNÁNDEZ NIEVES</h3>
	<h4>2ºDAM A, AED</h4>
	
	<form  id="form" style="background-color: MintCream" method="POST" action="resiUpdate.php">  <!-- El PHP ecargado de actualizar/insertar datos -->
		
		<fieldset>

			<legend><u><b>Altas residencias escolares</b></u></legend>

			<?php 

				// Si es una modficación, ya ponemos los campos especificados
				if( !isset( $_REQUEST["resi"]))
					echo '<label>Nombre residencia: <input type="text" name="NombreResi" placeholder="Nombre residencia"><br><br></label>'; // Al insertar
				else
					echo '<label>Nombre residencia: <input type="text" name="NombreResi" value='.$_REQUEST["resi"].'><br><br></label>'; // Al modificar
			?>

			Universidad: <select name="uni">

				<?php

					include( "mySql_residencias.php");

					$residb = conectarResi();

					$unis = $residb->prepare("select * from universidades") ;

					$unis->execute();
					$i = 0;
					$id = $_REQUEST["cod"];
					while( $row = $unis->fetch() ) { 

						if( $id != $row["codUniversidad"]) {
							echo "<option value=".$row["codUniversidad"].">".$row["nomUniversidad"]."</option>";
						} else {
							echo "<option value=".$row["codUniversidad"]." selected>".$row["nomUniversidad"]."</option>"; // Sólo uno será seleccionado
						}

						$i++;
					}

					$resid = null;
				?>

			</select><br><br>
			
			<?php 

				if( isset($_REQUEST["prec"])) { // El precio
					echo '<label>Precio: <input type="number" name="precio" min="900" value='.$_REQUEST["prec"].'><br><br></label>';
				} else {
					echo '<label>Precio: <input type="number" name="precio" min="900" value=900><br><br></label>'; // Si no está establecido
				}

				if( isset($_REQUEST["com"]) && $_REQUEST["com"] == 1) { // Comedor
					echo '<label>Comedor: <input type="checkbox" name="comedor" checked><br><br></label>'; // En caso de que esté seleccionado
				} else {
					echo '<label>Comedor: <input type="checkbox" name="comedor"><br><br></label>';
				}
			
				echo '<input type="button" name="backBt" value="Atrás" onClick=goBack() class="submitBt" style="border-radius: 10%; color: white; background-color: LightSlateGray">&nbsp';

				if( isset($_REQUEST["mod"])) {  // Variable adicional que me permite observar que vamos a modificar
					echo  '<input type="submit" name="altaBt" value="Modificar fila" class="submitBt" style="border-radius: 10%; color: white; background-color: LightSlateGray">';
					echo '<input type="hidden" name="subInfo" value='.$_REQUEST["mod"];
				} else {
					echo '<input type="submit" name="altaBt" value="Dar de alta" class="submitBt" style="border-radius: 10%; color: white; background-color: LightSlateGray">';
					echo '&nbsp <input type="button" onClick="altaProc()" name="altaBt2" value="Dar de alta (procedimiento)" class="submitBt" style="border-radius: 10%; color: white; background-color: LightSlateGray">';
				}

			?>


		</fieldset>

	</form>

			
		<script type="text/javascript">
				
			function goBack() {

				window.location.replace('residencias.php'); // Esto es para que no se refleje en el 'back' del navegador
				// En caso contrario, usaríamos window.loaction.href
			}

			function altaProc() {

				// Alta usando el procedimiento
				var form = document.getElementById("form");
				form.action = "resiInsert.php"; // El PHP encargado de insertar datos con el prodecimiento
				form.submit();
			}

		</script>

</body>
</html>