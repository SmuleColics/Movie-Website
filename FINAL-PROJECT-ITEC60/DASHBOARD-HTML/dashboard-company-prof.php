<?php
include '../includes/dashboard-header-sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Company Profile</title>
    <!-- ========== CSS LINK ========== -->
    <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css">
    <style>
      .sidebar-content-item:nth-child(2) {
        background-color: var(--dashboard-primary);
      }
    </style>
  </head>

  <body>
    <main class="container-lg p-0 overflow-hidden">
      <section class="company-profile-section p-3">
        <div class="row g-3">

          <div class="col-xl-8 col-md-8">
            <div class="card bg-dark">
              <div class="card-body">

                <div class="emp-stats-top db-text-sec">
                  <div class="py-2 ps-3">
                    <p class="m-0 fs-20">Company Profile</p>
                    <p class="m-0 fs-14" style="transform: translateY(-2px)">Description about the company</p>
                  </div>
                </div>

                <div class="emp-stats-bottom mt-2  db-text-secondary">
                  <!-- ========== COMPANY PROFILE 1ST ROW ========== -->
                  <div class="row mx-0 mt-2">
                    <div class="col p-0">
                      <div class="d-grid px-2 py-1" >
                        <label class="fs-14">Company Name</label>
                        <input type="text" class="input-cp" value="CineVault" disabled >
                      </div>
                    </div>
                    <div class="col p-0">
                      <div class="d-grid px-2 py-1">
                        <label class="fs-14">Username</label>
                        <input type="text" class="input-cp fs-16" value="admin@cinevault.com" disabled>
                      </div>
                    </div>
                    <div class="col p-0">
                      <div class="d-grid px-2 py-1">
                        <label class="fs-14">Email Address</label>
                        <input type="text" class="input-cp fs-16" value="CineVault@gmail.com" disabled>
                      </div>
                    </div>
                  </div>
                  <!-- ========== COMPANY PROFILE 2ND ROW ========== -->
                  <div class="row mx-0 mt-2">
                    <div class="col p-0">
                      <div class="d-grid px-2 py-1" >
                        <label class="fs-14">Admin First Name</label>
                        <input type="text" class="input-cp fs-16" value="Roberto" disabled>
                      </div>
                    </div>
                    <div class="col p-0">
                      <div class="d-grid px-2 py-1">
                        <label class="fs-14">Admin Last Name</label>
                        <input type="text" class="input-cp fs-16" value="Hawan" disabled>
                      </div>
                    </div>
                  </div>
                  <!-- ========== COMPANY PROFILE 3RD ROW ========== -->
                  <div class="row mx-0 mt-2">
                    <div class="col p-0">
                      <div class="d-grid px-2 py-1" >
                        <label class="fs-14">Address</label>
                        <input type="text" class="input-cp fs-16" value="Cavite, Philippines" disabled>
                      </div>
                    </div>
                  </div>
                  <!-- ========== COMPANY PROFILE 4TH ROW ========== -->
                  <div class="row mx-0 mt-2">
                    <div class="col p-0">
                      <div class="d-grid px-2 py-1" >
                        <label class="fs-14">City</label>
                        <input type="text" class="input-cp fs-16" value="Carmona" disabled>
                      </div>
                    </div>
                    <div class="col p-0">
                      <div class="d-grid px-2 py-1">
                        <label class="fs-14">Country</label>
                        <input type="text" class="input-cp fs-16" value="Philippines" disabled>
                      </div>
                    </div>
                    <div class="col p-0">
                      <div class="d-grid px-2 py-1">
                        <label class="fs-14">Postal Code</label>
                        <input type="text" class="input-cp fs-16" value="4020" disabled>
                      </div>
                    </div>
                  </div>

                  <div class="d-grid mt-4 mb-1 mx-2">
                    <h5 class="db-text-sec">About Us</h5>
                    <textarea class="textarea-about p-0" rows="3" disabled placeholder="ewan ko ba rito">CineSpree is a platform dedicated to celebrating movies and series across all genres. From blockbusters to hidden gems, our goal is to give film fans a place to explore, review, and discuss the stories they love.</textarea>
                  </div>
                  
                  <div class="d-flex justify-content-end mt-3 me-2">
                    <button class="btn db-bg-primary db-text-sec">Update Profile</button>
                  </div>

                </div>
                

              </div>
            </div>
          </div>
          
          <div class="col-lg-4 col-md-4">
            <div class="card bg-dark">
              <div class="card-body">
                <div class="ceo-top-container flexbox-align flex-column">
                  <img src="../MOVIE-IMG/DASHBOARD-IMG/james-pfp.jpg" alt="CEO Pfp" class="card-img-top rounded-pill" style="width: clamp(120px, 15vw, 160px);">
                  <p class="db-text-secondary mt-3 mb-0 fs-14">CEO / FOUNDER</p>
                  <p class="db-text-sec fs-18">James Macalintal</p>
                  <p class="db-text-secondary text-center"> CEO and founder of CineVault, is driven by his love for storytelling and cinema. He created CineVault not just as a streaming site, but as a curated space for unforgettable movie and series experiences — blending tech with heart for every viewer.</p>  
                  <a href="https://www.facebook.com/james.mangalindan" class="btn db-bg-primary db-text-sec follow-btn">Follow</a>
                </div>

              </div>
            </div>
          </div>
        </div>  
      </section>
    </main>
  </body>

</html>