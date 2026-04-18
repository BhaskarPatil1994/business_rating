<?php
include '../config/db.php';

if(!isset($_GET['business_id'])){
    echo json_encode(["counts"=>[], "total"=>0]);
    exit;
}

$business_id = intval($_GET['business_id']);

$totalRes = $conn->query("SELECT COUNT(*) as total FROM ratings WHERE business_id=$business_id");
$total = $totalRes->fetch_assoc()['total'];

$res = $conn->query("
    SELECT FLOOR(rating) as rating, COUNT(*) as total 
    FROM ratings 
    WHERE business_id = $business_id
    GROUP BY FLOOR(rating)
");

$data = [];

while($row = $res->fetch_assoc()){
    $data[$row['rating']] = $row['total'];
}

for($i=1; $i<=5; $i++){
    if(!isset($data[$i])){
        $data[$i] = 0;
    }
}

echo json_encode([
    "counts"=>$data,
    "total"=>$total
]);