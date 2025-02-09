<?php
  require __DIR__ . '/inc/db.inc.php';
  require __DIR__ . '/inc/functions.inc.php';

  date_default_timezone_set('Asia/Manila');

  $search = isset($_GET['search']) ? trim($_GET['search']) : '';

  $perPage = isset($_GET['perPage']) ? (int) $_GET['perPage'] : 3;
  $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

  if ($currentPage < 1) {
    header("Location: index.php?page=1");
  }

  $queryCount = 'SELECT COUNT(*) AS `count` FROM `entries`';
  $params = [];

  if (!empty($search)) {
      $queryCount .= " WHERE `title` LIKE :search";
      $params['search'] = "%$search%";
  }

  $stmtCount = $pdo->prepare($queryCount);
  $stmtCount->execute($params);
  $count = $stmtCount->fetch(PDO::FETCH_ASSOC)['count'];

  $startIndex = ($currentPage - 1) * $perPage + 1;
  $endIndex = min($currentPage * $perPage, $count);

  $offset = ($currentPage - 1) * $perPage;
  $numPages = ceil($count / $perPage);

  if ($currentPage > $numPages && $count !== 0) {
    header("Location: index.php?page={$numPages}");
  }

  $query = 'SELECT * FROM `entries`';

  if (!empty($search)) {
    $query .= " WHERE `title` LIKE :search";
  }

  $query .= ' ORDER BY `id` DESC LIMIT :perPage OFFSET :offset';

  $stmt = $pdo->prepare($query);
  if (!empty($search)) {
    $stmt->bindValue('search', "%$search%", PDO::PARAM_STR);
  }
  $stmt->bindValue('perPage', $perPage, PDO::PARAM_INT);
  $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
  $stmt->execute();
  
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?php require __DIR__ . '/views/header.view.php' ?>
  <div class="container">
      <div class="main-heading-container">
        <h1 class="main-heading">Entries</h1>

        <form id="searchForm">
          <input type="text" name="search" placeholder="Search" value="<?php echo trim($search); ?>">
        </form>
      </div>

    
      <?php if($count > 0): ?>
        <?php foreach($results as $result): ?>
          <?php 
            $dateExploded = explode('-', $result['date']);
            $timestamp = mktime(0, 0, 0, $dateExploded[1], $dateExploded[2], $dateExploded[0]);
            $dateFormatted = date('m-d-y', $timestamp);
          ?>

          <div class="card">
              <?php if(!empty($result['image'])): ?>
                <div class="card__image-container">
                  <img class="card__image" src="uploads/<?php echo e($result['image']); ?>" alt="<?php echo e($result['title']); ?>" />
                </div>
              <?php else: ?>
                <div class="card__image-container">
                  <img class="card__image" src="images/no-image.jpg" alt="<?php echo e($result['title']); ?>" />
                </div>
              <?php endif; ?>

              <div class="card__desc-container">
                <div class="date-actions-container">
                  <div class="card__desc-time">
                    <?php echo e($dateFormatted) ?>
                  </div>

                  <div class="actions-container">
                    <a href="edit-form.php?id=<?php echo $result['id']; ?>" class="edit">Edit</a>
                    <a href="delete.php?id=<?php echo $result['id']; ?>" class="delete">Delete</a>
                  </div>
                </div>
            
                <h2 class="card__heading">
                  <a href="show.php?id=<?php echo $result['id']; ?>">
                    <?php echo e($result['title']); ?>
                  </a>
                </h2>
                
                <p class="card__paragraph">
                  <?php 
                    $explodedDescription = explode(' ', $result['description']);
                    $explodedSelectedWords = array_slice($explodedDescription, 0, 50);
                    $implodedSelectedWords = implode(' ', $explodedSelectedWords) . '...';
                  ?>

                  <?php echo e($implodedSelectedWords) ?>
                </p>
              </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <h1 style="text-align: center; margin-top: 100px">NO DATA</h1>
      <?php endif; ?>




      <?php if($numPages > 1): ?>
        <div class="pagination-container">
          <div class="pagination-1">
            <select name="perPage" id="perPage">
              <option value="3" <?php echo isset($_GET['perPage']) && $_GET['perPage'] == 3 ? 'selected' : ''; ?>>3</option>
              <option value="6" <?php echo isset($_GET['perPage']) && $_GET['perPage'] == 6 ? 'selected' : ''; ?>>6</option>
              <option value="9" <?php echo isset($_GET['perPage']) && $_GET['perPage'] == 9 ? 'selected' : ''; ?>>9</option>
              <option value="12" <?php echo isset($_GET['perPage']) && $_GET['perPage'] == 12 ? 'selected' : ''; ?>>12</option>
            </select>
          </div>

          <div class="pagination-2">
            Showing <?php echo $count > 0 ? $startIndex : 0; ?> to <?php echo $endIndex; ?> of <?php echo $count; ?> results
          </div>

          <div class="pagination-3">
            <?php if($currentPage > 1): ?>
              <div class="pagination__li">
                  <a class="pagination__link" href="index.php?<?php echo http_build_query(['page' => $currentPage - 1, 'perPage' => $perPage]) ?>">⏴</a>
              </div>
            <?php endif; ?> 
        
            <?php for($i = 1; $i <= $numPages; $i++) : ?>
              <div class="pagination__li">
                  <a class="pagination__link <?php echo e($currentPage === $i ?'pagination__link--active' : '')?>" href="index.php?<?php echo http_build_query(['page' => $i]) ?>"><?php echo e($i) ?></a>
              </div>
            <?php endfor; ?>
          
            <?php if($currentPage < $numPages): ?>
              <div class="pagination__li">
                <a class="pagination__link disabled" href="index.php?<?php echo http_build_query(['page' => $currentPage + 1, 'perPage' => $perPage]) ?>">⏵</a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
  </div>

  <script>
    document.getElementById('perPage').addEventListener('change', function() {
      let url = new URL(window.location.href);
      url.searchParams.set('perPage', this.value);
      url.searchParams.set('page', 1);
      window.location.href = url.toString();
    });

    document.getElementById('searchForm').addEventListener('submit', function(e) {
      e.preventDefault();
      let url = new URL(window.location.href);
      let searchValue = document.querySelector('input[name="search"]').value.trim();
      url.searchParams.set('search', searchValue);
      url.searchParams.set('perPage', 3);
      url.searchParams.set('page', 1);

      window.location.href = url.toString();  
    });
  </script>
<?php require __DIR__ . '/views/footer.view.php' ?>
