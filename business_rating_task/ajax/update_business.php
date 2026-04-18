<?php
include '../config/db.php';

$conn->query("UPDATE businesses SET
name='{$_POST['name']}',
address='{$_POST['address']}',
phone='{$_POST['phone']}',
email='{$_POST['email']}'
WHERE id={$_POST['id']}");