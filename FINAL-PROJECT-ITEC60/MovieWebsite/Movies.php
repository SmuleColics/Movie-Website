<!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="FirstProject.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Override.css">
    <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg"> 
    
  </head>  
  <body class="bg-dark">
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
                      class="text-decoration-none text-primary fw-bold mx-2"
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
                      class="text-decoration-none text-white fw-bold ms-2"
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
                <!-- ========== END PROFILE MENU ========== -->
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
                    <a class="nav-link text-white" aria-current="page" href="FirstProject.php"
                      >Home</a
                    >
                  </li>
                  <li class="nav-item">
                    <a class="nav-link text-primary" href="Movies.php">Movies</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link text-white" href="Series.php">Series</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link text-white" href="Categories.php"
                      >Categories</a
                    >
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </nav>
        <!-- END OF HEADER -->

    <!-- START OF THE MAIN CONTENT -->
    <main>
      <!-- START OF SECTION WALLPAPER -->
      <section id="section-wallpaper-movies" class="section-wallpaper">
        <!-- data-bs-ride="carousel" -->
        <div id="carouselExampleControls" class="carousel slide carousel-fade" data-bs-ride="carousel">

          <div class="carousel-inner">
            <div class="carousel-item active position-relative">

              <img src="../Images/VenomWallpaper.jpg" class="d-block w-100 wallpaper" alt="Sonic Wallpaper">

              <div class="wallpaper-description text-primary">

                <div class="wallpaper-left text-white d-flex flex-column">
                  <div class="d-flex justify-content-center">
                    <img class="sonic-title-img" src="../Images/venomTitle.png" alt="Sonic Title">
                  </div>
                  <div class="wallpaper-ratings d-flex align-items-center justify-content-center my-2 my-md-4">
                    <div class="rating-year">
                      2024
                    </div>
                    <span class="mx-2 text-white-50">|</span>
                    <div class="rating-points d-flex">
                      16+
                    </div>
                    <span class="mx-2 text-white-50">|</span>
                    <div class="rating-gender text-nowrap">
                      Sci-Fi
                    </div>
                    <div class="wallpaper-line bg-secondary mx-3"></div>
                  </div>
                  <div class="rating-text">
                    <p>Eddie and Venom are on the run. Hunted by both of their worlds and with the net closing in, the duo are forced into a devastating decision that will bring...</p>
                  </div>
                </div>

                <div class="wallpaper-right mt-1">

                  <div class="wallpaper-watch d-none d-md-block">
                    <div class="d-flex align-items-center gap-3">
                      <button class="btn btn-outline-primary rounded-circle">
                        <i class="fa-solid fa-play"></i>
                      </button>
                      <p class="watch-now fs-2 fw-bold text-white m-0">WATCH NOW!</p>
                    </div>
                  </div>

                  <div class="wallpaper-watch-s d-md-none">
                    <button class="btn btn-primary rounded-5 watch-now-small">
                      <i class="fa-solid fa-play mx-1"></i>
                      <span>Watch Now</span>
                    </button>
                  </div>
                  
                </div>

              </div>

            </div>
            
            <div class="carousel-item">

              <img src="../Images/KravenWallpaper.jpg" class="d-block w-100 wallpaper" alt="Captain America Wallpaper">

              <div class="wallpaper-description text-primary">

                <div class="wallpaper-left text-white d-flex flex-column">
                  <div class="d-flex justify-content-center">
                    <img class="sonic-title-img" src="../Images/kravenTitle.png" alt="Captain America Title">
                  </div>
                  <div class="wallpaper-ratings d-flex align-items-center my-2 my-md-4">
                    <div class="rating-year">
                      2024
                    </div>
                    <span class="mx-2 text-white-50">|</span>
                    <div class="rating-points d-flex">
                      16+
                    </div>
                    <span class="mx-2 text-white-50">|</span>
                    <div class="rating-gender text-nowrap">
                      Sci-Fi
                    </div>
                    <div class="wallpaper-line bg-secondary mx-3"></div>
                  </div>
                  <div class="rating-text d-block">
                    Kraven Kravinoff's complex relationship with his ruthless gangster father, Nikolai, starts him down a path of vengeance with brutal consequences, motivating him to become not only the greatest...
                  </div>
                </div>

                <div class="wallpaper-right mt-1">

                  <div class="wallpaper-watch d-none d-md-block">
                    <div class="d-flex align-items-center gap-3">
                      <button class="btn btn-outline-primary rounded-circle">
                        <i class="fa-solid fa-play"></i>
                      </button>
                      <p class="watch-now fs-2 fw-bold text-white m-0">WATCH NOW!</p>
                    </div>
                  </div>

                  <div class="wallpaper-watch-s d-md-none">
                    <button class="btn btn-primary rounded-5 watch-now-small">
                      <i class="fa-solid fa-play mx-1"></i>
                      <span>Watch Now</span>
                    </button>
                  </div>
                  
                </div>

              </div>

            </div>

            <div class="carousel-item">
              <img src="../Images/PandaPlanWallpaper.jpg" class="d-block w-100 wallpaper" alt="Moana Wallpaper">

              <div class="wallpaper-description text-primary">

                <div class="wallpaper-left text-white d-flex flex-column">
                  <div class="d-flex justify-content-center">
                    <img class="sonic-title-img" src="../Images/pandaPlanTitle.png" alt="Moana Title">
                  </div>
                  <div class="wallpaper-ratings d-flex align-items-center my-2 my-md-4">
                    <div class="rating-year">
                      2024
                    </div>
                    <span class="mx-2 text-white-50">|</span>
                    <div class="rating-points d-flex">
                      16+
                    </div>
                    <span class="mx-2 text-white-50">|</span>
                    <div class="rating-gender text-nowrap">
                      Sci-Fi
                    </div>
                    <div class="wallpaper-line bg-secondary mx-3"></div>
                  </div>
                  <div class="rating-text d-block">
                    International action star Jackie Chan is invited to the adoption ceremony of a rare baby panda, but after an international crime syndicate attempts to...
                  </div>
                </div>

                <div class="wallpaper-right mt-3">

                  <div class="wallpaper-watch d-none d-md-block">
                    <div class="d-flex align-items-center gap-3">
                      <button class="btn btn-outline-primary rounded-circle">
                        <i class="fa-solid fa-play"></i>
                      </button>
                      <p class="watch-now fs-2 fw-bold text-white m-0">WATCH NOW!</p>
                    </div>
                  </div>

                  <div class="wallpaper-watch-s d-md-none">
                    <button class="btn btn-primary rounded-5 watch-now-small">
                      <i class="fa-solid fa-play mx-1"></i>
                      <span>Watch Now</span>
                    </button>
                  </div>
                  
                </div>

              </div>
            </div>
          </div>

          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>

        </div>
        
      </section>
      <!-- START OF SECTION WALLPAPER -->

      <!-- START OF SECTION RECOMMENDED -->
      <section id="section-coming-soon" class="section-action-movies ms-md-5 ms-3">
        <div class="action-movies-top d-flex justify-content-between">
          <p class="action-movies-text text-white fs-24 fw-bold">Recommended Movies for You</p>
        </div>
        <div class="action-images-container d-flex gap-3 position-relative">
          <div class="comedy-images">
            <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/2jIi55JtYKJTL1km8qHMuUilOWo.jpg" alt="">
            <i class="fa-solid fa-play play-button-comedy"></i>
          </div>
          <div class="comedy-images">
            <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/2Wmmu1MkqxJ48J7aySET9EKEjXz.jpg" alt="">
            <i class="fa-solid fa-play play-button-comedy"></i>
          </div>
          <div class="comedy-images">
            <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/3ZIPTvMnzI5ThmdGeEYQFxJV5Sg.jpg" alt="">
            <i class="fa-solid fa-play play-button-comedy"></i>
          </div>
          <div class="comedy-images">
            <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/gFEHva8Csx18hMGJJZ6gi4sFSKR.jpg" alt="">
            <i class="fa-solid fa-play play-button-comedy"></i>
          </div>
          <div class="comedy-images">
            <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/lgD4j9gUGmMckZpWWRJjorWqGVT.jpg" alt="">
            <i class="fa-solid fa-play play-button-comedy"></i>
          </div>
          <div class="comedy-images">
            <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/nEwybsfZBSPjcZamPpKDTaTI5g.jpg" alt="">
            <i class="fa-solid fa-play play-button-comedy"></i>
          </div>
          <div class="comedy-images">
            <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/oQxrvUhP3ycwnlxIrIMQ9Z3kleq.jpg" alt="">
            <i class="fa-solid fa-play play-button-comedy"></i>
          </div>
          <div class="comedy-images">
            <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/tK855sva67LB7opmPW4JHn1c5VI.jpg" alt="">
            <i class="fa-solid fa-play play-button-comedy"></i>
          </div>

          <div class="next-button-action position-absolute">
            <button class="btn border-1 next-chevron-btn">
              <i class="fas fa-chevron-right fa-xl text-white-50"></i>
            </button>
          </div>
        </div>
      </section>
      <!-- END OF SECTION RECOMMENDED -->

      <!-- START OF SECTION TOP 10 -->
      <section id="section-top-10-movies" class="section-top-10 text-white ms-5">  
        <div class="top-10-container">
          <div class="top-10-top d-flex align-items-center">
            <p class="top-10-top-text">TOP 10</p>
            <div class="ms-3 fs-2 movies-today-text">
              <p class="movies-text">MOVIES</p>
              <p class="today-text">TODAY</p>
            </div>
          </div>
          <div class="top-10-images-container d-flex gap-8 position-relative">
            <div class="top-10-images position-relative ">
              <p class="top-10-text">1</p>
              <div class="top-10-hover position-relative">
                <img class="top-10-img rounded-3" src="../Movie Web/Top 10 Movies/1ffZAucqfvQu36x1C49XfOdjuOG.jpg" alt="">
                <i class="fa-solid fa-play play-button-top-10"></i>
              </div>
            </div>
            <div class="top-10-images position-relative">
              <p class="top-10-text">2</p>
              <div class="top-10-hover position-relative">
                <img class="top-10-img rounded-3" src="../Movie Web/Top 10 Movies/4cR3hImKd78dSs652PAkSAyJ5Cx.jpg" alt="">
                <i class="fa-solid fa-play play-button-top-10"></i>
              </div>
            </div>
            <div class="top-10-images position-relative">
              <p class="top-10-text">3</p>
              <div class="top-10-hover position-relative">
                <img class="top-10-img rounded-3" src="../Movie Web/Top 10 Movies/5qGIxdEO841C0tdY8vOdLoRVrr0.jpg" alt="">
                <i class="fa-solid fa-play play-button-top-10"></i>
              </div>
            </div>
            <div class="top-10-images position-relative">
              <p class="top-10-text">4</p>
              <div class="top-10-hover position-relative">
                <img class="top-10-img rounded-3" src="../Movie Web/Top 10 Movies/7iMBZzVZtG0oBug4TfqDb9ZxAOa.jpg" alt="">
                <i class="fa-solid fa-play play-button-top-10"></i>
              </div>
            </div>
            <div class="top-10-images position-relative">
              <p class="top-10-text">5</p>
              <div class="top-10-hover position-relative">
                <img class="top-10-img rounded-3" src="../Movie Web/Top 10 Movies/9bXHaLlsFYpJUutg4E6WXAjaxDi.jpg" alt="">
                <i class="fa-solid fa-play play-button-top-10"></i>
              </div>
            </div>
            <div class="top-10-images position-relative">
              <p class="top-10-text">6</p>
              <div class="top-10-hover position-relative">
                <img class="top-10-img rounded-3" src="../Movie Web/Top 10 Movies/Ad0ZK9wUOKjyB8J90JvMUh0TQ4E.jpg" alt="">
                <i class="fa-solid fa-play play-button-top-10"></i>
              </div>
            </div>
            <div class="top-10-images position-relative">
              <p class="top-10-text">7</p>
              <div class="top-10-hover position-relative">
                <img class="top-10-img rounded-3" src="../Movie Web/Top 10 Movies/lu2vmmtStmTNMmSZl2LgrrQpLZo.jpg" alt="">
                <i class="fa-solid fa-play play-button-top-10"></i>
              </div>
            </div>
            <div class="top-10-images position-relative">
              <p class="top-10-text">8</p>
              <div class="top-10-hover position-relative">
                <img class="top-10-img rounded-3" src="../Movie Web/Top 10 Movies/oCoTgC3UyWGfyQ9thE10ulWR7bn.jpg" alt="">
                <i class="fa-solid fa-play play-button-top-10"></i>
              </div>
            </div>
            <div class="top-10-images position-relative">
              <p class="top-10-text">9</p>
              <div class="top-10-hover position-relative">
                <img class="top-10-img rounded-3" src="../Movie Web/Top 10 Movies/pzIddUEMWhWzfvLI3TwxUG2wGoi.jpg" alt="">
                <i class="fa-solid fa-play play-button-top-10"></i>
              </div>
            </div>
            <div class="top-10-images position-relative">
              <p class="top-10-text">10</p>
              <div class="top-10-hover position-relative">
                <img class="top-10-img rounded-3" src="../Movie Web/Top 10 Movies/uEygVMvDjgTz9N3sgJbn9auaNIA.jpg" alt="">
                <i class="fa-solid fa-play play-button-top-10"></i>
              </div>
            </div>

            <div class="next-button position-absolute">
              <button class="btn border-1 next-chevron-btn">
                <i class="fas fa-chevron-right fa-xl text-white-50"></i>
              </button>
            </div>
          </div>
        </div>
      </section>
      <!-- END OF SECTION TOP 10 -->

      <!-- START OF SECTION POPULAR DOCUMENTARIES -->
      <section id="section-popular-documentaries-movies" class="section-trending ms-md-5 ms-3">
        <div id="dropdown-poular-documentaries" class="trending-this-week d-flex justify-content-between">
          <p class="trending-text text-white fs-24 fw-bold">Popular Documentaries</p>
        </div>
        <div class="trending-images-container d-flex gap-3 position-relative">
          <div class="trending-images">
            <img class="trending-img rounded-3" src="../Movie Web/Trending This Week/6GDW4EsgsXlYrL1ASb5eCHQK4er.jpg" alt="">
            <i class="fa-solid fa-play play-button"></i>
          </div>
          <div class="trending-images">
            <img class="trending-img rounded-3" src="../Movie Web/Trending This Week/7MrgIUeq0DD2iF7GR6wqJfYZNeC.jpg" alt="">
            <i class="fa-solid fa-play play-button"></i>
          </div>
          <div class="trending-images">
            <img class="trending-img rounded-3" src="../Movie Web/Trending This Week/293Mo4GWf7Tl0TfAr5NFghqeMy7.jpg" alt="">
            <i class="fa-solid fa-play play-button"></i>
          </div>
          <div class="trending-images">
            <img class="trending-img rounded-3" src="../Movie Web/Trending This Week/fbGCmMp0HlYnAPv28GOENPShezM.jpg" alt="">
            <i class="fa-solid fa-play play-button"></i>
          </div>
          <div class="trending-images">
            <img class="trending-img rounded-3" src="../Movie Web/Trending This Week/geCRueV3ElhRTr0xtJuEWJt6dJ1.jpg" alt="">
            <i class="fa-solid fa-play play-button"></i>
          </div>
          <div class="trending-images">
            <img class="trending-img rounded-3" src="../Movie Web/Trending This Week/jNFcfgLFPhCZdhUUmwjHvq6TLxh.jpg" alt="">
            <i class="fa-solid fa-play play-button"></i>
          </div>
          <div class="trending-images">
            <img class="trending-img rounded-3" src="../Movie Web/Trending This Week/lastHere.jpg" alt="">
            <i class="fa-solid fa-play play-button"></i>
          </div>
          <div class="trending-images">
            <img class="trending-img rounded-3" src="../Movie Web/Trending This Week/sEma5RN8aZ5Yu2Dkefyn5ULdlc1.jpg" alt="">
            <i class="fa-solid fa-play play-button"></i>
          </div>

          <div class="next-button-trending position-absolute">
            <button class="btn border-1 next-chevron-btn">
              <i class="fas fa-chevron-right fa-2xl text-white-50"></i>
            </button>
          </div>
        </div>
      </section>
      <!-- START OF SECTION TRENDING -->
      
      <!-- START OF FEATURED -->
      <section class="section-featured text-white ms-md-5 ms-3">  
        <div class="featured-container">
          <div class="featured-top">
            <p class="featured-text">FEATURED</p>
            <p class="featured-movies-text fs-2">MOVIES</p>
          <div class="featured-images-container d-flex gap-3 position-relative">
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Freatured/2cxhvwyEwRlysAmRH4iodkvo0z5.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Freatured/3L3l6LsiLGHkTG4RFB2aBA6BttB.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Trending This Week/CA.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Freatured/4cR3hImKd78dSs652PAkSAyJ5Cx.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Freatured/4Qksv87C57O0IWA8sT2KwxZ4fXh.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Freatured/6m435uh40N7Gzfbd69ttp6W0sdR.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Freatured/A9ENz6d4lC3UYOX8Z1gJwDEo1sM.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Freatured/ajb1rMiorchfRemYHZCkbV9DBg6.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>

            <div class="next-button-featured position-absolute">
              <button class="btn border-1 next-chevron-btn">
                <i class="fas fa-chevron-right fa-xl text-white-50"></i>
              </button>
            </div>
          </div>
        </div>
      </section>
      <!-- END OF SECTION FEATURED -->

      <!-- START OF MOVIES 1 -->
      <section class="movies-1 mt-80">
        <div>

          <img src="../Images/PrincessMononoke.jpg" class="d-block w-100 wallpaper-lotr bg-danger" alt="Sonic Wallpaper">

          <div class="wallpaper-description-tem d-flex flex-column justify-content-end align-items-end position-relative me-5">

              <div class="d-flex justify-content-center">
                <img class="tem-title-img position-relative mb-1" src="../Images/PrincessMononokeTitle.png" alt="Sonic Title">
              </div>
              
              <div class="rating-text-tem my-4 position-relative text-white">
                <p>Ashitaka, a prince of the disappearing Emishi people, is cursed by a demonized boar god and must journey to the west to find a cure. Along the way, he encounters San, a young human woman fighting</p>
              </div>
                
              <div class="watch-now-tem position-relative">
                <button class="btn btn-primary rounded-5 watch-now-small">
                  <i class="fa-solid fa-play mx-1"></i>
                  <span>Watch Now</span>
                </button>
              </div>

              </div>

          </div>

        </div>
      </section>
    <!-- END OF MOVIES 1 -->

    <!-- START OF SECTION MCAM -->
    <section id="section-mcam" class="section-action-movies ms-md-5 ms-3">
      <div class="action-movies-top d-flex justify-content-between">
        <p class="action-movies-text text-white fs-24 fw-bold">Most Critically Acclaimed Movies</p>
      </div>
      <div class="action-images-container d-flex gap-3 position-relative">
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Freatured/diNagamit.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Freatured/jTPBMPTgk9zOUGSkWcoOGbX8cTi.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Freatured/nrlfJoxP1EkBVE9pU62L287Jl4D.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Freatured/ttN5D6GKOwKWHmCzDGctAvaNMAi.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Freatured/TVvIyCsFCmLk9MRLbAZi4X8f32.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Freatured/uDW5eeFUYp1vaU2ymEdVBG6g7iq.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Freatured/v313aUGmMNj6yNveaiQXysBmjVS.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Freatured/vGXptEdgZIhPg3cGlc7e8sNPC2e.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>

        <div class="next-button-action position-absolute">
          <button class="btn border-1 next-chevron-btn">
            <i class="fas fa-chevron-right fa-xl text-white-50"></i>
          </button>
        </div>
      </div>
    </section>
    <!-- END OF SECTION MCAM -->

    <!-- START OF MOVIES 2 -->
    <section class="movies-2">
      <div>

        <img src="../Images/Stellar.jpg" class="d-block w-100 wallpaper-lotr" alt="Sonic Wallpaper">

        <div class="wallpaper-description-lotr d-flex flex-column justify-content-end align-items-end position-relative me-5">

            <div class="d-flex justify-content-center">
              <img class="lotr-title-img position-relative mb-1" src="../Images/InterstellarTitle.png" alt="Sonic Title">
            </div>
            
            <div class="rating-text-lotr my-4 position-relative text-white">
              <p>The adventures of a group of explorers who make use of a newly discovered wormhole to surpass the limitations on human space travel and conquer the vast distances involved in an interstellar voyage.
              </p>
            </div>
              
            <div class="watch-now-lotr position-relative">
              <button class="btn btn-primary rounded-5 watch-now-small">
                <i class="fa-solid fa-play mx-1"></i>
                <span>Watch Now</span>
              </button>
            </div>

            </div>

        </div>

      </div>
    </section>
    <!-- END OF MOVIES 2 -->
    </main> 
    <!-- END OF THE MAIN CONTENT -->

    <footer>
      <div class="footer text-white d-flex justify-content-between mx-5 align-items-center">
        <p class="footer-long-text">This site does not store any files on it's server, It only links to the media which is hosted on 3rd party services like YouTube, Dailymotion, Ok.ru, Vidsrc and more.</p>
        <p>© 2025 CineVault. All rights reserved.</p>
      </div>
    </footer>
    
  </body>

  <script src="header-scroll.js"></script>
</html>