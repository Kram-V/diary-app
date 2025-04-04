<?php
  require __DIR__ . '/inc/db.inc.php';

  date_default_timezone_set('Asia/Manila');

  $diaryId = null; 
  $diary = null;

  if(!empty($_GET['id'])) {
    $diaryId = $_GET['id'];
  }

  if (!empty($diaryId)) {
    $query1 = 'SELECT * FROM entries WHERE id = :id';
    $stmt1 = $pdo->prepare($query1);
    $stmt1->bindValue('id', $diaryId, PDO::PARAM_INT);
    $stmt1->execute();

    $diary = $stmt1->fetch(PDO::FETCH_ASSOC);

    if (!empty($diary['image'])) {
      unlink(__DIR__ . '/uploads/' . $diary['image']);
    }

    $query2 = 'DELETE FROM entries WHERE id = :id';

    $stmt2 = $pdo->prepare($query2);
    $stmt2->bindValue('id', $diaryId, PDO::PARAM_INT);

    if ($stmt2->execute()) {
      header("Location: index.php");
    } else {
        echo "Error Deleting Record";
    }
  } else {
    echo "NO ID PROVIDED";
  }
?>