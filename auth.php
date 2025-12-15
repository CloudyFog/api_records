<?php

include('db.php'); 


$Table = 'light_states';
$id_state = 1;

if(isset($_POST['actiune'])) {
    
    $sql = "";
    
    switch ($_POST['actiune']) {
        case 'Open':
    
            $sql = "UPDATE $Table SET bedroom = 1, bathroom = 1, kitchen = 1, living = 1 WHERE id = :id";
            break;
            
        case 'Close':
           
            $sql = "UPDATE $Table SET bedroom = 0, bathroom = 0, kitchen = 0, living = 0 WHERE id = :id";
            break;
            
       default:
    $coloane_valide = ['bedroom', 'bathroom', 'kitchen', 'living'];
    $actiune = $_POST['actiune'];
    
    
    if (in_array($actiune, $coloane_valide)) {
       
        $sql = "UPDATE $Table SET $actiune = 1 - $actiune WHERE id = :id";
    }
    
    break;
    }

    if (!empty($sql)) {
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id_state, PDO::PARAM_INT); 
            $stmt->execute();

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