<?php
include '../config/db.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $res = $conn->query("SELECT * FROM businesses WHERE id=$id");
    echo json_encode($res->fetch_assoc());
    exit;
}

$res = $conn->query("SELECT * FROM businesses");
 $sr = 1;
while($row = $res->fetch_assoc()){
    $id = $row['id'];

    $avg = $conn->query("SELECT AVG(rating) as avg FROM ratings WHERE business_id=$id")->fetch_assoc()['avg'];
    $avg = $avg ? round($avg,1) : 0;

    echo "<tr>
    <td>{$sr}</td>
    <td>{$row['name']}</td>
    <td>{$row['address']}</td>
    <td>{$row['phone']}</td>
    <td>{$row['email']}</td>
    <td>
        <div class='avg-rating' data-score='$avg' onclick='openRating($id)'></div>
    </td>
    <td>
        <button onclick='editBusiness($id)' class='btn btn-sm btn-info'>Edit</button>
        <button onclick='deleteBusiness($id)' class='btn btn-sm btn-danger'>Delete</button>
    </td>
    </tr>";
    $sr++;
}
?>