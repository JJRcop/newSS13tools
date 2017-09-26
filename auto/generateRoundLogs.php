<?php
require __DIR__ . '/../config.php';
header('Content-type: application/json');
$return['message'] = null;
$round = filter_input(INPUT_GET, 'round', FILTER_VALIDATE_INT);

$logs = (new gameLogs)->generateRoundLogs($round);

echo json_encode($logs);