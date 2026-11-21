# Deploying CineVault to InfinityFree

InfinityFree is free hosting with PHP and MySQL, the same pieces XAMPP runs locally.
CineVault fits it well: the videos stream from archive.org, so only the site files
(about 70 MB, mostly posters) are uploaded.

## What you need

- `CineVault-deploy/htdocs/`: the site, ready to upload (built from `FINAL-PROJECT-ITEC60`
  without the `database` folder). Its `includes/config.php` has debug off and the
  TMDB key, with four database values left for you to paste.
- `CineVault-deploy/cinevault-database.sql`: the whole library, members and settings,
  exported so a shared host accepts it.
- An FTP program such as FileZilla (the online File Manager works too, but is slow for
  776 files).

To rebuild both from your local copy later, see "Updating the live site" below.

## Steps

1. **Create the hosting account** at infinityfree.com. Pick a free subdomain (for
   example `cinevault.rf.gd`). Activation can take a few minutes.
2. **Create the database.** Control Panel > MySQL Databases > create one (e.g.
   `cinevault`). Note the four values it shows:
   - MySQL host name (like `sql123.infinityfree.com`)
   - MySQL user name (like `if0_12345678`)
   - MySQL password (your hosting account password, in the Client Area under Account Details)
   - Database name (like `if0_12345678_cinevault`)
3. **Import the data.** Control Panel > phpMyAdmin > pick the database > Import >
   choose `cinevault-database.sql` > Go.
   - The export has no database views (InfinityFree doesn't allow them), so it
     imports as is. If an earlier import stopped partway, just import again: it
     replaces the tables it already made.
4. **Fill in `htdocs/includes/config.php`** with the four values from step 2, in place
   of the `PASTE_..._HERE` text.
5. **Upload.** In FileZilla, connect with the FTP details from the Control Panel
   (host `ftpupload.net`, your `if0_...` user name and password). Open the server's
   `htdocs` folder, delete the sample files InfinityFree put there, and upload
   everything inside `CineVault-deploy/htdocs` into it (including the hidden
   `.htaccess` files).
6. **Turn on HTTPS.** Control Panel > SSL Certificates > get the free certificate for
   your subdomain, then open the site with `https://`.
7. **Secure the accounts right away.** The imported data still has the local test
   passwords. Sign in as the admin, open Settings > Change your password, and change
   it. Do the same for any other admin, and archive test members you don't want
   on the live site.

## Check these on the live site

Free hosts sometimes limit these, so try each once:

- **Search TMDB** on Add Movie: if it can't reach TMDB, the host blocks outgoing
  requests. The site still works for members; add new titles on your local copy and
  re-import the database instead.
- **Free full movie** search on Add Movie: same as above, for archive.org.
- **Forgot password** email: free hosts often block email. Sign in and sign up don't
  need email, only password resets do.
- Play a movie and an episode, and check Continue Watching on Home.

## Limits to know

- About 50,000 page hits a day, plenty for a class project.
- No command line, so `database/seed-free-movies.php` can't run there. Import your
  local database instead.
- Pages with many titles (Series, Home) are large, so the first load can be slow.

## Updating the live site

- Changed code: upload just the changed files over FTP.
- Changed library or settings: export again (below) and re-import in phpMyAdmin.
  Re-importing replaces the live data, including new members and watch history, so
  avoid it once real members use the site.

To rebuild the export locally (Git Bash):

```
M=/c/XAMPP-Projects/mysql/bin
SKIP="--ignore-table=db_finalproject.tbl_login_attempts --ignore-table=db_finalproject.tbl_remember_tokens --ignore-table=db_finalproject.tbl_payment"
{ echo "SET FOREIGN_KEY_CHECKS=0;"
  echo 'DROP TABLE IF EXISTS `tbl_movie_series_all`, `tbl_movie_series_genre_all`;'
  $M/mysqldump.exe -uroot --skip-comments --no-data db_finalproject
  $M/mysqldump.exe -uroot --skip-comments --no-create-info --skip-triggers --complete-insert $SKIP db_finalproject
  echo "SET FOREIGN_KEY_CHECKS=1;"
} | sed -E 's/DEFINER=`[^`]+`@`[^`]+` ?//g; s/SQL SECURITY DEFINER/SQL SECURITY INVOKER/g; /^\/\*!50013 *\*\//d' > cinevault-database.sql
```

The `sed` removes any `root@localhost` owner lines, which shared hosts reject.

If your local database still has the `tbl_movie_series_all` tables and views from
`update-2026-11-archive.sql`, run `database/update-2026-11-no-views.sql` once first.
Archived titles and genres then live in `tbl_movie_series_archived` and
`tbl_movie_series_genre_archived`.
