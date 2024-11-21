<?php include 'php/procesar.php';?>
<!DOCTYPE html>
<html lang="es">
<head>

    <!--Trabajo hecho por Jaime Rocha Rodríguez, Adrián Sánchez Vázquez y Alejandro Alcántara Crugeiras-->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" src="css/styles.css">
    <title>Formulario</title>
</head>
<body class="m-0 row justify-content-center">
    <div class="col-auto p-5 text-center">
        <div class="card text-center" style="width: 30rem;">
            <h1 class="p-2">Registro</h1>
            <?php
                if (isset($_SESSION['errorMessages']) && count($_SESSION['errorMessages']) > 0) {
                    echo '<div class="alert alert-danger">';
                    foreach ($_SESSION['errorMessages'] as $message) {
                        echo '<p>' . htmlspecialchars($message) . '</p>';
                    }
                    echo '</div>';
                    unset($_SESSION['errorMessages']);
                }
            ?>
            <form action="php/procesar.php" method="post">
                <!--Valor hidden para diferenciar formularios-->
                <input type="hidden" name="hidden-organizador" value="hidden-organizador">
                <label for="nombre" class="control-label col-sm-4 text-right">Nombre:</label><br>
                <input type="text" id="nombre" name="nombre" ><br><br>

                <label for="telefono" class="control-label col-sm-4 text-right">Teléfono:</label><br>
                <input type="text" id="telefono" name="telefono" ><br><br>

                <label for="email" class="control-label col-sm-4 text-right">Email:</label><br>
                <input type="email" id="email" name="email" ><br><br>
                
                <input class="btn btn-primary btn-lg" type="submit" value="Registrar">
            </form>
            <a href="listado.php" class="text-decoration-none text-dark p-2">Ir al listado</a>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</html>