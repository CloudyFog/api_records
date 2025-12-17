<?php

$host = "localhost";
$db = "lights";
$user = "root";
$pass = "";
$tabela = "light_states";

$bedroom_status = 0;
$bathroom_status = 0;
$living_status = 0;
$kitchen_status = 0;

try {
   
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    
    $sql = "INSERT INTO $tabela (bedroom, bathroom, living, kitchen) VALUES (?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    
    $stmt->execute([
        $bedroom_status,
        $bathroom_status,
        $living_status,
        $kitchen_status
    ]);


    echo "Starea luminii s-a bagat " . $pdo->lastInsertId();

} catch (PDOException $e) {
  
    die("Eroare DB: " . $e->getMessage());
}

?>