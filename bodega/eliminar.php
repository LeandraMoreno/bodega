<?php
    //<!-- Llama a la base de datos-->
    require("conexion.php");

    $codigo = $_GET['codigo'] ?? '';
    $codigo = pg_escape_string($con, $codigo);

    // Verifica si la bodega tiene "encargados asignados"
    $check_query = "SELECT COUNT(*) as total FROM public.\"LISTADO_BOODEGAS\" WHERE cod_bodega = '$codigo'";
    $check_result = pg_query($con, $check_query);
    $check_row = pg_fetch_object($check_result);


if ($check_row->total > 0) {
    // Primero hay que eliminar las relaciones de encargados (tabla LISTADO_BOODEGAS)
    $delete_relaciones = "DELETE FROM public.\"LISTADO_BOODEGAS\" WHERE cod_bodega = '$codigo'";
    $result_relaciones = pg_query($con, $delete_relaciones);
    
    if (!$result_relaciones) {
        pg_query($con, "ROLLBACK");
        $error = pg_last_error($con);
        $resultado = false;
    } else {
        // Luego se elimina de la tabla BODEGAS
        $delete_bodega = "DELETE FROM public.\"BODEGAS\" WHERE cod_bodega = '$codigo'";
        $result_bodega = pg_query($con, $delete_bodega);
        
        if ($result_bodega) {
            pg_query($con, "COMMIT");
            $resultado = true;
        } else {
            pg_query($con, "ROLLBACK");
            $error = pg_last_error($con);
            $resultado = false;
        }
    }
} else {
    // Si no tiene encargados, elimina directamente de la tabla BODEGAS
    $delete_bodega = "DELETE FROM public.\"BODEGAS\" WHERE cod_bodega = '$codigo'";
    $result_bodega = pg_query($con, $delete_bodega);
    
    if ($result_bodega) {
        pg_query($con, "COMMIT"); //guarda
        $resultado = true;
    } else {
        pg_query($con, "ROLLBACK"); //revierte(deshace)
        $error = pg_last_error($con);
        $resultado = false;
    }
}

?>
<!DOCTYPE html>
<html>
        <head>
            <link rel="stylesheet" type="text/css" href="estilo.css">
        </head>
        <body>
            <div style="text-align: center; margin-top: 50px;">
                <?php if ($resultado): ?>
                    <h2 style="color: green;">✅ Bodega eliminada correctamente</h2>
                    <p>Se eliminó la bodega con código: <?php echo htmlspecialchars($codigo); ?></p>
                    <p>También se eliminaron <?php echo $check_row->total; ?> relación(es) de encargados.</p>
                <?php else: ?>
                    <h2 style="color: red;">❌ Error al eliminar bodega</h2>
                    <p><?php echo $error ?? pg_last_error($con); ?></p>
                <?php endif; ?>
                <br>
                <a href="bodega.php" class="btn">Volver a la lista</a>
            </div>
        </body>
</html>


<?php pg_close($con); ?>