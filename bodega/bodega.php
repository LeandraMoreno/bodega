<!-- Llama a la base de datos-->
<?php require("conexion.php");  

// Se obtiene el filtro de estado seleccionado (si existe)
$filtro_estado = $_GET['estado'] ?? 'todos';


//$consulta = "SELECT * FROM public.\"BODEGAS\" ORDER BY cod_bodega ASC";
$consulta = "SELECT 
    b.cod_bodega, 
    b.nombre_bodega, 
    b.direccion_bodega, 
    b.cant_personas_bodega, 
    b.estado_bodega, 
    b.fecha_creacion_bodega,
    (
        SELECT STRING_AGG(
            e.nombre_encargado || ' ' || e.appaterno_encargado || ' ' || e.apmaterno_encargado, 
            ', '        )
        FROM public.\"LISTADO_BOODEGAS\" be
        JOIN public.\"ENCARGADO_BODEGA\" e ON be.run_encargado = e.run_encargado
        WHERE be.cod_bodega = b.cod_bodega
    ) AS encargados_completos
  FROM public.\"BODEGAS\" b
  --ORDER BY b.cod_bodega;
  ";

  // Agregar filtro del estado_bodega
  if ($filtro_estado == 'activo') {
      $consulta .= " WHERE b.estado_bodega = true";
  } elseif ($filtro_estado == 'inactivo') {
      $consulta .= " WHERE b.estado_bodega = false";
  }
  $consulta .= " ORDER BY b.cod_bodega ASC";
  // Agregar filtro del estado_bodega


  $result = pg_query($con, $consulta) or die("Error en consulta: " . pg_last_error($con));
?>

<!DOCTYPE html>
<html>
    <head>
        <!--Agrego una hoja de estilo a la página -->
        <link rel="stylesheet" type="text/css" href="estilo.css">
    </head>

<body>
  
    <a href="../"><h2>Tabla Bodegas</h2></a>
    <a href="#" class="button green" onclick="openFormAgregar()">
        <span class="icon-house"></span>Agregar Bodega
    </a>
    

    <!-- Filtro x Estado -->
    <div class="filtros">
        <strong>Filtrar por estado: </strong>
        <a href="?estado=todos">
            <button type="button" class="filtro-todos <?php echo ($filtro_estado == 'todos') ? 'active' : ''; ?>">Todos</button>
        </a>
        <a href="?estado=activo">
            <button type="button" class="filtro-activo <?php echo ($filtro_estado == 'activo') ? 'active' : ''; ?>">Activos</button>
        </a>
        <a href="?estado=inactivo">
            <button type="button" class="filtro-inactivo <?php echo ($filtro_estado == 'inactivo') ? 'active' : ''; ?>">Inactivos</button>
        </a>
    </div>
    <!-- Filtro x Estado  -->


<table>

    <tr>
      <th>Código</th>
      <th>Nombre</th>
      <th>Direccion</th>
      <th>Dotación</th>
      <th>Nombre Completo Encargados</th>
      <th>Fecha / Hora</th>
      <th>Estado</th>
      <th colspan="2">Acciones</th>
    </tr>

<tbody>
  <?php


