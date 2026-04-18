<?php
include '../config/db.php';

$conn->query("INSERT INTO businesses(name,address,phone,email)
VALUES('{$_POST['name']}','{$_POST['address']}','{$_POST['phone']}','{$_POST['email']}')");