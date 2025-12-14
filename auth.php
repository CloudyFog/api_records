<?php
$becuri = [
    "dormitor" => false,
    "baie"     => false, 
    "bucatarie" => false,
    "living"   => false
];
   
header('Content-Type: application/json');
if(isset($_GET['users'])){
    echo "User aparut"; 
}
if(isset($_POST['dormitor'])){
$becuri['dormitor'] = true;
}
if(isset($_POST['baie'])){
$becuri['baie'] = true;
}
if(isset($_POST['bucatarie'])){
$becuri['bucatarie'] = true;
}
if(isset($_POST['living'])){
$becuri['living'] = true;
}
echo json_encode($becuri);
?>