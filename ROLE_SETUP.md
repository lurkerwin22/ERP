# User roles and Superadmin setup

The ERP uses three roles:

- `superadmin` — full access, including user management.
- `manager` — business operations, without user administration.
- `employee` — operational access; user administration is unavailable.

## First Superadmin

The first account created through `/register` is assigned the `superadmin` role. Public registration is disabled once a user exists.

If the database already contains users from before role support was added, promote the intended administrator explicitly:

```bash
php artisan app:make-superadmin your-email@example.com
```

The command also activates the account.

## Protection

User CRUD routes are protected by the `role:superadmin` middleware. The sidebar also hides the Users menu from non-superadmins.

The application prevents the currently signed-in superadmin from removing their own superadmin role or deactivating/deleting themselves, and it prevents the last remaining superadmin from being demoted or deleted.
