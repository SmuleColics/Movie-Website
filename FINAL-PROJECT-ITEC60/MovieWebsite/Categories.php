<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Categories</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    <link rel="stylesheet" href="FirstProject.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="Override.css" />
    <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg"> 
  </head>
  <body class="bg-dark" id="categories-body">
    <!-- START OF HEADER -->
    <nav id="navbar" class="navbar navbar-dark fixed-top">
      <div class="container-fluid">
        <header
          class="d-flex justify-content-between align-items-center w-100 px-md-5 bg-transparent"
        >
          <div class="left-header">
            <a class="navbar-brand fw-semibold" href="#"
              >Cine<span class="text-primary">Vault</span></a
            >
          </div>

          <div class="middle-header position-relative d-none d-lg-block">
            <ul class="d-flex list-unstyled fs-18 m-0">
              <li>
                <a
                  href="FirstProject.php"
                  class="text-decoration-none text-white fw-bold me-2"
                  >Home</a
                >
              </li>
              <li>
                <a
                  href="Movies.php"
                  class="text-decoration-none text-white fw-bold mx-2"
                  >Movies</a
                >
              </li>
              <li>
                <a
                  href="Series.php"
                  class="text-decoration-none text-white fw-bold mx-2"
                  >Series</a
                >
              </li>
              <li>
                <a
                  href="Categories.php"
                  class="text-decoration-none text-primary fw-bold ms-2"
                  >Categories</a
                >
              </li>
            </ul>
          </div>

          <div class="right-header d-flex">
            <div class="d-flex align-items-center">
              <div class="search-container">
                <input
                  class="search rounded-5 bg-transparent text-white"
                  type="text"
                  placeholder="Search..."
                />
                <i class="fa-solid fa-magnifying-glass"></i>
              </div>
              <!-- ========== PROFILE MENU ========== -->
                <div class="dropdown-center">

                  <button class="btn p-0 border-0" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="header-user rounded-circle flexbox-align ms-1" style="padding: 11px; color: #f4fff8;">
                      <i class="fa-solid fa-user db-text-sec fs-18"></i>
                    </div>
                  </button>

                  <ul class="dropdown-menu dropdown-menu-dark mt-1 profile-dropdown" style="transform: translateX(-130px);">
                      <li class="dropdown-profile-top d-flex mb-1" style="height: 50px;">
                        <a class="dropdown-item d-flex align-items-center" href="">

                          <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center ms-1"
                            style="height: 32px; width: 32px; transform: translateX(-9px);">
                            <i class="fa-solid fa-user db-text-sec fs-18"></i>
                          </div>

                          <div class="dropdown-profile-text" style="margin-left: -4px;">
                            <p class="fs-18 view-profile-text" style="transform: translateY(10px)">Manage Profile</p>
                            <p class="m-0 fs-14 db-text-secondary" style="transform: translateY(-10px)">User</p>
                          </div>
                        </a>
                      </li>
                      <li class="mb-1">
                        <a class="dropdown-item d-flex align-items-center" href="#">
                          <i class="fa-solid fa-question ms-1 me-2 fs-22"></i>
                          <span class="fs-18 d-inline-block ms-1">Help & Support</span>
                        </a>
                      </li>
                      <li class="mb-1" style="transform: translateX(-12px);">
                        <a class="dropdown-item d-flex align-items-center" href="#">
                          <i class="fa-solid fa-gear me-2 fs-22 "></i>
                          <span class="fs-18 d-inline-block">Settings</span>
                        </a>
                      </li>
                      <li class="mb-1">
                        <a href="../LANDING-PAGE/LandingPageMovie.php" class="dropdown-item d-flex align-items-center" >
                          <i class="fa-solid fa-right-from-bracket me-2 fs-22"></i>
                          <span class="fs-18 d-inline-block">Log out</span>
                        </a>
                      </li>
                    </ul>
                  </div>

                </div>
              <button
                class="navbar-toggler text-light d-lg-none ms-3"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasDarkNavbar"
                aria-controls="offcanvasDarkNavbar"
              >
                <span class="navbar-toggler-icon"></span>
              </button>
            </div>
          </div>
        </header>

        <div
          class="offcanvas offcanvas-end text-bg-dark"
          tabindex="-1"
          id="offcanvasDarkNavbar"
          aria-labelledby="offcanvasDarkNavbarLabel"
        >
          <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">
              Cine<span class="text-primary">Vault</span>
            </h5>
            <button
              type="button"
              class="btn-close btn-close-white"
              data-bs-dismiss="offcanvas"
              aria-label="Close"
            ></button>
          </div>
          <div class="offcanvas-body text-center">
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
              <li class="nav-item">
                <a class="nav-link text-white" aria-current="page" href="FirstProject.html"
                  >Home</a
                >
              </li>
              <li class="nav-item">
                <a class="nav-link text-white" href="Movies.html">Movies</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white" href="Series.html">Series</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-primary" href="Categories.html"
                  >Categories</a
                >
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>
    <!-- END OF HEADER -->

    <!-- START OF MAIN CONTENT -->
    <main>
      <div
        id="main-content-categories"
        class="main-content mx-md-4 mx-3 d-flex flex-column align-items-center justify-content-center"
      >
        <div class="main-content-top-categories text-white">
          <p class="text-center fw-semibold text-30">
            Explore a selection of Movies and TV Series organized by category.
          </p>
          <p
            id="categories-long-text"
            class="text-center w-75 mx-auto text-white-50"
          >
            Explore a wide range of genres, from timeless classics to the latest
            updates. Dive into thrilling adventures, heartfelt romances,
            hilarious comedies, gripping dramas, chilling horrors, insightful
            documentaries, and more. Discover our categories and uncover your
            next captivating story.
          </p>
        </div>

        <div
          class="main-content-bottom-categories d-flex flex-wrap justify-content-center gap-2 gap-md-3"
        >
          <button class="btn btn-outline-primary rounded-5">Action</button>
          <button class="btn btn-outline-light rounded-5">Adventure</button>
          <button class="btn btn-outline-primary rounded-5">Animation</button>
          <button class="btn btn-outline-light rounded-5">Comedy</button>
          <button class="btn btn-outline-primary rounded-5">Crime</button>
          <button class="btn btn-outline-light rounded-5">Documentary</button>
          <button class="btn btn-outline-primary rounded-5">Drama</button>
          <button class="btn btn-outline-light rounded-5">Family</button>
          <button class="btn btn-outline-primary rounded-5">Fantasy</button>
          <button class="btn btn-outline-light rounded-5">History</button>
          <button class="btn btn-outline-primary rounded-5">Terror</button>
          <button class="btn btn-outline-light rounded-5">Music</button>
          <button class="btn btn-outline-primary rounded-5">Mystery</button>
          <button class="btn btn-outline-light rounded-5">Science fiction</button>
          <button class="btn btn-outline-light rounded-5">Cinema TV</button>
          <button class="btn btn-outline-primary rounded-5">Thriller</button>
          <button class="btn btn-outline-light rounded-5">War</button>
          <button class="btn btn-outline-primary rounded-5">Western</button>
          <button class="btn btn-outline-light rounded-5">Kids</button>
          <button class="btn btn-outline-primary rounded-5">News</button>
          <button class="btn btn-outline-light rounded-5">Reality</button>
          <button class="btn btn-outline-primary rounded-5">
            Sci-Fi & Fantasy
          </button>
          <button class="btn btn-outline-light rounded-5">Soap</button>
          <button class="btn btn-outline-primary rounded-5">Talk</button>
          <button class="btn btn-outline-light rounded-5">
            War & Politics
          </button>
        </div>
      </div>
    </main>
    <!-- END OF MAIN CONTENT -->

    <!-- START OF FOOTER -->
    <footer>
      <div
        id="footer-categories"
        class="footer text-white d-flex justify-content-between mx-5 align-items-center"
      >
        <p class="footer-long-text">
          This site does not store any files on it's server, It only links to
          the media which is hosted on 3rd party services like YouTube,
          Dailymotion, Ok.ru, Vidsrc and more.
        </p>
        <p>© 2025 CineVault. All rights reserved.</p>
      </div>
    </footer>
    <!-- START OF FOOTER -->
    <script src="header-scroll.js"></script>
  </body>
</html>
