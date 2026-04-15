<?php
    // <!-- Llama a la base de datos-->
    require("conexion.php");


    // Función para validar q el código_bodega sea alfanumérico
    function validarCodigo($codigo) {
        // Verificar longitud (max. 5 caracteres) 
        if (strlen($codigo) > 5) {
            return "El código no puede tener más de 5 caracteres";
        }
        
        // Verifica que sea alfanumérico (solo letras y números)
        if (!preg_match('/^[A-Za-z0-9]+$/', $codigo)) {
            return "El código solo puede contener letras y números";
        }
        
        return true;
    }

    // obtiene los nuevos datos ingresados del formulario
    $cod = strtoupper($_POST['codBodega'] ?? ''); // Convierte a mayúsculas
    $nombre = $_POST['nomBodega'] ?? '';
    $direccion = $_POST['direcBodega'] ?? '';
    $cantidadPerson = $_POST['dotaBodega'] ?? '';
    $estado = $_POST['estadoBodega'] ?? 'true';
    $fecha_creacion = $_POST['fechaBodega'] ?? '';

    // Valida el código_bodega
    $validacion = validarCodigo($cod);
    if ($validacion !== true) {
        die("<h2 style='color: red;'>❌ Error: $validacion</h2><br><a href='bodega.php'>Volver</a>");
    }

    // Valida otros campos del formulario
    if (empty($cod) || empty($nombre) || empty($direccion) || empty($cantidadPerson) || empty($fecha_creacion)) {
        die("Error: Todos los campos son obligatorios. <a href='bodega.php'>Volver</a>");
    }

    // Verificar/valida si el código_bodega ya existe
    $check_query = "SELECT COUNT(*) as total FROM public.\"BODEGAS\" WHERE cod_bodega = '$cod'";
    $check_result = pg_query($con, $check_query);
    $check_row = pg_fetch_object($check_result);

    if ($check_row->total > 0) {
        die("<h2 style='color: red;'>❌ Error: El código '$cod' ya existe. Use un código diferente.</h2><br><a href='bodega.php'>Volver</a>");
    }

    // limpia casillas/valores
    $cod = pg_escape_string($con, $cod);
    $nombre = pg_escape_string($con, $nombre);
    $direccion = pg_escape_string($con, $direccion);
    $cantidadPerson = pg_escape_string($con, $cantidadPerson);
    $estado_boolean = ($estado == 'true' || $estado == '1' || $estado == 't') ? 'true' : 'false';
    $fecha_creacion = pg_escape_string($con, $fecha_creacion);

    // sentencia que Inserta datos en la BD
    $query = "INSERT INTO public.\"BODEGAS\" 
            (cod_bodega, nombre_bodega, direccion_bodega, cant_personas_bodega, estado_bodega, fecha_creacion_bodega) 
            VALUES ('$cod', '$nombre', '$direccion', '$cantidadPerson', $estado_boolean, '$fecha_creacion')";
    $result = pg_query($con, $query);

?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="estilo.css">
    </head>
    <body>
        <div style="text-align: center; margin-top: 50px;">
            <?php if ($result): ?>
                <h2 style="color: green;">✅ Bodega agregada correctamente</h2>
                <p>Código Bodega Agregado: <?php echo htmlspecialchars($cod); ?></p>
                <p>Nombre Bodega Agregado: <?php echo htmlspecialchars($nombre); ?></p>
            <?php else: ?>
                <h2 style="color: red;">❌ Error al agregar bodega</h2>
                <p><?php echo pg_last_error($con); ?></p>
            <?php endif; ?>
            <br>
            <a href="bodega.php" class="btn">Volver a la lista</a>
        </div>
    </body>
</html>


<?php pg_close($con); ?>