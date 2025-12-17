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
            $sql = "UPDATE $Table SET bedroom = 1, bathroom = 1, kitchen = 1, living = 1 WHERE id = :id";
            break;
            
        case 'Close':
   
            $sql = "UPDATE $Table SET bedroom = 0, bathroom = 0, kitchen = 0, living = 0 WHERE id = :id";
            break;
            
        case 'bedroom':
        case 'bathroom':
        case 'kitchen':
        case 'living':
            $room_name_to_log = $_POST['actiune'];
            $is_individual_toggle = true; 
            $sql = "UPDATE $Table SET {$room_name_to_log} = 1 - {$room_name_to_log} WHERE id = :id";
            break;
            
        default:
            break;
    }

    if (!empty($sql)) {
        try {

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id_state, PDO::PARAM_INT); 
            $stmt->execute();
            if ($is_individual_toggle) {
                
              
                $sql_select_new = "SELECT {$room_name_to_log} FROM $Table WHERE id = :id";
                $stmt_select = $pdo->prepare($sql_select_new);
                $stmt_select->bindParam(':id', $id_state, PDO::PARAM_INT);
                $stmt_select->execute();
                $new_status = $stmt_select->fetchColumn(); 
                $sql_log = "INSERT INTO $Table_Log (room_name, status, time) 
                            VALUES (:room, :status, NOW())";
                
                $stmt_log = $pdo->prepare($sql_log);
                $stmt_log->bindParam(':room', $room_name_to_log);
                $stmt_log->bindParam(':status', $new_status, PDO::PARAM_INT);
                $stmt_log->execute();
            }

            
        } catch (PDOException $e) {
        }
    }
}

if (isset($_GET['statusuri'])) {
    $camere = ['bedroom', 'bathroom', 'kitchen', 'living'];
    $durate_totale = [];
    $sql_select = "SELECT bedroom, bathroom, kitchen, living FROM $Table WHERE id = :id";
    try {
        $stmt_select = $pdo->prepare($sql_select);
        $stmt_select->bindParam(':id', $id_state, PDO::PARAM_INT);
        $stmt_select->execute();
        $stari_db = $stmt_select->fetch(PDO::FETCH_ASSOC);
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
                WHERE l1.room_name = :room AND l1.status = 1;
            ";
            
            $stmt_duration = $pdo->prepare($sql_duration);
            $stmt_duration->bindParam(':room', $camera);
            $stmt_duration->execute();
            $result = $stmt_duration->fetch(PDO::FETCH_ASSOC);
            $total_seconds = (int)$result['total_seconds_on'];
            $hours = floor($total_seconds / 3600);
            $minutes = floor(($total_seconds % 3600) / 60);
            $seconds = $total_seconds % 60;
            $rezultat_final[$camera] = [
                'status' => (bool)$stari_db[$camera],
                'total_time_on' => sprintf("%02dh %02dm %02ds", $hours, $minutes, $seconds),
                'total_seconds' => $total_seconds
            ];
        }
        
        echo json_encode($rezultat_final);

    } catch (PDOException $e) {
    }
}
?>