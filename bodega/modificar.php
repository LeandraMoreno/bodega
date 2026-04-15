<?php
    //<!-- Llama a la base de datos-->
    require("conexion.php");

    $codigo = $_GET['codigo'] ?? '';
    $codigo = pg_escape_string($con, $codigo);

    // Obtiene los datos de la bodega seleccionada
    $query = "SELECT * FROM public.\"BODEGAS\" WHERE cod_bodega = '$codigo'";
    $result = pg_query($con, $query);
    $bodega = pg_fetch_object($result);

    if (!$bodega) {
        echo "Bodega no encontrada";
        exit;
    }

    // Se obtiene los encargados actuales de esta bodega
    $query_encargados_actuales = "SELECT run_encargado FROM public.\"LISTADO_BOODEGAS\" WHERE cod_bodega = '$codigo'";
    $result_encargados_actuales = pg_query($con, $query_encargados_actuales);
    $encargados_actuales = [];
    while ($row = pg_fetch_object($result_encargados_actuales)) {
        $encargados_actuales[] = $row->run_encargado;
    }

    // se obtiene todos los encargados disponibles de la tabla ENCARGADO_BODEGA
    $query_todos_encargados = "SELECT run_encargado, nombre_encargado, appaterno_encargado, apmaterno_encargado 
                            FROM public.\"ENCARGADO_BODEGA\" 
                            ORDER BY nombre_encargado";
    $result_todos_encargados = pg_query($con, $query_todos_encargados);
    
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="estilo.css">
    </head>
    <body>
        <h2>Editar Bodega</h2>

        <!-- Creación vista formulario, para Editar los datos de Bodega-->
        <form action="editar_bodega.php" method="POST" class="form-container">
            <input type="hidden" name="codigo_original" value="<?php echo $bodega->cod_bodega; ?>">
            
            <label><b>Codigo Bodega</b></label>
            <input type="text" name="codBodega" value="<?php echo htmlspecialchars($bodega->cod_bodega); ?>" readonly>
            
            <label><b>Nombre Bodega</b></label>
            <input type="text" name="nomBodega" value="<?php echo htmlspecialchars($bodega->nombre_bodega); ?>" required>
            
            <label><b>Dirección Bodega</b></label>
            <input type="text" name="direcBodega" value="<?php echo htmlspecialchars($bodega->direccion_bodega); ?>" required>
            
            <label><b>Dotación Bodega</b></label><br>
            <input type="number" name="dotaBodega" value="<?php echo htmlspecialchars($bodega->cant_personas_bodega); ?>" required>
    <br> <!-- Espacio extra-->        
            <label><b>Estado Bodega</b></label><br>
            <select name="estadoBodega" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f1f1f1;">
                <option value="true" <?php echo ($bodega->estado_bodega == 't' || $bodega->estado_bodega == 'true' || $bodega->estado_bodega == '1') ? 'selected' : ''; ?>>Activo</option>
                <option value="false" <?php echo ($bodega->estado_bodega == 'f' || $bodega->estado_bodega == 'false' || $bodega->estado_bodega == '0') ? 'selected' : ''; ?>>Inactivo</option>
            </select>

    <br> <!-- Espacio extra-->

<!--Seleccion Multiple, para agregar encargados -->
    <label><b>Encargados de Bodega</b></label>
        <select name="encargados[]" multiple size="5" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            <?php if (pg_num_rows($result_todos_encargados) == 0): ?>
                <option disabled>No hay encargados registrados.</option>
            <?php else: ?>
                <?php while ($encargado = pg_fetch_object($result_todos_encargados)): ?>
                    <option value="<?php echo $encargado->run_encargado; ?>"
                        <?php echo (in_array($encargado->run_encargado, $encargados_actuales)) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($encargado->run_encargado . ' - ' . $encargado->nombre_encargado . ' ' . $encargado->appaterno_encargado . ' ' . $encargado->apmaterno_encargado); ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>
        <small>Presione Ctrl para seleccionar múltiples encargados</small>
<!-- -->
        
<!--Se le da un Formato de Fecha y hora para que pueda editar 
            <label><b>Fecha creación Bodega</b></label>
            <input type="datetime-local" name="fechaBodega" 
                value="<?php echo date('Y-m-d\TH:i', strtotime($bodega->fecha_creacion_bodega)); ?>" required>
            <small>Formato: DD/MM/AAAA HH:MM</small>
-->
            
            <div class="contenedor-botones">
                <button type="submit" class="btn">Guardar</button>
                <a href="bodega.php" class="btn cancel" >Cancelar</a>
            </div>
        </form>
    </body>
</html>