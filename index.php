<!-- Llama a la base de datos-->
<?php require_once("bodega/conexion.php"); ?> 

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/style.css">
        <title>App Bodegas</title>
    </head>
<body>
    <header>
        <h2>App Bodegas</h2>
    </header>
    <section>
        <nav>
            <ul>
            <li><a href="bodega/bodega.php" style="font-size: 20px;">Bodegas</a></li>
            <li><a href="encargados/lista_encargados.php" style="font-size: 20px;">Encargados</a></li>
            </ul>
        </nav>
        
        <article>
            <h1>Nota: </h1>
            <p>
                Módulo que permite al usuario agregar nuevas bodegas, actualizar los datos
                creados, eliminar bodegas y entregar un listado de las bodegas existentes.
            </p>
        </article>
    </section>
<!--pie de página -->
    <footer>
        <p>@derechos reservados.</p>
    </footer>
</body>
</html>