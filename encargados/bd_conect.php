<!--Archivo que ayudará a conectarse a la Base de datos en Postgresql -->
<?php
$con = pg_connect("host='localhost' dbname=DBproyecto port=5432 user=postgres password=root") 
      or die("Error de Conexion: " . pg_last_error());

$consulta = "SELECT * FROM public.\"ENCARGADO_BODEGA\" ORDER BY run_encargado ASC";
$result = pg_query($con, $consulta) or die("Error en query: " . pg_last_error());


//pg_close($con);
?>