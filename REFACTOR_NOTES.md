# ERP Frontend Refactor

This version completes the main frontend checklist while preserving the existing Laravel + Tailwind structure.

## Added
- Forgot-password and reset-password screens and routes.
- Profile/settings page with name, email, phone and profile-photo upload.
- Change-password form with current-password validation.
- User management screens for the superadmin: list, search/filter, create, edit and delete.
- User roles: `superadmin`, `manager`, `employee`.
- User status: `active`, `inactive`.
- Inactive accounts are blocked at login.
- First-time registration creates the initial superadmin; public registration is disabled afterward.
- Profile avatar now uses the uploaded profile photo when available.
- Product image upload preview on create/edit.
- Fixed product-create image field mismatch (`image_file`).
- Standardized select styling with the existing input component style.

## Migration
Run:

```bash
php artisan migrate
php artisan storage:link
```

The new migration adds role/status/phone/profile-photo fields to `users`.

## Frontend build
Dependencies are intentionally not included in this refactored archive. Run:

```bash
composer install
npm install
npm run build
```

For local development:

```bash
php artisan serve
npm run dev
```

## Validation note
PHP source files were syntax-checked successfully. Full Laravel migration/tests could not be executed in this environment because the PHP SQLite PDO driver is unavailable, and the uploaded `node_modules` installation could not be rebuilt in the sandbox.
