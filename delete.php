<?php
  require __DIR__ . '/inc/db.inc.php';

  date_default_timezone_set('Asia/Manila');

  $diaryId = null; 
  $diary = null;

  if(!empty($_GET['id'])) {
    $diaryId = $_GET['id'];
  }

  if (!empty($diaryId)) {
    $query = 'DELETE FROM entries WHERE id = :id';

    $stmt = $pdo->prepare($query);
    $stmt->bindValue('id', $diaryId, PDO::PARAM_INT);

    if ($stmt->execute()) {
      header("Location: index.php");
    } else {
        echo "Error Deleting Record";
    }
  } else {
    echo "NO ID PROVIDED";
  }
?>