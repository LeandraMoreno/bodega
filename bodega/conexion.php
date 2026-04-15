<!--Archivo que ayudará a conectarse a la Base de datos en Postgresql -->
<?php
    $con = pg_connect("host='localhost' dbname=DBproyecto port=5432 user=postgres password=root") 
        or die("Error de Conexion: " . pg_last_error());

    $consulta = "SELECT * FROM public.\"BODEGAS\" ORDER BY cod_bodega ASC";
    $result = pg_query($con, $consulta) or die("Error en query: " . pg_last_error());

    /*while ($row = pg_fetch_object($result)) {
        print_r($row);
        echo "<br>";
    }*/

    //pg_close($con);
?>