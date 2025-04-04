<?php
function loadEnv($path) {
  if (!file_exists($path)) return;

  $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
      if (str_starts_with(trim($line), '#')) continue;

      list($key, $value) = explode('=', $line, 2);
      putenv(trim($key) . '=' . trim($value));
  }
}

loadEnv(__DIR__ . '/../.env'); 

$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$dbuser = getenv('DB_USER');
$dbpass = getenv('DB_PASS');

try {
  $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $dbuser, $dbpass, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);
}
catch (PDOException $e) {
  // var_dump($e->getMessage());
  echo 'A problem occured with the database connection...';
  die();
}