# Movie Website Dashboard

CineVault is a free streaming site for **classic movies and TV series**, built with PHP, MySQL, Bootstrap 5 and DataTables. Every title is a full-length film or episode that is in the public domain or openly licensed, streamed from the Internet Archive. Admins manage movies and series, genres, seasons and episodes from a responsive dashboard.

## Features

- **Add, Edit, Delete Movies/Series**
- **Genre Management** (multiple genres per movie/series)
- **Season & Episode Management** (for series)
- **Responsive Dashboard UI** (Bootstrap 5)
- **DataTables Integration** for searching, sorting, and pagination
- **Modal-based CRUD** for a smooth user experience
- **Cascading Deletes** (removing a movie/series also removes its seasons and episodes)
- **Secure Database Operations** (uses prepared statements or input sanitization)
- **Tooltips and Truncated Descriptions** for better UX
- **Search TMDB** on the add form: title, year, runtime, rating, genres, cast, description, poster and title logo fill in automatically
- **Free full movies**: pick a public domain film from the Internet Archive instead of uploading a video. It streams inside CineVault's own player
- **Continue Watching**: the player remembers where each member stopped, resumes from there, and the home page lists unfinished titles (and the next episode of a series)

## Technologies Used

- PHP (Backend)
- MySQL (Database)
- Bootstrap 5 (Frontend)
- DataTables (Table UI)
- jQuery (Frontend scripting)

## Setup Instructions

1. **Clone the repository** into your XAMPP `htdocs` folder:
   ```bash
   git clone https://github.com/SmuleColics/Movie-Website.git
   ```

2. **Import the database** (in phpMyAdmin or the MySQL command line):
   - Create a database named `db_finalproject`.
   - Import `FINAL-PROJECT-ITEC60/ROMANCE_DOWNLOAD/COMEDY_DOWNLOAD/db_finalproject.sql`.
   - Then import `FINAL-PROJECT-ITEC60/database/update-2026-11.sql`. It adds the tables and columns the old dump is missing.
   - Then import `FINAL-PROJECT-ITEC60/database/update-2026-11-free-membership.sql`. CineVault is free now: accounts work right after sign up, with no payment or approval.
   - Then import `FINAL-PROJECT-ITEC60/database/update-2026-11-remember-me.sql` for the "Remember me" option on sign in.
   - Then import `FINAL-PROJECT-ITEC60/database/update-2026-11-favorite-genres.sql` (favorite genres saved per account), `update-2026-11-home-carousel.sql` and `update-2026-11-movie-series-carousels.sql` (carousel titles) , `update-2026-11-admin-notifications.sql` and `update-2026-11-member-notifications.sql` (read and unread notifications for admins and members), then `update-2026-11-tmdb.sql` (TMDB import).
   - Then import `update-2026-11-classics.sql`. CineVault is now a free classic-film site: this removes the old sample titles (they only had short clips) and adds the backdrop and featured-spot columns for the carousels and banners. Then import `update-2026-11-continue-watching.sql` (saved watch positions).
   - Fill the library (after step 3, because it needs the TMDB key):
     ```
     C:\XAMPP-Projects\php\php.exe FINAL-PROJECT-ITEC60\database\seed-free-movies.php
     ```
     It adds full-length public domain movies (horror, noir, comedy, westerns, sci-fi, silent films, cartoons and Blender open movies) and classic TV series, then picks the titles for the carousels and banners. Details, posters and backdrops come from TMDB, and the videos stream from the Internet Archive, so nothing large is downloaded. A movie is only added if the archive.org copy is at least 80% of its real runtime, so no trailers or clips get in. Running it again skips titles that are already there.

3. **Create your settings file:**
   - Copy `FINAL-PROJECT-ITEC60/includes/config.example.php` to `config.php` in the same folder.
   - Fill in your MySQL login and a Gmail App Password for the reset and approval emails.
   - For the TMDB import, get a free API key at themoviedb.org (Settings, then API) and put it in `tmdb_api_key`.
   - `config.php` is ignored by git, so your passwords never reach GitHub.

4. **Run locally:**
   - Start Apache and MySQL in XAMPP.
   - Website: `http://localhost/Movie-Website/FINAL-PROJECT-ITEC60/LANDING-PAGE/LandingPageMovie.php`
   - Admin: `http://localhost/Movie-Website/FINAL-PROJECT-ITEC60/DASHBOARD-HTML/admin-signin.php`

## Folder Structure

```
FINAL-PROJECT-ITEC60/
  DASHBOARD-HTML/   admin pages (movies, series, genres, accounts)
  DASHBOARD-CSS/    admin styles
  LANDING-PAGE/     public landing page, sign in, sign up, payment, password reset
  MovieWebsite/     member pages (browse, search, play, profile)
  includes/         database connection, security setup, config
  database/         schema updates
  libs/             FPDF for the payment report
```

## Credits

- [Bootstrap](https://getbootstrap.com/)
- [DataTables](https://datatables.net/)
- [FontAwesome](https://fontawesome.com/)
- Movie and series data and images: [TMDB](https://www.themoviedb.org/). This product uses the TMDB API but is not endorsed or certified by TMDB.
- Free public domain films and classic TV: [Internet Archive](https://archive.org/)

## License

This project is open source and available under the [MIT License](LICENSE).

---

**Enjoy managing your movie and series collection!**
