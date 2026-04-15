<?php
    // Llama a la base de datos
    require("conexion.php");

    // Verificar que la conexión esté activa
    if (!$con) {
        die("Error de conexión a la base de datos");
    }

    //Obtiene los datos del formulario
    $codigo_original = $_POST['codigo_original'] ?? '';
    $cod = $_POST['codBodega'] ?? '';
    $nombre = $_POST['nomBodega'] ?? '';
    $direccion = $_POST['direcBodega'] ?? '';
    $cantidadPerson = $_POST['dotaBodega'] ?? '';
    $estado = $_POST['estadoBodega'] ?? '';
    //$fecha_creacion = $_POST['fechaBodega'] ?? '';

    //Valida que no haya campos vacíos
    if (empty($cod) || empty($nombre) || empty($direccion) || empty($cantidadPerson)) {
        die("Error: Todos los campos son obligatorios. <a href='bodega.php'>Volver</a>");
    }

    // Limpia los valores para evitar inyección SQL
    $cod = pg_escape_string($con, $cod);
    $nombre = pg_escape_string($con, $nombre);
    $direccion = pg_escape_string($con, $direccion);
    $cantidadPerson = pg_escape_string($con, $cantidadPerson);
    $estado = pg_escape_string($con, $estado);
    //$fecha_creacion = pg_escape_string($con, $fecha_creacion);
    $codigo_original = pg_escape_string($con, $codigo_original);
    $encargados = $_POST['encargados'] ?? []; //Array

    // Consulta para editar los elementos (UPDATE)
    $query = "UPDATE public.\"BODEGAS\" SET 
        cod_bodega = '$cod',
        nombre_bodega = '$nombre',
        direccion_bodega = '$direccion',
        cant_personas_bodega = '$cantidadPerson',
        estado_bodega = '$estado'
        WHERE cod_bodega = '$codigo_original'";
    //--,fecha_creacion_bodega = '$fecha_creacion'

    // Ejecuta consulta update
    $result = pg_query($con, $query);


if (!$result) {
        pg_query($con, "ROLLBACK");
        $error = pg_last_error($con);
        $resultado = false;
    } else {
        // Elimina encargados existentes de la tabla LISTADO_BOODEGAS
        $query_delete = "DELETE FROM public.\"LISTADO_BOODEGAS\" WHERE cod_bodega = '$cod'";
        $result_delete = pg_query($con, $query_delete);
        
        if (!$result_delete) {
            pg_query($con, "ROLLBACK");
            $error = pg_last_error($con);
            $resultado = false;
        } else {
            // Inserta nuevos encargados
            $resultado = true;
            if (!empty($encargados)) {
                foreach ($encargados as $run_encargado) {
                    $run_encargado = pg_escape_string($con, $run_encargado);
                    $query_insert = "INSERT INTO public.\"LISTADO_BOODEGAS\" (cod_bodega, run_encargado) 
                                    VALUES ('$cod', '$run_encargado')";
                    $result_insert = pg_query($con, $query_insert);
                    
                    if (!$result_insert) {
                        pg_query($con, "ROLLBACK");
                        $error = pg_last_error($con);
                        $resultado = false;
                        break;
                    }
                }
            }
            
            if ($resultado) {
                pg_query($con, "COMMIT");
            }
        }
    }

?>

<!--Muestra mensaje si se edito o no el archivo -->
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="estilo.css">
    <!-- <meta http-equiv="refresh" content="2;url=bodega.php"> "linea que ayuda a redirigir a la pagina de bodega.php--> 
</head>
<body>
    <div style="text-align: center; margin-top: 50px;">
        <?php if ($result): ?>
            <h2 style="color: green;">✅ ¡Registro Actualizado Correctamente!</h2>
            <p>Redirigiendo a la lista de bodegas...</p>
        <?php else: ?>
            <h2 style="color: red;">❌ Error al Actualizar</h2>
            <p><?php echo pg_last_error($con); ?></p>
            <p>Redirigiendo a la lista de bodegas...</p>
        <?php endif; ?>
        <p><a href="bodega.php">Volver ahora</a></p>
    </div>
</body>
</html>

<?php
    pg_close($con);
?>