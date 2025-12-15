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
    
    $sql_select = "SELECT bedroom, bathroom, kitchen, living FROM $Table WHERE id = :id";
    
    try {
        $stmt_select = $pdo->prepare($sql_select);
        $stmt_select->bindParam(':id', $id_state, PDO::PARAM_INT);
        $stmt_select->execute();
        
        $stari_db = $stmt_select->fetch(PDO::FETCH_ASSOC);

    
        $becuri = [
            'bedroom' => (bool)$stari_db['bedroom'],
            'bathroom' => (bool)$stari_db['bathroom'],
            'kitchen' => (bool)$stari_db['kitchen'],
            'living' => (bool)$stari_db['living'],
        ];

        echo json_encode($becuri);

    } catch (PDOException $e) {
    
        echo json_encode(['error' => 'Couldnt read data']);
    }
}
?>