// Verifica si hay datos en la tabla
if (pg_num_rows($result) == 0) {
    echo "<tr><td colspan='6'>No se encontraron registros</td></tr>";
} else {
    // Muestra lo datos de la tabla
    while($obj = pg_fetch_object($result)){ 
        if ($obj) { // Verificar que el objeto no sea null
            ?>
            <tr>
                <td><?php echo htmlspecialchars($obj->cod_bodega ?? '') ?></td>
                <td><?php echo htmlspecialchars($obj->nombre_bodega ?? '') ?></td>
                <td><?php echo htmlspecialchars($obj->direccion_bodega ?? '') ?></td>
                <td><?php echo htmlspecialchars($obj->cant_personas_bodega ?? '') ?></td>
                <td><?php echo htmlspecialchars($obj->encargados_completos ?? '') ?></td>
                <td><?php echo htmlspecialchars($obj->fecha_creacion_bodega ?? '') ?></td>
                <td style="text-align: center;">
                      <?php 
                      $estado = $obj->estado_bodega ?? '';
                      $es_activo = ($estado == '1' || $estado == 't' || $estado == 'true' || strtolower($estado) == 'activo');
                      $estado_texto = $es_activo ? 'Activo' : 'Inactivo';
                      $estado_color = $es_activo ? '#4CAF50' : '#f44336';
                      ?>
                      <span style="background-color: <?php echo $estado_color; ?>; color: white; padding: 5px 10px; border-radius: 5px; display: inline-block;">
                          <?php echo $estado_texto; ?>
                      </span>
                </td>
                
                <td><button class="btn" onclick="openFormEditar('<?php echo htmlspecialchars($obj->cod_bodega); ?>')"><i class="fa fa-home"></i> Editar</button></td>
                <td><button class="btn" onclick="confirmarEliminar('<?php echo $obj->cod_bodega; ?>')"><i class="fa fa-home" ></i> Eliminar</button></td>
            </tr>
            <?php
        }
    }
}
// Libera recursos
pg_free_result($result);

?>
</table>

<!-- funciones para mostrar formularios creados, como el editar o agregar -->
<script>
  function openFormEditar(codigo) {
    window.location.href = 'modificar.php?codigo=' + encodeURIComponent(codigo);
  }

  function confirmarEliminar(codigo) {
        if(confirm('¿Estás seguro de eliminar esta bodega?')) {
            window.location.href = 'eliminar.php?codigo=' + encodeURIComponent(codigo);
        }
  }

  function openFormAgregar() {
    document.getElementById("AgregarForm").style.display = "block";
  }

  function closeFormAgregar() {
    document.getElementById("AgregarForm").style.display = "none";
  }

  function validarFormulario() {
      var codigo = document.getElementById('codBodega').value;
      
      // Valida que el codigo no esté vacío
      if (codigo === "") {
          alert("El código de bodega es obligatorio");
          return false;
      }
      
      // Valida longitud máxima 5
      if (codigo.length > 5) {
          alert("El código de bodega no puede tener más de 5 caracteres");
          return false;
      }
      
      // Valida que sea alfanumérico (solo letras y números)
      var regex = /^[A-Za-z0-9]+$/;
      if (!regex.test(codigo)) {
          alert("El código de bodega solo puede contener letras y números (sin espacios ni caracteres especiales)");
          return false;
      }
      
      return true;
  }
</script>


<!-- Formulario popup para agregar una nueva Bodega -->
<div class="form-popup" id="AgregarForm">
  <form action="agregar.php" method="POST" class="form-container" onsubmit="return validarFormulario()">
    <h1>Agregar Bodega</h1>

    <label for="codBodega"><b>Código Bodega (Máx. 5 caracteres alfanuméricos)</b></label>
    <input type="text" maxlength="5" pattern="[A-Za-z0-9]+" placeholder="Código Bodega" title="Solo letras y números, máximo 5 caracteres"
        onkeyup="this.value = this.value.toUpperCase();" name="codBodega" required>

    <label for="nomBodega"><b>Nombre Bodega</b></label>
    <input type="text" placeholder="Nombre Bodega" name="nomBodega" required>

    <label for="direcBodega"><b>Dirección Bodega</b></label>
    <input type="text" placeholder="Dirección Bodega" name="direcBodega" required>

    <label for="dotaBodega"><b>Dotación Bodega</b></label>
    <input type="number" placeholder="Dotación Bodega" name="dotaBodega" required>

    <label for="estadoBodega"><b>Estado Bodega</b></label>
    <select name="estadoBodega" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f1f1f1;">
                <option value="true">Activo</option>
                <option value="false">Inactivo</option>
    </select>
<br> <!-- Espacio extra-->
    <label for="fechaBodega"><b>Fecha creación Bodega</b></label>
    <input type="datetime-local" placeholder="Fecha creación Bodega" name="fechaBodega" required>




    <div class="contenedor-botones">
        <button type="submit" class="btn" name="agregar-registro">Agregar</button>
        <button type="button" class="btn cancel" onclick="closeFormAgregar()">Cancelar</button>
    </div>
  </form>
</div>

</body>
</html>

