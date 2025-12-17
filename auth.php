<?php
include('db.php'); 

$Table = 'light_states'; 
$Table_Log = 'light_log';
$id_state = 1;

if(isset($_POST['actiune'])) {
    $sql = "";
    $room_name_to_log = null; 
    $is_individual_toggle = false; 

    switch ($_POST['actiune']) {
        case 'Open':
            $sql = "UPDATE $Table SET bedroom = 1, bathroom = 1, kitchen = 1, living = 1 WHERE id = ?";
            break;
        case 'Close':
            $sql = "UPDATE $Table SET bedroom = 0, bathroom = 0, kitchen = 0, living = 0 WHERE id = ?";
            break;
        case 'bedroom':
        case 'bathroom':
        case 'kitchen':
        case 'living':
            $room_name_to_log = $_POST['actiune'];
            $is_individual_toggle = true; 
            // ATENȚIE: Numele coloanei se pune direct în string (cu validare switch), nu ca parametru ?
            $sql = "UPDATE $Table SET {$room_name_to_log} = 1 - {$room_name_to_log} WHERE id = ?";
            break;
    }

    if (!empty($sql)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_state); // "i" înseamnă integer
        $stmt->execute();

        if ($is_individual_toggle) {
            // Luăm noul status
            $sql_select_new = "SELECT {$room_name_to_log} FROM $Table WHERE id = ?";
            $stmt_select = $conn->prepare($sql_select_new);
            $stmt_select->bind_param("i", $id_state);
            $stmt_select->execute();
            $result_new = $stmt_select->get_result();
            $new_status = $result_new->fetch_row()[0];

            // Inserăm în log
            $sql_log = "INSERT INTO $Table_Log (room_name, status, time) VALUES (?, ?, NOW())";
            $stmt_log = $conn->prepare($sql_log);
            $stmt_log->bind_param("si", $room_name_to_log, $new_status); // "s" pt string, "i" pt integer
            $stmt_log->execute();
        }
    }
}

if (isset($_GET['statusuri'])) {
    $camere = ['bedroom', 'bathroom', 'kitchen', 'living'];
    
    $sql_select = "SELECT bedroom, bathroom, kitchen, living FROM $Table WHERE id = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $id_state);
    $stmt_select->execute();
    $stari_db = $stmt_select->get_result()->fetch_assoc();

    $rezultat_final = [];
    foreach ($camere as $camera) {
        $sql_duration = "
            SELECT 
                SUM(TIMESTAMPDIFF(SECOND, l1.time, 
                    (SELECT MIN(l2.time) FROM $Table_Log l2 
                     WHERE l2.room_name = l1.room_name 
                       AND l2.status = 0 
                       AND l2.time > l1.time
                    )
                )) AS total_seconds_on
            FROM $Table_Log l1
            WHERE l1.room_name = ? AND l1.status = 1;
        ";
        
        $stmt_duration = $conn->prepare($sql_duration);
        $stmt_duration->bind_param("s", $camera);
        $stmt_duration->execute();
        $res = $stmt_duration->get_result()->fetch_assoc();
        
        $total_seconds = (int)$res['total_seconds_on'];
        $hours = floor($total_seconds / 3600);
        $minutes = floor(($total_seconds % 3600) / 60);
        $seconds = $total_seconds % 60;

        $rezultat_final[$camera] = [
            'status' => (bool)$stari_db[$camera],
            'total_time_on' => sprintf("%02dh %02dm %02ds", $hours, $minutes, $seconds),
            'total_seconds' => $total_seconds
        ];
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($rezultat_final);
}
?>