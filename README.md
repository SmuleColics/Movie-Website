# Movie Website Dashboard

This project is a **Movie/Series Management Dashboard** built with PHP, MySQL, Bootstrap 5, and DataTables. It allows admins to manage movies and series, including genres, seasons, episodes, and more, with a modern, responsive UI.

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

3. **Create your settings file:**
   - Copy `FINAL-PROJECT-ITEC60/includes/config.example.php` to `config.php` in the same folder.
   - Fill in your MySQL login and a Gmail App Password for the reset and approval emails.
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

## License

This project is open source and available under the [MIT License](LICENSE).

---

**Enjoy managing your movie and series collection!**
