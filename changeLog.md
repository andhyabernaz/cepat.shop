# Changelog - Cepatshop Migration

## Summary
This changelog documents the migration from "Nextshop" to "Cepatshop" across the entire codebase.

## Renamed Directories
- `d:\cepat.shop\routes\nextshop` -> `d:\cepat.shop\routes\cepatshop`
- `d:\cepat.shop\nextshop` -> `d:\cepat.shop\cepatshop`

## Renamed Files
- `d:\cepat.shop\app\Providers\NextshopServiceProvider.php` -> `d:\cepat.shop\app\Providers\CepatshopServiceProvider.php`
- `d:\cepat.shop\database\nextshop_starter.sql` -> `d:\cepat.shop\database\cepatshop_starter.sql`
- `d:\cepat.shop\database\nextshop_starter_with_demo.sql` -> `d:\cepat.shop\database\cepatshop_starter_with_demo.sql`

## Modified Files

### Configuration
- **`d:\cepat.shop\config\app.php`**
  - Updated `APP_NAME` default to 'Cepatshop'.
  - Updated Service Provider reference to `App\Providers\CepatshopServiceProvider::class`.
- **`d:\cepat.shop\config\database.php`**
  - Updated database names to `cepatshop_starter` and `cepatshop_starter_with_demo`.
- **`d:\cepat.shop\package.json`**
  - Updated scripts to reference `cd cepatshop` instead of `cd nextshop`.

### Backend (Laravel)
- **`d:\cepat.shop\app\Providers\CepatshopServiceProvider.php`** (formerly NextshopServiceProvider.php)
  - Updated class name to `CepatshopServiceProvider`.
  - Updated default shop name config to 'Cepatshop'.
- **`d:\cepat.shop\app\Providers\RouteServiceProvider.php`**
  - Updated all route group paths from `routes/nextshop/` to `routes/cepatshop/`.
- **`d:\cepat.shop\app\Console\Commands\InstallApp.php`**
  - Updated default `--shop_name` to 'Cepatshop'.
- **`d:\cepat.shop\app\Scopes\MetaDescriptionScope.php`**
  - Updated namespace to `Silehage\Cepatshop\Scopes`.
- **`d:\cepat.shop\database\migrations\2022_11_03_173555_create_mail_configs_table.php`**
  - Updated default `from_name` to 'Cepatshop'.

### Frontend (Quasar/Vue)
- **`d:\cepat.shop\cepatshop\package.json`**
  - Updated package `name` to `cepatshop`.
  - Updated `productName` to "Cepatshop App".
- **`d:\cepat.shop\cepatshop\quasar.config.js`**
  - Updated `appId` to 'cepatshop'.
- **`d:\cepat.shop\cepatshop\src\store\index.js`**
  - Updated Vuex persisted state key to `_cepatshop__state`.
- **`d:\cepat.shop\cepatshop\src\pages\Install\InstallWelcome.vue`**
  - Updated text "Nextshop Installer" to "Cepatshop Installer".
- **`d:\cepat.shop\cepatshop\src\pages\Config\BasicConfig.vue`**
  - Updated localStorage key to `__cepatshop_theme`.
- **`d:\cepat.shop\cepatshop\src\components\MainMenu.vue`**
  - Updated external link to `https://cepatshop.my.id`.
- **`d:\cepat.shop\cepatshop\README.md`**
  - Updated title to "Cepatshop App".

### Database Content
- **`d:\cepat.shop\database\cepatshop_starter.sql`**
  - Replaced all occurrences of "Nextshop" with "Cepatshop" (case-sensitive).
  - Replaced all occurrences of "nextshop" with "cepatshop" (case-sensitive).
- **`d:\cepat.shop\database\cepatshop_starter_with_demo.sql`**
  - Replaced all occurrences of "Nextshop" with "Cepatshop" (case-sensitive).
  - Replaced all occurrences of "nextshop" with "cepatshop" (case-sensitive).
- **`d:\cepat.shop\database\demo\content.sql`**
  - Replaced all occurrences of "Nextshop" with "Cepatshop" (case-sensitive).
  - Replaced all occurrences of "nextshop" with "cepatshop" (case-sensitive).

## Testing
- Ran `php artisan test`.
  - **Result**: `Tests\Unit\ExampleTest` PASSED.
  - **Result**: `Tests\Feature\ExampleTest` FAILED with 302 Redirect (Expected behavior when app is not installed/configured).
  - **Verification**: Manually verified code integrity by temporarily bypassing `Installed` middleware, which resulted in a Database Connection Error (confirming code execution reached the controller and wasn't blocked by missing classes/files).

## Notes
- Build artifacts (`public/build`, `cepatshop/build`) still contain "nextshop" references but will be regenerated upon next build.
- `package-lock.json` files will be updated upon next `npm install`.
