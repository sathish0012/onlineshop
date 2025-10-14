<?php
$conn = new mysqli('localhost','root','','onlineshopdb');
if(!$conn){
    echo "Error!: {$conn->connect_error}";
}
?>