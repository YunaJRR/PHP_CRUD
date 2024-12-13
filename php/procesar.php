<?php

// Trabajo hecho por Jaime Rocha Rodríguez, Adrián Sánchez Vázquez y Alejandro Alcántara Crugeiras

$servername = "localhost";
$database = "eventos_deportivos";
$username = "root";
$password = "";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Se inicia una sesión para almacenar los mensajes de error
session_start(); 
ob_start(); 


$errorMessages = [];

// Añadir evento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty( $_POST["hidden-evento"] ) && !isset($_GET['id'])) {
    $nombreEvento = $_POST['nombre_evento'];
    $tipoDeporte = $_POST['tipo_deporte'];
    $ubicacion = $_POST['ubicacion'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $organizador = $_POST['organizador'];
    $eventoValido = true;

    if (empty($nombreEvento)) {
        $errorMessages[] = 'El campo "Nombre del evento" es obligatorio.';
        $eventoValido = false;
    }
    if (empty($tipoDeporte)) {
        $errorMessages[] = 'El campo "Tipo de deporte" es obligatorio.';
        $eventoValido = false;
    }
    if (empty($ubicacion)) {
        $errorMessages[] = 'El campo "ubicacion" es obligatorio.';
        $eventoValido = false;
    }
    if (empty($fecha)) {
        $errorMessages[] = 'El campo "Fecha" es obligatorio.';
        $eventoValido = false;
    }
    if (empty($hora)) {
        $errorMessages[] = 'El campo "Hora" es obligatorio.';
        $eventoValido = false;
    }

    if ($eventoValido) {
        añadirEvento();
    } else {

        $_SESSION['errorMessages'] = $errorMessages;

        header('Location: ../añadir_evento.php');
        exit;
    }


    
}


// Añadir organizador
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST["hidden-organizador"]) && !isset($_GET['id'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $organizadorValido = true;

    if (empty($nombre)) {
        $errorMessages[] = 'El campo "nombre" es obligatorio.';
        $organizadorValido = false;
    }
    if (empty($email)) {
        $errorMessages[] = 'El campo "email" es obligatorio.';
        $organizadorValido = false;
    }
    if (empty($telefono)) {
        $errorMessages[] = 'El campo "telefono" es obligatorio.';
        $organizadorValido = false;
    }

    if ($organizadorValido) {
        añadirOrganizador();
    } else {
        // Store error messages in session
        $_SESSION['errorMessages'] = $errorMessages;
        // Redirect back to the form
        header('Location: ../añadir_organizador.php');
        exit;
    }
}


// Declaración variables para la paginación
$limiteEventos = 3; 
$pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$offset = ($pagina - 1) * $limiteEventos; 
$totalEventos = 0; // Initialize total events variable

// Buscar evento
$searchTerm = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['buscar-evento'])) {
    $searchTerm = $_POST['buscar-evento'];
    
    // Count the results based on the search
    $sqlCount = "SELECT COUNT(*) as total FROM eventos WHERE nombre_evento LIKE '%" . $conn->real_escape_string($searchTerm) . "%'";
    $totalResultados = $conn->query($sqlCount);
    $totalFilas = $totalResultados->fetch_assoc();
    $totalEventos = $totalFilas['total'];
    $totalPaginas = ceil($totalEventos / $limiteEventos);
    
    // Fetch the filtered events with pagination
    $resultsEventos = obtenerListadoEventos(true, $searchTerm, $offset, $limiteEventos);
} else {
    // Count all events for pagination
    $totalSql = "SELECT COUNT(*) as total FROM eventos";
    $totalResultados = $conn->query($totalSql);
    $totalFilas = $totalResultados->fetch_assoc();
    $totalEventos = $totalFilas['total'];
    $totalPaginas = ceil($totalEventos / $limiteEventos);
    
    // Fetch all events with pagination
    $resultsEventos = obtenerListadoEventos(false, '', $offset, $limiteEventos);
}


// Borrar evento
if (isset($_GET['action']) && $_GET['action'] == 'borrar_evento') {
    borrarEvento();
}

// Borrar organizador
if (isset($_GET['action']) && $_GET['action'] == 'borrar_organizador') {
    borrarOrganizador();
}

// Editar evento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['id'])) {
    actualizarEvento();
}

function añadirOrganizador() {
    global $conn;

    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];


    $sql = "INSERT INTO organizadores (nombre, email, telefono) VALUES ('$nombre', '$email', '$telefono')";
    if ($conn->query($sql) === TRUE) {
        header('Location: ../listado.php');
    } else {
        echo "Error en el registro: " . $conn->error;
    }
}
function añadirEvento() {
    global $conn;

    $nombre_evento = $_POST['nombre_evento'];
    $tipo_deporte = $_POST['tipo_deporte'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $ubicacion = $_POST['ubicacion'];
    $organizador = $_POST['organizador'];

    // Obtener id de organizador
    $query = "SELECT id FROM organizadores WHERE nombre = '$organizador'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();
        $id_organizador = $row['id'];

        // Insertar evento
        $sql = "INSERT INTO eventos (nombre_evento, tipo_deporte, fecha, hora, ubicacion, id_organizador) VALUES ('$nombre_evento', '$tipo_deporte', '$fecha', '$hora', '$ubicacion', '$id_organizador')";
        
        if ($conn->query($sql) === TRUE) {
            header('Location: ../listado.php');
        } else {
            echo "Error en el registro: " . $conn->error;
        }
    } else {
        echo "Error: organizador no encontrado.";
    }
}

