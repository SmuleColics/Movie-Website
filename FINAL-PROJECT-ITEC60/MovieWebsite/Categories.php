<?php
include 'CineVault-header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Categories</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="FirstProject.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="Override.css" />
  <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">
</head>

<body class="bg-dark" id="categories-body">

  <!-- START OF MAIN CONTENT -->
  <main>
    <div id="main-content-categories"
      class="main-content mx-md-4 mx-3 d-flex flex-column align-items-center justify-content-center">
      <div class="main-content-top-categories text-white">
        <p class="text-center fw-semibold text-30">
          Explore a selection of Movies and TV Series organized by category.
        </p>
        <p id="categories-long-text" class="text-center w-75 mx-auto text-white-50">
          Explore a wide range of genres, from timeless classics to the latest
          updates. Dive into thrilling adventures, heartfelt romances,
          hilarious comedies, gripping dramas, chilling horrors, insightful
          documentaries, and more. Discover our categories and uncover your
          next captivating story.
        </p>
      </div>

      <div class="main-content-bottom-categories d-flex flex-wrap justify-content-center gap-2 gap-md-3">
        <?php
        $genre_query = mysqli_query($con, "SELECT genre_id, genre_name FROM tbl_movie_series_genre ORDER BY genre_name ASC");
        $btn_styles = ['btn-outline-primary', 'btn-outline-light'];
        $i = 0;
        while ($genre = mysqli_fetch_assoc($genre_query)):
          $style = $btn_styles[$i % 2];
          // Optional: make the genre name "safe" for HTML and URLs
          $genre_name = htmlspecialchars($genre['genre_name']);
          $genre_id = (int) $genre['genre_id'];
          ?>
          <button class="btn <?php echo $style; ?> rounded-5 category-btn" data-genre-id="<?php echo $genre_id; ?>">
            <?php echo $genre_name; ?>
          </button>
          <?php
          $i++;
        endwhile;
        ?>
      </div>
    </div>
  </main>
  <!-- END OF MAIN CONTENT -->

  <!-- START OF FOOTER -->
  <footer>
    <div id="footer-categories" class="footer text-white d-flex justify-content-between mx-5 align-items-center">
      <p class="footer-long-text">
        This site does not store any files on it's server, It only links to
        the media which is hosted on 3rd party services like YouTube,
        Dailymotion, Ok.ru, Vidsrc and more.
      </p>
      <p>© 2025 CineVault. All rights reserved.</p>
    </div>
  </footer>
  <!-- END OF FOOTER -->
  <script src="header-scroll.js"></script>
</body>
<script>
document.querySelectorAll('.category-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const genreId = this.getAttribute('data-genre-id');
    window.location.href = 'Website-Search.php?genre_id=' + genreId;
  });
});
</script>

</html>