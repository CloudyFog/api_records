<?php

include('db.php');


$becuri = [
    "bedroom" => false,
    "bathroom" => false, 
    "kitchen" => false,
    "living" => false
];

switch ($_POST['actiune']){
    case 'Open':
        foreach($becuri as $key => $value){
            $becuri[$key] = true;
        }
        break;
    case 'Close':
        foreach($becuri as $key => $value){
            $becuri[$key] = false;
        }
        break;
    case 'bedroom':
        $becuri['bedroom'] = true;
        break;
    case 'bathroom':
        $becuri['bathroom'] = true;
        break;
    case 'kitchen':
        $becuri['kitchen'] = true;
        break;
    case 'living':
        $becuri['living'] = true;
        break;
    default:
        break;
}

 if(isset($_GET['statusuri'])){
    echo json_encode($becuri);
}
?>