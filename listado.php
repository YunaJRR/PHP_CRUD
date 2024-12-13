<?php include 'php/procesar.php';?>
<!DOCTYPE html>
<html lang="es">
<head>

    <!--Trabajo hecho por Jaime Rocha Rodríguez, Adrián Sánchez Vázquez y Alejandro Alcántara Crugeiras-->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1 class="text-center">Filtrar eventos</h1>
    <div class="text-center">
                <?php
                    $currentSearch = isset($_GET['buscar-evento']) ? $_GET['buscar-evento'] : '';
                ?>
    <form class="d-flex d-inline-flex p-2" role="search" method="GET" action="listado.php">
        <input class="form-control me-2" id="buscar-evento" name="buscar-evento" placeholder="Nombre Evento" aria-label="Search" value="<?php echo htmlspecialchars($currentSearch); ?>">
        <button class="btn btn-outline-success" type="submit">Filtrar</button>
    </form>
</div>
    
    <h1 class="text-center">Listado de eventos</h1><br>
    <div class="text-center">
        <a href="añadir_evento.php" class="btn btn-lg btn-primary">Añadir nuevo evento</a><br><br>
    </div>
    <!--Tabla de eventos-->
    <table class="table table-hover table-bordered">
        <thead class="table-info">
            <tr>
                <?php
                    $currentAction = isset($_GET['action']) ? $_GET['action'] : '';
                    $currentOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';
                    function getSortOrder($action, $currentAction, $currentOrder) {
                        if ($action !== $currentAction) {
                            return 'asc'; 
                        }
                        return $currentOrder === 'asc' ? 'desc' : 'asc';
                    }
                ?>
                <th>
                    <a href="listado.php?action=sort_nombre_evento&order=<?php echo getSortOrder('sort_nombre_evento', $currentAction, $currentOrder); ?>&buscar-evento=<?php echo urlencode($currentSearch); ?>">
                        Nombre del evento
                        <?php if ($currentAction === 'sort_nombre_evento'){ 
                            echo $currentOrder === 'asc' ? ' ↑' : ' ↓'; 
                        } ?>
                    </a>
                </th>
                <th>
                    <a href="listado.php?action=sort_tipo_deporte&order=<?php echo getSortOrder('sort_tipo_deporte', $currentAction, $currentOrder); ?>&buscar-evento=<?php echo urlencode($currentSearch); ?>">
                        Tipo de deporte
                        <?php if ($currentAction === 'sort_tipo_deporte'){ 
                            echo $currentOrder === 'asc' ? ' ↑' : ' ↓'; 
                        } ?>
                    </a>
                </th>
                <th>
                    <a href="listado.php?action=sort_fecha&order=<?php echo getSortOrder('sort_fecha', $currentAction, $currentOrder); ?>&buscar-evento=<?php echo urlencode($currentSearch); ?>">
                        Fecha
                        <?php if ($currentAction === 'sort_fecha'){ 
                            echo $currentOrder === 'asc' ? ' ↑' : ' ↓'; 
                        } ?>
                    </a>
                </th>
                <th>
                    <a href="listado.php?action=sort_hora&order=<?php echo getSortOrder('sort_hora', $currentAction, $currentOrder); ?>&buscar-evento=<?php echo urlencode($currentSearch); ?>">
                        Hora
                        <?php if ($currentAction === 'sort_hora'){
                            echo $currentOrder === 'asc' ? ' ↑' : ' ↓';
                        } ?>
                    </a>
                </th>
                <th>
                    <a href="listado.php?action=sort_ubicacion&order=<?php echo getSortOrder('sort_ubicacion', $currentAction, $currentOrder); ?>&buscar-evento=<?php echo urlencode($currentSearch); ?>">
                        Ubicación
                        <?php if ($currentAction === 'sort_ubicacion'){
                            echo $currentOrder === 'asc' ? ' ↑' : ' ↓';
                        } ?>
                    </a>
                </th>
                <th>
                    <a href="listado.php?action=sort_organizador&order=<?php echo getSortOrder('sort_organizador', $currentAction, $currentOrder); ?>&buscar-evento=<?php echo urlencode($currentSearch); ?>">
                        Organizador
                        <?php if ($currentAction === 'sort_organizador'){
                            echo $currentOrder === 'asc' ? ' ↑' : ' ↓'; 
                        } ?>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if (!empty($resultsEventos)){ 
                    foreach ($resultsEventos as $row){ 
                        echo "<tr>
                            <td> {$row['nombre_evento']} </td>
                            <td> {$row['tipo_deporte']} </td>
                            <td> {$row['fecha']} </td>
                            <td> {$row['hora']} </td>
                            <td> {$row['ubicacion']} </td>
                            <td> {$row['nombre_organizador']} </td>
                            <td>
                                <a href='añadir_evento.php?id={$row['id']}' class='btn btn-primary'>Editar</a>
                                <a href='php/procesar.php?id={$row['id']}&action=borrar_evento' class='btn btn-danger' onclick='return confirmarEvento()'>Eliminar</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr>
                        <td colspan='7' class='text-center'>No hay eventos disponibles.</td>
                    </tr>";
                }
            ?>
        </tbody>
        
    </table>
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php if ($pagina <= 1) echo 'disabled'; ?>">
                            <a class="page-link" href="listado.php?page=<?php echo max(1, $pagina - 1); ?>&buscar-evento=<?php echo urlencode($currentSearch); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPaginas && $i <= 10; $i++){ ?>
                            <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                                <a class="page-link" href="listado.php?page=<?php echo $i; ?>&buscar-evento=<?php echo urlencode($currentSearch); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php }; ?>
                        <li class="page-item <?php if ($pagina >= $totalPaginas) echo 'disabled'; ?>">
                            <a class="page-link" href="listado.php?page=<?php echo min($totalPaginas, $pagina + 1); ?>&buscar-evento=<?php echo urlencode($currentSearch); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!--Listado de los organizadores-->
    <h1 class="text-center">Listado de organizadores</h1><br>
    <div class="text-center">
        <a href="añadir_organizador.php" class="btn btn-lg btn-primary">Añadir nuevo organizador</a><br><br>
    </div>
    <table class="table table-hover table-bordered">
        <thead class="table-info">
            <tr>

                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                
            </tr>
        </thead>
        <tbody>
            <?php 
                if (!empty($organizadores)){ 
                    foreach ($organizadores as $row){ 
                        echo "<tr>
                            <td> {$row['nombre']} </td>
                            <td> {$row['email']} </td>
                            <td> {$row['telefono']} </td>
                            <td>
                                <a href=\"php/procesar.php?id=" . $row['id'] . "&action=borrar_organizador\" class=\"btn btn-danger\" onclick=\"return confirmar()\">Eliminar</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr>
                        <td colspan='3' class='text-center'>No hay organizadores disponibles.</td>
                    </tr>";
                }
            ?>
        </tbody>
        
    </table>
    <!--Advertencias de eliminación de eventos y organizadores-->
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
        function confirmar() {
            return confirm("¿Estas seguro de que quieres hacer eso?");
        }

        function confirmarEvento() {
            return confirm("¿Estas seguro de que quieres eliminar este evento?");
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>