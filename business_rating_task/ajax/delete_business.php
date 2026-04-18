<?php
include '../config/db.php';

$conn->query("DELETE FROM businesses WHERE id={$_POST['id']}");