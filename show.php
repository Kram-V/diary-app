<?php
  require __DIR__ . '/inc/db.inc.php';
  require __DIR__ . '/inc/functions.inc.php';

  date_default_timezone_set('Asia/Manila');

  $diaryId = null; 
  $diary = null;

  if(!empty($_GET['id'])) {
    $diaryId = $_GET['id'];
  } else {
    die('ID NOT PROVIDED');
  }

  if (!empty($diaryId)) {
    $query = 'SELECT * FROM `entries` WHERE id = :id';

    $stmt = $pdo->prepare($query);
    $stmt->bindValue('id', $diaryId, PDO::PARAM_INT);
    $stmt->execute();

    $diary = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($diary)) {
      die('NO DIARY SELECTED FROM DATABASE');
    }
  }
?>

<?php require __DIR__ . '/views/header.view.php' ?>
  <div class="container">
    <div class="diary-container card">
      <?php if(!empty($diary['image'])): ?>
        <img src="uploads/<?php echo e($diary['image']); ?>" alt="<?php echo e($diary['title']); ?>">
      <?php else: ?>
        <img style="height: 300px; width: 400px;" src="images/no-image.jpg" alt="<?php echo e($diary['title']); ?>">
      <?php endif; ?>

      <div class="content-container">
        <h1><?php echo e($diary['title']); ?></h1>
        <p><?php echo e($diary['description']); ?></p>
      </div>
    </div>


    <a href="index.php">&#x2190; BACK TO ENTRIES</a>
  </div>
<?php require __DIR__ . '/views/footer.view.php' ?>