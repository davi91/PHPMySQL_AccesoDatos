<!DOCTYPE html>
<html>
<head>
	<title>Visualización residencias escolares</title>
	<meta charset="utf-8">

	<!-- Para la comunicación entre servidor y cliente necesitamos Ajax -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

	<style type="text/css">
		
		.tableBt {

			width: 100%;
			background-color: LightBlue;
			border-radius: 2px;
		}

	</style>
</head>

<body>

	<h3>DAVID FERNÁNDEZ NIEVES</h3>
	<h4>2ºDAM A, AED</h4>

	<form name="resiFormVisual" style="background-color: MintCream" method="POST" id="form">
		<fieldset>

			<legend><u><b>Residencias escolares</b></u></legend>

			<table border="1px solid gray">

				<tr>
					<th>Código residencia</th>
					<th>Nombre residencia</th>
					<th>Nombre universidad</th>
					<th>Precio Mensual</th>
					<th>Comedor</th>
					<th>Baja</th>
					<th>Modificación</th>
				</tr>

				<?php

					include ("mySql_residencias.php");

					$residb = conectarResi();

					// Preparamos lo que queremos
					$consulta = $residb->prepare("select * from residencias");

					// Ejecutamos
					$consulta->execute();

					$uniData = $residb->prepare("select codUniversidad, nomUniversidad from universidades as uni") ;
					$uniData->execute();
					$array = $uniData->fetchAll(); // Array de universidades, hay que tener cuidado porque me los carga en memoria, lo usamos si no se esperan muchos datos
					// Vamos a ir recorriendo las filas
					while($row = $consulta->fetch()) { 
						// El array_column te lo pone como un array unidimensional, puesto que lo que nos devuelve la sentencia atenrior es un arrray bidimensional
						$id = array_search($row["codUniversidad"], array_column($array, "codUniversidad")); // Buscamos donde está la universidad que queremos
						?>
						<tr>	
							<td><?php echo $row["codResidencia"] ?></td>
							<td><?php echo $row["nomResidencia"] ?></td>
							<td><?php echo $array[$id]["nomUniversidad"] ?></td> <!-- La columna 1 hace referencia a nombre de universidad -->
							<td><?php echo $row["precioMensual"] ?></td>
							<td><?php echo ($row["comedor"] == 1) ? "Si" : "No" ?></td>

							<!-- Aquí usaremos un pequeño truco entre servidor-cliente usando JSON, que nos permite pasar array de PHP a JavaSCript -->
						<!--	<td><input type="button" <?php echo 'onClick=onDelete('.json_encode($row).')' ?> value="Dar de baja" class="tableBt"></td> OLD METHOD -->
							<td align="center"><input type="checkbox" <?php echo 'id="ch_'.$row["codResidencia"].'" value='.$row["codResidencia"] ?> onClick=onDeleteChecked(this) ></td>
							<td><input type="button" <?php echo 'onClick=onModify('.json_encode($row).')' ?> value="Modificar" class="tableBt"></td>
						</tr>

					<?php }

					// -- *
				?>

			</table>

			<input type="hidden" id="deleteArrayData" name="deleteArrayData">

			<br><br>

			<input type="button" name="goInsertBt" onClick="onGoInsert()" value="Insertar nuevo registro" style="border-radius: 10%; color: white; background-color: LightSlateGray" >
			&nbsp <input type="button" name="goDeleteBt" onClick="onDeleteSeleced()" value="Eliminar elementos seleccionados" style="border-radius: 10%; color: white; background-color: LightSlateGray" >

			<hr>
			<!-- Consulta para ver cuantas residencias por universidad hay con menor precio -->
			<br><b>Cuenta de residencias por universidad a un precio menor:</b><br><br>

			<select id="unis" name="universidades">
			<?php  
					// * --
					// echo "<script>console.log(". json_encode($array) . ")</script>"; DEBUG
			
					// Ahora vamos a ver las universidades disponibles, usamos lo que ya tenemos más arriba
					foreach ($array as $value) {  // Recordemos que es un array bidimensional
						echo '<option value='.$value[1].'>'.$value[1].'</option>';
					}
		
					$residb = null;
			?>

			</select>

			&nbsp Precio: <input id="precio" type="number" name="precioSelect" value="0">

			&nbsp <input type="button" onClick="getNumResis()" name="selectResiCount" value="Consultar residencias">

			<br><br><p id="r_p1" style="visibility: hidden; font-weight: bold; font-style: italic; display: none"></p><br>
			<p id="r_p2" style="visibility: hidden; font-weight: bold; font-style: italic; display: none"></p>
			
			<hr>

			<p><b>Esancias por alumno</b></p>
			<br><input type="text" name="estanciasDNI" placeholder="DNI del alumno" id="dni_estancia">
			&nbsp <input type="button" name="consultaEstanaciasBt" value="Consultar estancias" onClick="onConsultaEstancia()">
			&nbsp <input type="button" name="tiempoBt" value="Consultar tiempo" onClick="onConsutltaTiempo()">

			<br><br><p id="p_time" style="visibility: hidden; font-weight: bold; font-style: italic; display: none"></p>
		</fieldset>
	</form>


	<script type="text/javascript">

		var deleteArray = new Array();

		function onConsultaEstancia() {

			// Nuestra intención es abrir una nueva ventana que muestra los datos soliccitados
			var str = "muestraEstancias.php?dni=" + document.getElementById("dni_estancia").value;

			// Lo mostramos en una ventana aparte, no en una pestaña
			window.open(str, "_blank", "toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=800,height=600");
		}

		function onGoInsert() {

			window.location.href = 'http://localhost/miweb/altaResidencias.php'; // Vamos a la página de insercciones.
		}

		function onModify(row) {

			form = document.getElementById("form");

			/*
				 Si queremos pasarlo en forma de array, usamos esta forma:
				 ....+JSON.stringify(row);
				 y en el PHP:
				 $array = json_decode($_REQUEST[.....])
				 y para acceder a estos objetos especiales:
				 $array->{"nombreCampo"}....para acceder a objetos.
			*/

			// Por defecto, la enviamos por GET, el Ajax puede ser también una opción para la comunicación entre cliente y servidor
			form.action = "altaResidencias.PHP?resi="+row.nomResidencia+"&cod="+row.codUniversidad+"&prec="+row.precioMensual+"&com="+row.comedor+"&mod="+row.codResidencia;
			form.submit();
		}

		function onDeleteChecked(e) {

			// Lo que vamos a hacer es comprobar si está con checked o no, porque no hay un evento directo que nos lo diga
			if( e.checked ) {
				deleteArray.push(e.value); // Lo colocamos en el Array
			} else {
				deleteArray.splice(deleteArray.indexOf(e.value), 1); // Eliminamos en el índice donde se encuentre 1 elemento
			}
		}

		function onDeleteSeleced() {

			/* Ahora simplemente pasamos el array a PHP para que se eliminen los datos, lo vamos a pasar como un string
			   que es lo que utilizaremos en la sentencia del MySQL */

			   if( deleteArray.length <= 0 ) {
			   		alert("No se ha seleccionado ningún elemento");
			   		return;
			   }

			   // Como el array puede llegar a ser muy largo, será mejor pasarlo como formulario con el método POST y guardar esta información en un hidden
			   // Aquí no usamos Ajax, simplemente enviamos el formulario y así recargamos la página cn los datos actualizados
			   document.getElementById("deleteArrayData").value = JSON.stringify(deleteArray);
			   form = document.getElementById("form");
			   form.action = "deleteResi.php";
			   form.submit();
		}

		/* OLD  METHOD
		function onDelete(row) {

			if( !confirm("¿Está seguro de eliminar la residencia " + row.nomResidencia + "?") ) {
				return;
			}

			// Otra forma es usando $_SESSION o cookies
			// También desde JS podemos llamar a funciones del PHP, pero en nuestro caso vamos a hacerlo por separado
			form = document.getElementById("form");
			form.action = "deleteResi.php?resi="+row.codResidencia; // Enviamos nuestro elemento a eliminar -> Métood GET
			form.submit();
		}
		*/
		function getNumResis() {

			var p1 = document.getElementById("r_p1");
			var p2 = document.getElementById("r_p2");
			// Vamos a usar a nuestro aliado Ajax para así no cambiar de página
      		 var xmlhttp = new XMLHttpRequest(); // Abrimos el canal, además, con este objeto no hace falta refrescar la página
       		 xmlhttp.onreadystatechange = function() { // Vemos cuando cambia el estado de la conexión -> Función anónima de JS
         		   if (this.readyState == 4 && this.status == 200) { // 200 = "Ok", 4 = La petición ha sido enviada y la respuesta ha sido recibida
          			    // Hemos recibido los resultados, se guardan en la variable responseText

          				if( this.responseText != "-1" ) { // -1 seria nuestro código de error de datos no encontrados

	          				var res = this.responseText.split(';');	

	          				// En cuanto le damos a la consulta, el texto ya se queda anclado
	          				p1.innerHTML = "El número de residencias coincidentes es " + res[0];
	          				p1.style.visibility = "visible";
	          				p1.style.display = "inline";
	          				p2.innerHTML = "El número de residencias con menor precio a " + document.getElementById("precio").value + " es " + res[1];    
	          				p2.style.visibility = "visible"; 		
	          				p2.style.display = "inline";	  	
	          			}

	          			else {

	          				p1.style.visibility = "hidden";
	          				p1.style.display = "none";
	          				p2.style.visibility = "hidden";
	          				p2.style.display = "none";
	          				alert("Error en la consulta de los datos");
	          			}
            }
	        };

	       	// Abrimos el PHP, el true se refiere a que es asíncrono, así el JS se puede ejecutar sin tener que esperar
	        xmlhttp.open("POST", "countResis.php?uni="+document.getElementById("unis").value+
	        								   "&precio="+document.getElementById("precio").value,
	        								   true); 

	        xmlhttp.send(); // Enviamos nuestra petición

		}

		
		function onConsutltaTiempo() {

			var xmlhttp = new XMLHttpRequest();
			var p = document.getElementById("p_time");
			xmlhttp.onreadystatechange = function() {

				if( this.readyState == 4 && this.status == 200 ) {

					if( this.responseText != "-1" ) { // -1 seria nuestro código de error de datos no encontrados
					
						p.style.display = "inline";
						p.style.visibility = "visible";
						p.innerHTML = "Tiempo de estancia: " + this.responseText + " meses";
					}

					else {

						p.style.visibility = "hidden";
						p.style.display = "none";
						alert("No se introdujo un dato válido en el DNI");
					}
				}
			};

			xmlhttp.open("POST", "tiempoEstudiante.php?dni="+document.getElementById("dni_estancia").value, true);
			xmlhttp.send();
		}


	</script>

</body>
</html>