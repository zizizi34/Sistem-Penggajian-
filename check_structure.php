<?php
$conn = new mysqli('localhost', 'root', '', 'sistem-penggajian');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Kolom di tabel 'role':\n";
$result = $conn->query('SHOW COLUMNS FROM role');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

echo "\nKolom di tabel 'permission':\n";
$result = $conn->query('SHOW COLUMNS FROM permission');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Tabel permission tidak ada\n";
}

echo "\nKolom di tabel 'role_permission':\n";
$result = $conn->query('SHOW COLUMNS FROM role_permission');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Tabel role_permission tidak ada\n";
}

$conn->close();
?>
