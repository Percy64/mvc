# MVC Project Instructions for AI Agents

## Architecture Overview
This is a custom PHP MVC framework designed for XAMPP environments. The application uses a single-entry point (`public/index.php`) with URL rewriting to route requests through the `App` class dispatcher.

### Core Components
- **App.php**: Central router that parses URLs and dispatches to controllers
- **Autoloader.php**: Custom class loader for core classes (`App`, `Controller`, `Model`, `Response`, `DataBase`, `Session`) + namespace support for `app\` classes
- **Controller.php**: Base controller with path resolution, 404 handling, and session management
- **Response.php**: Static view renderer with variable injection and validation
- **Model.php**: Base model with static methods for basic CRUD operations via PDO
- **DataBase.php**: Singleton PDO wrapper with prepared statements and transaction support
- **Session.php**: Session management class for user authentication

## URL Routing Pattern
URLs follow the pattern: `/controller/action/param1/param2`
- Maps to `{Controller}Controller->action{Action}($param1, $param2)`
- Default controller: `HomeController`
- Default action: `actionIndex`
- Missing controllers/actions redirect to `action404`
- **Critical**: URL base is `/MVC-2024` in `.htaccess` but project folder is `MVC`

Example: `/mascota/perfil/123` → `MascotaController->actionPerfil("123")`

## Naming Conventions
- **Controllers**: `{Name}Controller.php` in `app/controllers/` namespace `app\controllers`
- **Actions**: Methods prefixed with `action` (e.g., `actionIndex`, `actionCrear`)
- **Views**: Located in `app/views/{controller_name}/` (lowercase, no "Controller" suffix)
- **Models**: `{Name}.php` in `app/models/` namespace `app\models` extending base `Model`

## View Rendering Pattern
Controllers must use this exact pattern:
```php
$viewDir = $this->viewDir('app\\controllers\\');
Response::render($viewDir, 'view_name', [
    'mascotas' => $mascotas,
    'session' => $this->session
]);
```

The `viewDir()` method strips namespace and "Controller" to get view directory path.

## Path Management & Assets
- Controllers call `static::path()` to set base URL in `self::$ruta`
- `SiteController::head()` loads `app/views/inc/head.php` and replaces `#PATH#` placeholder
- CSS/JS assets are in `public/css/` and `public/js/` directories
- Use path replacement pattern: `#PATH#css/main.css` becomes `/MVC-2024/css/main.css`

## Database Configuration
Database settings in `DataBase.php`:
- Database: "mascotas_db" (actual current DB name)
- Connection: PDO with persistent connections, UTF-8, timezone -03:00
- Error handling: Exceptions with custom error messages

## Database Patterns
### Model Implementation
Models extend base `Model` class and use static methods:
```php
// In app/models/Mascota.php
public static function findById($id) {
    $sql = "SELECT * FROM mascotas WHERE id_mascota = ?";
    return DataBase::getRecord($sql, [$id]);
}
```

### Database Methods
- `DataBase::getRecord($sql, $params)`: Single record as array
- `DataBase::getRecords($sql, $params)`: Multiple records as array
- `DataBase::execute($sql, $params)`: Insert/Update/Delete, returns row count
- `DataBase::obtenerUltimoId()`: Get last insert ID

## Session Management
Controllers initialize session in constructor:
```php
public function __construct() {
    $this->session = new Session();
}
```

## Development Setup
- **Environment**: XAMPP with Apache mod_rewrite enabled
- **Document Root**: `public/` directory (assets served directly)
- **Error Reporting**: Enabled in `public/index.php` for development
- **URL Rewrite**: Base `/MVC-2024` in `public/.htaccess` but project folder is `MVC`

## Key Implementation Patterns
1. **Controllers**: Must extend `Controller`, use namespace `app\controllers`, inject `$this->session`
2. **Actions**: Public methods with `action` prefix; private methods not URL-accessible
3. **Views**: Variables extracted to template scope, no array access needed
4. **Models**: Use namespace `app\models`, implement static database methods
5. **Path Resolution**: Always call `viewDir('app\\controllers\\')` in controllers
6. **404 Handling**: Automatic fallback to `action404` for missing routes

## Security & Validation
- **CSRF**: Use `Controller::generarToken()` for form protection
- **Database**: All queries use PDO prepared statements
- **View Variables**: Validated with regex before extraction in `Response::render()`
- **Directory Protection**: `.htaccess` blocks direct access to `app/` directory

## File Upload & Assets
- **Upload Directory**: `assets/images/mascotas/` for pet photos
- **QR Codes**: Generated in `assets/qr/` directory
- **Public Assets**: CSS in `public/css/`, JS in `public/js/`
- **Asset Loading**: Use `SiteController::head()` for common CSS/JS with path replacement