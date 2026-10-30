# Marwadi University EventHub

Responsive PHP and MySQL event portal based on the three supplied desktop designs. Campus, profile, and event photography is stored locally from the supplied visual references. The interface is a development demo and is not an official university service.

## Run with Laragon

1. Start Apache and MySQL in Laragon.
2. Keep this project at `C:\laragon\www\mueventhub` (or rename the folder and update `config/app.php` `BASE_URL`).
3. Open phpMyAdmin, import `database/eventhub_db.sql`. It creates the `eventhub_db` schema and its tables without dropping existing EventHub data. Importing again leaves existing tables and records in place.
4. If Laragon uses a different MySQL account, set `EVENTHUB_DB_HOST`, `EVENTHUB_DB_PORT`, `EVENTHUB_DB_NAME`, `EVENTHUB_DB_USER`, and `EVENTHUB_DB_PASS` in the Apache environment, or edit the defaults in `config/app.php`.
5. Seed the demonstration accounts/events once from PowerShell: `& 'C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe' 'C:\laragon\www\mueventhub\setup\seed.php'`. The seeder is CLI-only and will not run over the web.
6. Open `http://localhost/mueventhub/`.

Requires PHP 8.1+, MySQL/MariaDB, PDO MySQL, and Apache with rewrite not required. No Composer or Node setup is needed. Laragon PHP may be found under `C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe` if `php` is not available in PowerShell.

## Demo sign-in

- Admin: `ashishraunksmp@gmail.com` / `Ashish@03`
- Admin: `meenalpatro@gmail.com` / `Meenal@27`
- Student accounts can register and sign in only with an email ending in `@marwadiuniversity.ac.in`.

Both supplied demo accounts are administrators. Students can self-register at `/mueventhub/register.php`; administrators can also create student accounts from **Admin Panel → Users**. Both paths enforce the university email domain and store a hashed password. Administrators can create separate committee accounts and assign events to each one. A committee account can view its assigned events and manage applications for those events only; it cannot open admin routes. The admin panel has no role-change action. Existing users keep their current roles when the committee role is enabled.

The seed script hashes the initial passwords with PHP. Both demo accounts have the administrator role, as requested. Do not expose the setup script on a public server.

## Included

- Student public homepage, event search and category filtering, event detail, login and logout.
- Student dashboard, applications, profile, password settings, notifications, calendar, volunteering and cultural activity pages.
- Admin dashboard with database-backed activity charts and color-coded statistic cards; event and opportunity management; event/volunteer application review; student, committee, and admin account create/edit/password reset/activation; committee event assignments; announcement create/edit/delete; cultural activity registration management; volunteer-hour edit/verify/unverify/delete; searchable, paginated admin lists, CSV exports, and date-filtered reports.
- PDO, prepared statements, escaped output, role-protected routes, CSRF validation, session ID regeneration, login throttling, and unique event application constraints.
- Desktop composition follows the supplied student home, student dashboard, and admin dashboard references. Campus, Meenal profile, and event photos are local assets and work offline. Layout stacks for smaller screens.

## Local checks

From PowerShell, run PHP lint checks with:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { & 'C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe' -l $_.FullName }
```

Import the SQL and open the site locally to verify database connection and login. The PHP lint command above checks all application files; database-backed workflows should be walked through after importing the schema and seed data. Local images work offline; the optional Google Font falls back to system sans-serif if offline.
