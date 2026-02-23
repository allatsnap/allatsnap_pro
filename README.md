# AllAtSnap Pro - Account Claim Website (PHP + MySQL)

## Setup
1. Upload files to your PHP hosting (InfinityFree compatible).
2. Import `schema.sql` into MySQL.
3. Update database and Cloudflare Turnstile settings in `config.php`.
4. Update `BASE_URL`, `SHORTLINK_URL`, and `TOKEN_SECRET`.
5. Login to admin panel at `/admin/login.php` (default in schema).

## Default Admin
- Username: `admin`
- Password: `ChangeMe123!`

Change this immediately after first login by updating DB with a new `password_hash()` value.

## Included Pages
- User flow: `index.php`, `watch.php`, `generate.php`, `success.php`, `login.php`, `dashboard.php`, `logout.php`
- Admin flow: `admin/login.php`, `admin/dashboard.php`, `admin/add_account.php`, `admin/manage_accounts.php`, `admin/view_claims.php`, `admin/logout.php`
- Shared core: `config.php`, `functions.php`, `schema.sql`