function actualizarEvento() {
    global $conn;

    $id = $_GET['id'];
    $nombre_evento = $_POST['nombre_evento'];
    $tipo_deporte = $_POST['tipo_deporte'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $ubicacion = $_POST['ubicacion'];
    $organizador = $_POST['organizador'];

    // Obtener id de organizador
    $query = "SELECT id FROM organizadores WHERE nombre = '$organizador'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();
        $id_organizador = $row['id'];

        // Insertar evento
        $sql = "UPDATE eventos SET nombre_evento='$nombre_evento', tipo_deporte='$tipo_deporte', fecha='$fecha', hora='$hora', ubicacion='$ubicacion', id_organizador='$id_organizador'  WHERE id=$id";
        
        if ($conn->query($sql) === TRUE) {
            header('Location: ../listado.php');
        } else {
            echo "Error en el registro: " . $conn->error;
        }
    } else {
        echo "Error: organizador no encontrado.";
    }

    global $conn;

    $id = $_GET['id'];
    $nombre_evento = $_POST['nombre_evento'];
    $tipo_deporte = $_POST['tipo_deporte'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $ubicacion = $_POST['ubicacion'];
    $organizador = $_POST['organizador'];

    // Obtener id de organizador
    $query = "SELECT id FROM organizadores WHERE nombre = '$organizador'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();
        $id_organizador = $row['id'];


        $sql = "UPDATE evento SET nombre_evento='$nombre_evento', tipo_deporte='$tipo_deporte', fecha='$fecha', hora='$hora', ubicacion='$ubicacion', id_organizador='$id_organizador'  WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            header('Location: ../listado.php');
        } else {
            echo "Error in the update: " . $conn->error;
        }
    } else {
        echo "Error: organizador no encontrado.";
    }
}

function obtenerOrganizadores() {
    global $conn;

    $sql = "SELECT * FROM organizadores"; 

    
    
    $result = $conn->query($sql);
    if (!$result) {
        echo "Error en la consulta: " . $conn->error;
        return []; 
    }
    $organizadores = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $organizadores[] = $row;
        }
    }
    return $organizadores;
}

$organizadores = obtenerOrganizadores();
$currentlyFiltering = false;



function obtenerListadoEventos($listaOrganizadores) { 
    global $conn;
    global $limiteEventos;
    global $offset;
    $sql = "SELECT eventos.*, organizadores.nombre AS nombre_organizador  
            FROM eventos  
            JOIN organizadores ON eventos.id_organizador = organizadores.id"; 
    $busqueda_evento = isset($_GET['buscar-evento']) ? $_GET['buscar-evento'] : ''; 
    if (isset($_GET['order']) && $_GET['order'] === 'desc') { 
        $isAsc = false;  
    } else { 
        $isAsc = true;  
    } 
   if (isset($_GET['id']) && $listaOrganizadores) {
        $id = $_GET['id'];
        $sql .= " WHERE organizadores.id = $id";
    } else if (isset($_POST['buscar-evento']) && !empty($_POST['buscar-evento'])) {
        $busqueda_evento = ($_POST['buscar-evento']); 
        $sql = "SELECT eventos.*, organizadores.nombre AS nombre_organizador 
            FROM eventos 
            JOIN organizadores ON eventos.id_organizador = organizadores.id 
            WHERE eventos.nombre_evento LIKE '%$busqueda_evento%'";
            echo 'filtro';
    }
    if (!empty($busqueda_evento)) { 
        $sql .= " WHERE eventos.nombre_evento LIKE '%$busqueda_evento%'"; 
    } 
    if (isset($_GET['action'])) { 
        switch ($_GET['action']) { 
            case "sort_nombre_evento": 
                $sql .= " ORDER BY nombre_evento " . ($isAsc ? "asc" : "desc");  
                break; 
            case "sort_tipo_deporte": 
                $sql .= " ORDER BY tipo_deporte " . ($isAsc ? "asc" : "desc");  
                break; 
            case "sort_fecha": 
                $sql .= " ORDER BY fecha " . ($isAsc ? "asc" : "desc");  
                break; 
            case "sort_hora": 
                $sql .= " ORDER BY hora " . ($isAsc ? "asc" : "desc");  
                break; 
            case "sort_ubicacion": 
                $sql .= " ORDER BY ubicacion " . ($isAsc ? "asc" : "desc");  
                break; 
            case "sort_organizador": 
                $sql .= " ORDER BY organizadores.nombre " . ($isAsc ? "asc" : "desc");  
                break; 
        } 
    } 
    
    $sql .= " LIMIT $limiteEventos OFFSET $offset";
    $result = $conn->query($sql); 
    $listadoEventos = array(); 

    if ($result->num_rows > 0) { 
        while ($row = $result->fetch_assoc()) { 
            $listadoEventos[] = $row; 
        } 
    } 
    return $listadoEventos; 
} 


function borrarEvento(){
    global $conn;
    $id = $_GET['id'];

    $sql = "DELETE FROM eventos WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header('Location: ../listado.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

function borrarOrganizador(){
    global $conn;
    $eventos = obtenerListadoEventos(true);
    $id = $_GET['id'];
    if (sinEventos($id, $eventos)==0){
        echo"<script>alert('No se puede eliminar a un organizador con eventos a su nombre'); window.location.href='../listado.php';</script>";
    }else{
        $sql = "DELETE FROM organizadores WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            header('Location: ../listado.php');
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}


function sinEventos($id, $resultsEventos) {
    if (!empty($resultsEventos)){  
        
        foreach ($resultsEventos as $row){ 
            if ($id === $row['id_organizador']){  
                return 0;  
            }
        } 
    } 
    return 1; 
}



ob_end_flush();
?>
