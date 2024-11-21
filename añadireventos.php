<?php
$servername = "localhost";
$database = "eventos_deportivos";
$username = "root";
$password = "";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (($file = fopen("eventos.csv", "r"))) {
    while (($data = fgetcsv($file, 1000, ";"))) {
        $evento = $conn->real_escape_string($data[0]); 
        $tipoDeporte = $conn->real_escape_string($data[1]); 
        $fecha = $conn->real_escape_string($data[2]); 
        $hora = $conn->real_escape_string($data[3]); 
        $ubicacion = $conn->real_escape_string($data[4]); 
        $organizador = $conn->real_escape_string($data[5]); 

        $query = "SELECT id FROM organizadores WHERE nombre = '$organizador'";
        $result = $conn->query($query);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_organizador = $row['id'];

            $sql = "INSERT INTO eventos (nombre_evento, tipo_deporte, fecha, hora, ubicacion, id_organizador) VALUES ('$evento', '$tipoDeporte', '$fecha', '$hora', '$ubicacion', '$id_organizador')";
            if ($conn->query($sql) === TRUE) {
                echo "Lineas introducidas<br>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
            }
        } else {
            echo "Organizador: '$organizador' no fue encontrado. Saltando el evento: '$evento'.<br>";
        }
    }
    fclose($file);
} else {
    echo "Error no se ecnontró el archivo";
}

$conn->close();
?>