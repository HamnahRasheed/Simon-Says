<?php
$conn = mysqli_connect("localhost", "root", "", "gameDB");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $gameId = $_POST['gameId'];
    $score = $_POST['score'];

    $stmt = $conn->prepare("INSERT INTO scores (game_id, score) VALUES (?, ?)");
    $stmt->bind_param("si", $gameId, $score);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>
