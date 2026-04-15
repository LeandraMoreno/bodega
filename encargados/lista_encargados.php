<!-- Llama a la base de datos-->
<?php require("bd_conect.php");  

$consulta = "SELECT * FROM public.\"ENCARGADO_BODEGA\" ORDER BY run_encargado ASC";
$result = pg_query($con, $consulta) or die("Error en consulta: " . pg_last_error($con));
?>


<!DOCTYPE html>
<html>
    <head>
        <!--Agrego una hoja de estilo a la página -->
        <link rel="stylesheet" type="text/css" href="/bodega/bodega/estilo.css">
    </head>

    <body>
        <a href="../"><h2>Tabla Encargados</h2></a>
        <table>

            <tr>
                <th>Run Encargado</th>
                <th>Nombre Encargado</th>
                <th>Apellido Paterno Encargado</th>
                <th>Apellido Materno Encargado</th>
                <th>Dirección Encargado</th>
                <th>Telefono Encargado</th>
            </tr>

<tbody>
<?php
// Verificar si hay datos en la tabla
if (pg_num_rows($result) == 0) {
    echo "<tr><td colspan='6'>No se encontraron registros</td></tr>";
} else {
    // Muestra los datos de la tabla
    while($obj = pg_fetch_object($result)){ 
        if ($obj) { // Verifica que el objeto no sea null
            ?>
            <tr>
                <td><?php echo htmlspecialchars($obj->run_encargado ?? '') ?></td>
                <td><?php echo htmlspecialchars($obj->nombre_encargado ?? '') ?></td>
                <td><?php echo htmlspecialchars($obj->appaterno_encargado ?? '') ?></td>
                <td><?php echo htmlspecialchars($obj->apmaterno_encargado ?? '') ?></td>
                <td><?php echo htmlspecialchars($obj->direccion_encargado ?? '') ?></td>
                <td><?php echo htmlspecialchars($obj->telefono_encargado ?? '') ?></td>

            </tr>
            <?php
        }
    }
}

// Libera recursos
pg_free_result($result);

?>
        </table>

    </body>
</html>