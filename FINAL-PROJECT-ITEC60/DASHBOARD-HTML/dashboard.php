<?php
include '../includes/db-connection.php';
include '../includes/dashboard-header-sidebar.php';

$sql = mysqli_query($con, "SELECT p.date_created 
                          FROM tbl_payment AS p 
                          JOIN tbl_signup_acc AS s ON p.signup_id = s.signup_id");

$users_quantity = array(0, 0, 0, 0, 0, 0, 0);

while ($row = mysqli_fetch_assoc($sql)) {
    $date_created = $row['date_created'];

    // Get the day of the week (0 = Sunday, 6 = Saturday)
    $day_index = date('w', strtotime($date_created));

    // Increment the corresponding day in users_quantity
    $users_quantity[$day_index]++;
}


$labels = array("Sun", "Mon", "Tues", "Wed", "Thurs", "Fri", "Sat");

//USERS
$result = mysqli_query($con, "SELECT COUNT(*) FROM tbl_signup_acc");
$row = mysqli_fetch_array($result);

$revenue_query = mysqli_query($con, "SELECT SUM(p.payment_amount) FROM tbl_signup_acc AS s JOIN tbl_payment AS p ON s.signup_id = p.signup_id");
$revenue = mysqli_fetch_array($revenue_query);

$movie_query = mysqli_query($con, "SELECT COUNT(category) FROM tbl_trending WHERE category = 'Movie'");
$movie = mysqli_fetch_array($movie_query);

$series_query = mysqli_query($con, "SELECT COUNT(category) FROM tbl_trending WHERE category = 'Series'");
$series = mysqli_fetch_array($series_query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Main Dashboard</title>
  <!-- ========== CSS LINK ========== -->
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css">
  <link rel="stylesheet" href="../DASHBOARD-CSS/for-all.css">
  <!-- ========== FONT AWESOME CDN ========== -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    integrity="sha512-yX3+R+x6vHhvEcH3q5jTyZl+AhSYB6Q9k5ovFAnPjgw8ZfuZxV1Q5h8ER8rYz7I1pMcmZkq/IsDlE96j6EeB1A=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
      .sidebar-content-item:nth-child(1) {
        background-color: var(--dashboard-primary);
      }
    </style>

</head>

<body>
  <main class="container-lg p-0 overflow-hidden">
    <!-- ========== CARD SECTION ========== -->
    <section class="card-section p-3">
      <div class="row g-3">
        <!-- ========== CARD USERS ========== -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
          <div class="card bg-dark">

            <div class="card-body">

              <div class="card-top d-flex align-items-center justify-content-between">
                <div class="card-top-left">
                  <div class="card-icon-container flexbox-align" style="background-color: #607BEC;">
                    <i class="fa-solid fa-user db-card-icon"></i>
                  </div>
                </div>
                <div class="card-top-right w-100 text-end mt-3 db-text-sec">
                  <h6 class="card-subtitle fs-14 mb-1">Users</h6>
                  <h5 class="card-title text-end fs-24">
                    <?php echo $row["COUNT(*)"]; ?>
                  </h5>
                </div>
              </div>

              <div class="card-divider my-3"></div>

              <div class="card-bottom d-flex align-items-center gap-2 db-text-secondary fs-12">
                <i class="fa-solid fa-clock"></i>
                <p class="m-0">Total Users</p>
              </div>

            </div>
          </div>
        </div>

        <!-- ========== CARD REVENUE ========== -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
          <div class="card bg-dark">

            <div class="card-body">

              <div class="card-top d-flex align-items-center justify-content-between">
                <div class="card-top-left">
                  <div class="card-icon-container flexbox-align" style="background-color: #607BEC;">
                    <i class="fa-solid fa-peso-sign db-card-icon"></i>
                  </div>
                </div>
                <div class="card-top-right w-100 text-end mt-3 db-text-sec">
                  <h6 class="card-subtitle fs-14 mb-1">Revenue</h6>
                  <h5 class="card-title fs-24">₱<?php echo $revenue[0]; ?></h5>
                </div>
              </div>

              <div class="card-divider my-3"></div>

              <div class="card-bottom d-flex align-items-center gap-2 db-text-secondary fs-12">
                <i class="fa-solid fa-clock"></i>
                <p class="m-0">Total Revenue</p>
              </div>

            </div>
          </div>
        </div>

        <!-- ========== CARD MOVIES ========== -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
          <div class="card bg-dark">

            <div class="card-body">

              <div class="card-top d-flex align-items-center justify-content-between">
                <div class="card-top-left">
                  <div class="card-icon-container flexbox-align" style="background-color: #607BEC;">
                    <i class="fa-solid fa-film db-card-icon"></i>
                  </div>
                </div>
                <div class="card-top-right w-100 text-end mt-3 db-text-sec">
                  <h6 class="card-subtitle fs-14 mb-1">Movies</h6>
                  <h5 class="card-title fs-24"><?php echo $movie[0]; ?></h5>
                </div>
              </div>

              <div class="card-divider my-3"></div>

              <div class="card-bottom d-flex align-items-center gap-2 db-text-secondary fs-12">
                <i class="fa-solid fa-clock"></i>
                <p class="m-0">Total Users</p>
              </div>

            </div>
          </div>
        </div>

        <!-- ========== CARD SERIES ========== -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
          <div class="card bg-dark">

            <div class="card-body">

              <div class="card-top d-flex align-items-center justify-content-between">
                <div class="card-top-left">
                  <div class="card-icon-container flexbox-align" style="background-color: #607BEC;">
                    <i class="fa-solid fa-video db-card-icon"></i>
                  </div>
                </div>
                <div class="card-top-right w-100 text-end mt-3 db-text-sec">
                  <h6 class="card-subtitle fs-14 mb-1">Series</h6>
                  <h5 class="card-title fs-24"><?php echo $series[0]; ?></h5>
                </div>
              </div>

              <div class="card-divider my-3"></div>

              <div class="card-bottom d-flex align-items-center gap-2 db-text-secondary fs-12">
                <i class="fa-solid fa-clock"></i>
                <p class="m-0">Total Series</p>
              </div>

            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- ========== CHART SECTION ========== -->
    <section class="chart-section px-3 py-2">
      <div class="row g-3">
        <!-- ========== LINE GRAPH USERS ========== -->
        <div class="col-xl-6 col-lg-6">
          <div class="card bg-dark">
            <center class="card-chart-container" style="height: 257px; position: relative;">
              <canvas id="lineChart" style="width: 100%; height: 100%;"></canvas>
            </center>
            <div class="card-body">
              <div class="card-chart-top db-text-sec">
                <h6 class="card-subtitle fs-18">Daily Users</h6>
                <h5 class="card-title text-start fs-14 db-text-secondary">Users activity this week</h5>
              </div>
              <div class="card-divider my-3"></div>
              <div class="card-chart-bottom db-text-secondary d-flex align-items-center gap-2 fs-14">
                <i class="fa-solid fa-clock"></i>
                <p class="m-0">Daily Users</p>
              </div>

            </div>
          </div>
        </div>

        <!-- ========== PIE GRAPH WATCHED MOVIES ========== -->
        <div class="col-xl-6 col-lg-6 h-100">
          <div class="card bg-dark">
            <div class="card-chart-container">
              <center>
                <canvas id="pieGraph" class="p-2"></canvas>
              </center>
            </div>
            <div class="card-body">
              <div class="card-chart-top db-text-sec">
                <h6 class="card-subtitle fs-18">Most Watched Genres</h6>
                <h5 class="card-title text-start fs-14 db-text-secondary">Genre distribution among top viewed movies
                </h5>
              </div>
              <div class="card-divider my-3"></div>
              <div class="card-chart-bottom db-text-secondary d-flex align-items-center gap-2 fs-14">
                <i class="fa-solid fa-clock"></i>
                <p class="m-0">Viewership by Genre</p>
              </div>
            </div>
          </div>
        </div>
      

        <!-- ========== BAR GRAPH MOVIES ========== -->
        <div class="col-xl-12 mt-3">
          <div class="card bg-dark">
            <div class="card-chart-container">
              <canvas id="barGraphMovies" class="p-2 w-100"></canvas>
            </div>
            <div class="card-body">
              <div class="card-chart-top db-text-sec">
                <h6 class="card-subtitle fs-18">Movie Genres</h6>
                <h5 class="card-title text-start fs-14 db-text-secondary"> Overview of movie categories</h5>
              </div>
              <div class="card-divider my-3"></div>
              <div class="card-chart-bottom db-text-secondary d-flex align-items-center gap-2 fs-14">
                <i class="fa-solid fa-clock"></i>
                <p class="m-0">Genre Statistics</p>
              </div>
            </div>
          </div>
        </div>

        <!-- ========== BAR GRAPH SERIES ========== -->
        <div class="col-xl-12 mt-3">
          <div class="card bg-dark">
            <div class="card-chart-container">
              <canvas id="barGraphSeries" class="p-2 w-100"></canvas>
            </div>
            <div class="card-body">
              <div class="card-chart-top db-text-sec">
                <h6 class="card-subtitle fs-18">Series Genres</h6>
                <h5 class="card-title text-start fs-14 db-text-secondary"> Overview of series categories</h5>
              </div>
              <div class="card-divider my-3"></div>
              <div class="card-chart-bottom db-text-secondary d-flex align-items-center gap-2 fs-14">
                <i class="fa-solid fa-clock"></i>
                <p class="m-0">Genre Statistics</p>
              </div>
            </div>
          </div>
        </div> 
        
      </div>
    </section>

    <!-- ========== TABLES SECTION ========== -->
    <!-- <section class="db-tables-section p-3">
      <div class="row g-3">
    
        <div class="col-xl-7">
          <div class="card bg-dark">
            <div class="card-body">

              <div class="table-task-top db-text-sec">
                <div class="py-2 ps-3">
                  <p class="m-0 fs-20">Employee Status</p>
                  <p class="m-0 fs-14" style="transform: translateY(-2px)">CineVault Develop Team</p>
                </div>
              </div>

              <div class="table-responsive mt-2">
                <table class="table table-hover text-center">
                  <thead>
                    <tr>
                      <th class="db-text-primary" scope="col">#</th>
                      <th class="db-text-primary" scope="col">Name</th>
                      <th class="db-text-primary" scope="col">Section</th>
                      <th class="db-text-primary" scope="col">Role</th>
                      <th class="db-text-primary" scope="col">Edit</th>
                      <th class="db-text-primary" scope="col">Delete</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th class="db-text-primary" scope="row">1</th>
                      <td>John Lenard Colico</td>
                      <td>BSIT-2C</td>
                      <td>Full Stack Developer</td>
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#empEditModal">
                          <i class="fa-solid fa-pen-to-square"></i> 
                        </button>
                      </td> 
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#empDeleteModal">
                          <i class="fa-solid fa-delete-left text-danger"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <th class="db-text-primary" scope="row">2</th>
                      <td>Eisen Drix Plantilla</td>
                      <td>BSIT-2C</td>
                      <td>Back-End Developer</td>
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#empEditModal">
                          <i class="fa-solid fa-pen-to-square"></i> 
                        </button>
                      </td> 
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#empDeleteModal">
                          <i class="fa-solid fa-delete-left text-danger"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <th class="db-text-primary" scope="row">3</th>
                      <td>Ma. Christina Sandil</td>
                      <td>BSIT-2C</td>
                      <td>Paper</td>
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#empEditModal">
                          <i class="fa-solid fa-pen-to-square"></i> 
                        </button>
                      </td> 
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#empDeleteModal">
                          <i class="fa-solid fa-delete-left text-danger"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <th class="db-text-primary" scope="row">4</th>
                      <td>Matthew Vergel Vidal</td>
                      <td>BSIT-2C</td>
                      <td>Data Analyst</td>
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#empEditModal">
                          <i class="fa-solid fa-pen-to-square"></i> 
                        </button>
                      </td> 
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#empDeleteModal">
                          <i class="fa-solid fa-delete-left text-danger"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>


        <div class="col-xl-5">
          <div class="card bg-dark">
            <div class="card-body">

              <div class="table-task-top db-text-sec">
                <div class="py-2 ps-3">
                  <p class="m-0 fs-18">Tasks:</p>
                  <div class="bugs-container py-1 px-2 d-flex align-items-center gap-2 fs-14">
                    <i class="fa-solid fa-bug"></i>
                    Bugs
                  </div>
                </div>
              </div>

              <div class="table-responsive mt-2">
                <table class="table table-hover" style="margin-top: -24px">
                  <tbody>
                    <tr>
                      <td scope="row">
                        <input type="checkbox">
                      </td>
                      <td>
                        Sign contract for "What are conference organizers afraid of?"
                      </td>
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#taskEditModal">
                          <i class="fa-solid fa-pen-to-square"></i> 
                        </button>
                      </td> 
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#taskDeleteModal">
                          <i class="fa-solid fa-delete-left text-danger"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td scope="row">
                        <input type="checkbox">
                      </td>
                      <td>
                        Lines From Great Russian Literature? Or E-mails From My Boss
                      </td>
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#taskEditModal">
                          <i class="fa-solid fa-pen-to-square"></i> 
                        </button>
                      </td> 
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#taskDeleteModal">
                          <i class="fa-solid fa-delete-left text-danger"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                    <td scope="row">
                        <input type="checkbox">
                      </td>
                      <td>
                        Create 4 Invisible user Experiences you Never Know About
                      </td>
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#taskEditModal">
                          <i class="fa-solid fa-pen-to-square"></i> 
                        </button>
                      </td> 
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#taskDeleteModal">
                          <i class="fa-solid fa-delete-left text-danger"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                    <td scope="row">
                        <input type="checkbox" checked>
                      </td>
                      <td>
                        Flooded: One year later, assessing what was lost and what was found when a ravagin rain swept through metro Detroit
                      </td>
                      <<td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#taskEditModal">
                          <i class="fa-solid fa-pen-to-square"></i> 
                        </button>
                      </td> 
                      <td>
                        <button class="btn text-white p-0 border-0" data-bs-toggle="modal" data-bs-target="#taskDeleteModal">
                          <i class="fa-solid fa-delete-left text-danger"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>


            </div>
          </div>
        </div>
      </div>
    </section> -->
  </main>

  <!-- ========== TASK MODAL  EDIT ========== -->
  <!-- <div class="modal fade" id="taskEditModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5 db-text-sec" id="staticBackdropLabel">Edit Tasks</h1>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="" method="post">
          <div class="modal-body">
            <div class="mb-3">
              <label for="tasks-task" class="form-label db-text-sec">Tasks</label>
              <input type="text" class="form-control" id="tasks-task" name="tasks-task">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn db-bg-primary text-white">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div> -->

  <!-- ========== TASK MODAL DELETE ========== -->
  <!-- <div class="modal fade" id="taskDeleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header d-flex justify-content-between">
          <h1 class="modal-title fs-5 db-text-sec" id="staticBackdropLabel"> Delete Tasks </h1>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form action="" method="post">
          <div class="modal-body">
            <h3 class="db-text-sec text-center m-0 py-4">Are you sure you want to Delete?</h3>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-danger">Delete</button>
          </div>
        </form>

      </div>
    </div>
  </div> -->

  <!-- ========== EMPLOYEE MODAL  EDIT ========== -->
  <!-- <div class="modal fade" id="empEditModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5 db-text-sec" id="staticBackdropLabel">Edit Employee Status</h1>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="#" method="post">
          <div class="modal-body">
            <div class="mb-3">
              <label for="emp-name" class="form-label db-text-sec">Name</label>
              <input type="text" class="form-control" id="emp-name" name="emp-name">
            </div>
            <div class="mb-3">
              <label for="emp-section" class="form-label db-text-sec">Section</label>
              <input type="text" class="form-control" id="emp-section" name="emp-section">
            </div>
            <div class="mb-3">
              <label for="emp-role" class="form-label db-text-sec">Role</label>
              <input type="text" class="form-control" id="emp-role" name="emp-role">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn db-bg-primary text-white">Save</button>
          </div>
        </form>
        
      </div>
    </div>
  </div> -->

  <!-- ========== EMPLOYEE MODAL DELETE ========== -->
  <!-- <div class="modal fade" id="empDeleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header d-flex justify-content-between">
          <h1 class="modal-title fs-5 db-text-sec" id="staticBackdropLabel"> Delete Employee Status </h1>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="" method="post">
          <div class="modal-body">
            <h3 class="db-text-sec text-center m-0 py-4">Are you sure you want to Delete?</h3>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-danger">Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div> -->

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>

    /* ========== LINE CHART USERS ========== */
    const lineChart = document.getElementById('lineChart');

    new Chart(lineChart, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
          label: 'Daily Users',
          data: <?php echo json_encode($users_quantity); ?>,
          fill: false,
          borderColor: '#607EBC',
          tension: 0.1
        }]
      },
      options: {
        maintainAspectRatio: true,
        responsive: true,
        plugins: {
          legend: {
            labels: {
              color: 'white' // Color of legend text
            }
          },
          title: {
            display: true,
            text: 'User Activity Over Time', // More appropriate title for a line chart
            color: 'white' // Title color
          }
        },
        scales: {
          x: {
            ticks: {
              color: 'white' // X-axis labels color
            }
          },
          y: {
            ticks: {
              color: 'white' // Y-axis labels color
            },
            min: 0,
            max: 20
          }
        }
      }
    });

    /* ========== PIE GRAPH MOST WATCHED ========== */
    const pieGraph = document.getElementById('pieGraph');

    new Chart(pieGraph, {
      type: 'doughnut',
      data: {
        labels: [
          'Red',
          'Blue',
          'Yellow'
        ],
        datasets: [{
          label: 'Watched Genres',
          data: [300, 50, 100],
          backgroundColor: [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'
          ],
          hoverOffset: 4
        }]
      },
      options: {
        maintainAspectRatio: true,  // Ensure the chart's aspect ratio is maintained
        responsive: true,           // Allow the chart to resize with the window
        plugins: {
          tooltip: { enabled: true },
          title: {
            display: true,
            text: 'Top Viewed Categories',
            color: 'white'
          }
        },
      }
    });

    /* ========== BAR GRAPH MOVIES ========== */
    const barGraphMovies = document.getElementById('barGraphMovies');

    new Chart(barGraphMovies, {
      type: 'bar',
      data: {
        labels: [
          'Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Family',
          'Fantasy', 'History', 'Terror', 'Music', 'Mystery', 'Romance', 'Science Fiction', 'Cinema TV',
          'Thriller', 'War', 'Western', 'Action & Adventure', 'Kids', 'News', 'Reality', 'Sci-Fi & Fantasy',
          'Soap', 'Talk', 'War & Politics'
        ],
        datasets: [{
          label: 'Movie Genres',
          data: [
            120, 85, 90, 150, 100, 60, 130, 70,
            75, 50, 40, 65, 80, 110, 95, 45,
            88, 30, 20, 55, 60, 25, 35, 43,
            40, 22, 15
          ],
          backgroundColor: [
            '#607EBC', '#88A2DB', '#9BB3E0', '#A8C4EC', '#7696D1',
            '#5C74B0', '#7FA5D5', '#4F659F', '#9AB8E6', '#6B88C4',
            '#425D99', '#7B9ED3', '#8DAEE0', '#A2C3F2', '#6785C0',
            '#4A69A7', '#91AFE5', '#5775B1', '#3C5288', '#6F90CB',
            '#99B9F0', '#AECDF4', '#819FD6', '#5F79B3', '#7EA3D9',
            '#A1C1EE', '#3E5B90'
          ],
          hoverOffset: 6
        }]
      },
      options: {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
          scales: {
            y: {
              beginAtZero: true
            }
          },
          title: {
            display: true,
            text: 'No. of Movies in Genre',
            color: 'white'
          }
        },
      }
    });

    /* ========== BAR GRAPH MOVIES ========== */
    const barGraphSeries = document.getElementById('barGraphSeries');

    new Chart(barGraphSeries, {
      type: 'bar',
      data: {
        labels: [
          'Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Family',
          'Fantasy', 'History', 'Terror', 'Music', 'Mystery', 'Romance', 'Science Fiction', 'Cinema TV',
          'Thriller', 'War', 'Western', 'Action & Adventure', 'Kids', 'News', 'Reality', 'Sci-Fi & Fantasy',
          'Soap', 'Talk', 'War & Politics'
        ],
        datasets: [{
          label: 'Series Genres',
          data: [
            120, 85, 90, 150, 100, 60, 130, 70,
            75, 50, 40, 65, 80, 110, 95, 45,
            88, 30, 20, 55, 60, 25, 35, 43,
            40, 22, 15
          ],
          backgroundColor: [
            '#607EBC', '#88A2DB', '#9BB3E0', '#A8C4EC', '#7696D1',
            '#5C74B0', '#7FA5D5', '#4F659F', '#9AB8E6', '#6B88C4',
            '#425D99', '#7B9ED3', '#8DAEE0', '#A2C3F2', '#6785C0',
            '#4A69A7', '#91AFE5', '#5775B1', '#3C5288', '#6F90CB',
            '#99B9F0', '#AECDF4', '#819FD6', '#5F79B3', '#7EA3D9',
            '#A1C1EE', '#3E5B90'
          ],
          hoverOffset: 6
        }]
      },
      options: {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
          scales: {
            y: {
              beginAtZero: true
            }
          },
          title: {
            display: true,
            text: 'No. of Series in Genre',
            color: 'white'
          }
        },
      }
    });

  </script>
</body>

</html>