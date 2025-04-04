<?php
  require __DIR__ . '/inc/db.inc.php';

  if (!empty($_POST)) {
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $date = isset($_POST['date']) ? $_POST['date'] : '';
    $imageName = null;

    if ($_FILES['image']['error'] === 0 && $_FILES['image']['size'] !== 0) {
      $nameWithoutExtension = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
      $name = preg_replace('/[^a-zA-Z0-9]/', '', $nameWithoutExtension);
  
      $originalImage = $_FILES['image']['tmp_name'];
      $imageName =  $name . '-' . time() . '.jpg';
      $imgPath = __DIR__ . '/uploads/' . $imageName;
  
      [$width, $height] = getimagesize($originalImage);
  
      $maxDim = 400;
      $scaleFactor = $maxDim / max($width, $height);
  
      $newWidth = (int) ($width * $scaleFactor);
      $newHeight = (int) ($height * $scaleFactor);
  
      // Detect the image type
      $imageType = mime_content_type($originalImage);
  
      switch ($imageType) {
          case 'image/jpeg':
              $im = imagecreatefromjpeg($originalImage);
              break;
          case 'image/png':
              $im = imagecreatefrompng($originalImage);
              break;
          case 'image/gif':
              $im = imagecreatefromgif($originalImage);
              break;
          case 'image/webp':
              $im = imagecreatefromwebp($originalImage);
              break;
          default:
              die("Unsupported image type!");
      }

  
      $newImg = imagecreatetruecolor($newWidth, $newHeight);
      imagecopyresampled($newImg, $im, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
  
      imagejpeg($newImg, $imgPath); // Always save as JPG
      imagedestroy($im);
      imagedestroy($newImg);
    }
  
    $query = "INSERT INTO `entries` (`title`, `description`, `date`, `image`) VALUES (:title, :description, :date, :image)";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue('title', $title);
    $stmt->bindValue('description', $description);
    $stmt->bindValue('date', $date);
    $stmt->bindValue('image', $imageName);

    if ($stmt->execute()) {
      header("Location: index.php");
    } else {
      echo "Error Creating Diary";
    }
  }
?>

<?php require __DIR__ . '/views/header.view.php' ?>
  <div class="container">
      <h1 class="main-heading">New Entry</h1>

      <form method="POST" enctype="multipart/form-data">
          <div class="form-group">
              <label class="from-group__label" for="title">Title:</label>
              <input class="from-group__input" type="text" id="title" name="title" required />
          </div>
          <div class="form-group">
              <label class="from-group__label" for="date">Date:</label>
              <input class="from-group__input" type="date" id="date" name="date" required />
          </div>
          <div class="form-group">
              <label class="from-group__label" for="image">Image:</label>
              <input class="from-group__input" type="file" id="image" name="image" />
          </div>
          <div class="form-group">
              <label class="from-group__label" for="description">Description:</label>
              <textarea class="from-group__input" id="description" name="description" rows="10" minlength="420" maxlength="1000" required></textarea>
          </div>
  
          <div class="form-submit">
              <button style="cursor: pointer;" class="button" type="submit">
                  <svg class="button__icon" viewBox="0 0 34.7163912799 33.4350009649">
                      <g style="fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2px;">
                          <polygon points="20.6844359446 32.4350009649 33.7163912799 1 1 10.3610302393 15.1899978903 17.5208901631 20.6844359446 32.4350009649"/>
                          <line x1="33.7163912799" y1="1" x2="15.1899978903" y2="17.5208901631"/>
                      </g>
                  </svg>
                  Save
              </button>
          </div>
      </form>
  </div>
<?php require __DIR__ . '/views/footer.view.php' ?>
