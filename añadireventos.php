<?php
$servername = "localhost";
$database = "eventos_deportivos";
$username = "root";
$password = "";
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$totalRegistros = 0;
$registrosInsertados = 0;
$registrosRechazados = 0;
$errores = [];

if (($file = fopen("eventos.csv", "r"))) {
    while (($data = fgetcsv($file, 1000, ";"))) {
        $totalRegistros++;

        $evento = $conn->real_escape_string(trim($data[0]));
        $tipoDeporte = $conn->real_escape_string(trim($data[1]));
        $fecha = trim($data[2]);
        $hora = trim($data[3]);
        $ubicacion = $conn->real_escape_string(trim($data[4]));
        $organizador = $conn->real_escape_string(trim($data[5]));

        if (empty($evento) || empty($tipoDeporte) || empty($fecha) || empty($hora) || empty($ubicacion) || empty($organizador)) {
            $errores[] = "Faltan campos obligatorios en el evento: '$evento'.";
            $registrosRechazados++;
            continue; 
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $errores[] = "Fecha inválida para el evento: '$evento'. Debe estar en formato YYYY-MM-DD.";
            $registrosRechazados++;
            continue;
        }

        if (!preg_match('/^\d{2}:\d{2}$/', $hora)) {
            $errores[] = "Hora inválida para el evento: '$evento'. Debe estar en formato HH:MM.";
            $registrosRechazados++;
            continue;
        }

        $query = "SELECT id FROM organizadores WHERE nombre = '$organizador'";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_organizador = $row['id'];
            $sql = "INSERT INTO eventos (nombre_evento, tipo_deporte, fecha, hora, ubicacion, id_organizador) VALUES ('$evento', '$tipoDeporte', '$fecha', '$hora', '$ubicacion', '$id_organizador')";
            if ($conn->query($sql) === TRUE) {
                $registrosInsertados++;
            } else {
                $errores[] = "Error al insertar el evento: '$evento'. " . $conn->error;
                $registrosRechazados++;
            }
        } else {
            $errores[] = "Organizador: '$organizador' no fue encontrado. Saltando el evento: '$evento'.";
            $registrosRechazados++;
        }
    }
    fclose($file);
} else {
    echo "Error: no se encontró el archivo.";
}

$conn->close();

echo "Resumen de carga ";
echo "Total de registros procesados: $totalRegistros ";
echo "Registros insertados correctamente: $registrosInsertados ";
echo "Registros rechazados: $registrosRechazados ";

if ($registrosRechazados > 0) {
    echo "Errores: ";
    foreach ($errores as $error) {
        echo "$error ";
    }
}
?>