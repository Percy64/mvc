# MVC Project Instructions for AI Agents

## Architecture Overview
This is a custom PHP MVC framework designed for XAMPP environments. The application uses a single-entry point (`index.php` in root) with URL rewriting to route requests through the `App` class dispatcher.

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
- **Critical**: URL base is `/MVC/public/` in `.htaccess` (check current RewriteBase setting)

Example: `/mascota/perfil/123` → `MascotaController->actionPerfil("123")`

## Naming Conventions
- **Controllers**: `{Name}Controller.php` in `app/controllers/` namespace `app\controllers`
- **Actions**: Methods prefixed with `action` (e.g., `actionIndex`, `actionCrear`)
- **Views**: Located in `app/views/{controller_name}/` (lowercase, no "Controller" suffix)
- **Models**: `{Name}.php` in `app/models/` namespace `app\models` extending base `Model`

## View Rendering Pattern
Controllers use two patterns for view rendering:

**Pattern 1: Dynamic viewDir (recommended for consistency)**
```php
$viewDir = $this->viewDir('app\\controllers\\');
Response::render($viewDir, 'view_name', [
    'mascotas' => $mascotas,
    'session' => $this->session
]);
```

**Pattern 2: Fixed viewsDir property (used in MascotaController, UsuarioController)**
```php
protected $viewsDir = 'mascotas/';  // Set in controller class
Response::render($this->viewsDir, 'view_name', [
    'mascotas' => $mascotas,
    'session' => $this->session
]);
```

The `viewDir()` method strips namespace and "Controller" to get view directory path.

## Path Management & Assets
- Controllers call `static::path()` to set base URL in `self::$ruta`
- `SiteController::head()` loads `app/views/inc/head.php` and replaces `#PATH#` placeholder
- CSS/JS assets are in `public/css/` and `public/js/` directories
- Use path replacement pattern: `#PATH#css/main.css` becomes `/MVC/public/css/main.css`

## Database Configuration
Database settings in `DataBase.php` with flexible configuration:
- **Default**: Database "mascotas_db", localhost, root user
- **Config File**: Load from `app/config/db.php` (see `db.php.example`)
- **Environment Variables**: Override with `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_PORT`
- Connection: PDO with UTF-8, 5-second timeout, exception mode
- Priority: Environment vars > config file > defaults

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
- **Entry Point**: `index.php` in root (not public/), handles routing via .htaccess
- **Document Root**: `app/public/` for static assets (CSS/JS), root .htaccess handles routing
- **Error Reporting**: Enabled in `index.php` for development
- **URL Rewrite**: Root .htaccess redirects to `index.php?url=...`, `app/public/.htaccess` uses `/MVC/public/` base
- **Test Environment**: `test.php` provides system status and routing tests
- **Database Import**: Use `mascotas.sql` to set up the database structure
- **Configuration**: Copy `app/config/*.example` files to remove `.example` extension

## Development Workflow
- **Starting Development**: Access via `http://localhost/MVC/` (root .htaccess routes to index.php)
- **Static Assets**: Access via `http://localhost/MVC/public/` (CSS/JS served directly)
- **Database Setup**: Import `mascotas.sql` into `mascotas_db` database
- **Configuration**: Set up `app/config/db.php` from `db.php.example` if needed
- **File Organization**: Controllers in `app/controllers/`, models in `app/models/`, views in `app/views/`
- **Asset Management**: Public assets (CSS/JS) in `app/public/`, uploaded files in `assets/`
- **Debugging**: Error reporting enabled; check Apache error logs for routing issues
- **System Check**: Use `test.php` to verify all components are working

## Key Implementation Patterns
1. **Controllers**: Must extend `Controller`, use namespace `app\controllers`, inject `$this->session`
2. **Actions**: Public methods with `action` prefix; private methods not URL-accessible
3. **Views**: Variables extracted to template scope, no array access needed
4. **Models**: Use namespace `app\models`, implement static database methods
5. **Path Resolution**: Always call `viewDir('app\\controllers\\')` in controllers
6. **404 Handling**: Automatic fallback to `action404` for missing routes

## Security & Validation
- **CSRF**: Use `$this->generateCsrf()` in controllers for form tokens, or `Controller::generarToken()` for custom security tokens
- **Database**: All queries use PDO prepared statements
- **View Variables**: Validated with regex before extraction in `Response::render()`
- **Directory Protection**: `.htaccess` blocks direct access to `app/` directory

## File Upload & Assets
- **Upload Directory**: `assets/images/mascotas/` for pet photos
- **QR Codes**: Generated in `assets/qr/` directory
- **Public Assets**: CSS in `app/public/css/`, JS in `app/public/js/`
- **Asset Loading**: Use `SiteController::head()` for common CSS/JS with path replacement