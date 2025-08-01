<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json');

// Decode JSON
$data = json_decode(file_get_contents("php://input"), true);

// Validate and extract
$user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;
$moves = isset($data['moves']) ? (int)$data['moves'] : 0;
$time = isset($data['time']) ? (int)$data['time'] : 0;
$status = ($data['status'] === 'success') ? 'success' : 'fail';
$background_id = isset($data['background_id']) ? (int)$data['background_id'] : 0;
$size = isset($data['size']) ? (int)$data['size'] : 0;

// Ensure all required data is present
if (!$user_id || !$background_id || !$size) {
    echo json_encode(['success' => false, 'error' => 'Missing required data.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO game_stats 
        (user_id, puzzle_size, time_taken_seconds, moves_count, background_image_id, win_status, game_date) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$user_id, $size, $time, $moves, $background_id, $status]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Insert failed.']);
}
