<?php
include '../config/db.php';

$b = $_POST['business_id'];
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$rating = $_POST['rating'];

$check = $conn->query("SELECT id FROM ratings 
WHERE business_id='$b' AND (email='$email' OR phone='$phone')");

if($check->num_rows > 0){
    $conn->query("UPDATE ratings SET rating='$rating'
    WHERE business_id='$b' AND (email='$email' OR phone='$phone')");
}else{
    $conn->query("INSERT INTO ratings(business_id,name,email,phone,rating)
    VALUES('$b','$name','$email','$phone','$rating')");
}