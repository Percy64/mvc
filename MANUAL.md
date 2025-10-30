# Manual del Framework MVC-2024

## Tabla de Contenidos
1. [Introducción](#introducción)
2. [Instalación y Configuración](#instalación-y-configuración)
3. [Arquitectura del Framework](#arquitectura-del-framework)
4. [Estructura de Directorios](#estructura-de-directorios)
5. [Enrutamiento](#enrutamiento)
6. [Controladores](#controladores)
7. [Modelos](#modelos)
8. [Vistas](#vistas)
9. [Base de Datos](#base-de-datos)
10. [Seguridad](#seguridad)
11. [Ejemplos Prácticos](#ejemplos-prácticos)
12. [Mejores Prácticas](#mejores-prácticas)
13. [Resolución de Problemas](#resolución-de-problemas)

---

## Introducción

MVC-2024 es un framework PHP personalizado diseñado para aplicaciones web siguiendo el patrón Modelo-Vista-Controlador (MVC). Está optimizado para funcionar en entornos XAMPP y utiliza un sistema de enrutamiento basado en URLs amigables.

### Características Principales
- Patrón MVC estricto
- Enrutamiento automático basado en URLs
- Autoloader personalizado para clases core
- Sistema de vistas con inyección de variables
- Capa de abstracción de base de datos con PDO
- Generación de tokens CSRF
- Manejo automático de errores 404

---

## Instalación y Configuración

### Requisitos
- PHP 7.4 o superior
- Apache con mod_rewrite habilitado
- MySQL/MariaDB
- XAMPP (recomendado)

### Instalación
1. Clona o descarga el proyecto en tu directorio de XAMPP:
   ```
   c:\xampp\htdocs\MVC-2024\
   ```

2. Configura Apache para habilitar mod_rewrite en `httpd.conf`:
   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```

3. Asegúrate de que el archivo `.htaccess` esté presente en el directorio `public/`

4. Configura la base de datos en `app/core/DataBase.php`:
   ```php
   private static $host = "localhost";
   private static $dbname = "tu_base_de_datos";
   private static $dbuser = "root";
   private static $dbpass = "";
   ```

### URL de Acceso
El framework está configurado para funcionar en:
```
http://localhost/MVC-2024/
```

---

## Arquitectura del Framework

### Flujo de Ejecución
1. **Punto de entrada**: `public/index.php`
2. **Autoloader**: Carga automática de clases core
3. **App**: Parsea la URL y determina el controlador/acción
4. **Controller**: Procesa la lógica de negocio
5. **Model**: Interactúa con la base de datos (opcional)
6. **View**: Renderiza la respuesta HTML

### Componentes Core

#### App.php
Clase principal que maneja el enrutamiento:
- Parsea URLs en formato `/controlador/accion/parametros`
- Carga controladores dinámicamente
- Maneja errores 404 automáticamente

#### Autoloader.php
Sistema de carga automática para clases core:
- Clases autoloadeables: `App`, `Controller`, `Model`, `Response`, `DataBase`
- Convierte namespaces a rutas de archivos

#### Controller.php
Clase base para todos los controladores:
- Métodos de utilidad (`path()`, `viewDir()`)
- Generación de tokens de seguridad
- Manejo de errores 404

#### Response.php
Sistema de renderizado de vistas:
- Inyección de variables en plantillas
- Validación de rutas de vistas
- Manejo de errores de vistas no encontradas

#### Model.php
Clase base para modelos:
- Métodos CRUD básicos
- Integración con DataBase
- Patrón Active Record simplificado

#### DataBase.php
Capa de abstracción de base de datos:
- Conexión singleton con PDO
- Prepared statements automáticos
- Métodos de consulta seguros

---

## Estructura de Directorios

```
MVC-2024/
├── app/
│   ├── controllers/        # Controladores de la aplicación
│   │   ├── HomeController.php
│   │   ├── SiteController.php
│   │   └── UserController.php
│   ├── core/              # Clases core del framework
│   │   ├── App.php
│   │   ├── Autoloader.php
│   │   ├── Controller.php
│   │   ├── DataBase.php
│   │   ├── Model.php
│   │   └── Response.php
│   ├── models/            # Modelos de datos
│   │   └── UserModel.php
│   └── views/             # Plantillas de vistas
│       ├── home/
│       │   ├── 404.php
│       │   └── inicio.php
│       └── inc/
│           └── head.php
├── public/                # Directorio público (document root)
│   ├── index.php         # Punto de entrada
│   ├── .htaccess         # Configuración de rewrite
│   ├── css/              # Archivos CSS
│   ├── js/               # Archivos JavaScript
│   └── img/              # Imágenes
└── .htaccess             # Bloqueo de acceso directo
```

---

## Enrutamiento

### Formato de URLs
```
http://localhost/MVC-2024/[controlador]/[accion]/[parametro1]/[parametro2]
```

### Ejemplos de Enrutamiento
| URL | Controlador | Método | Parámetros |
|-----|-------------|--------|------------|
| `/` | HomeController | actionIndex | - |
| `/home` | HomeController | actionIndex | - |
| `/home/inicio` | HomeController | actionInicio | - |
| `/user/profile/123` | UserController | actionProfile | [123] |

### Controlador y Acción por Defecto
- **Controlador por defecto**: `HomeController`
- **Acción por defecto**: `actionIndex`
- **Error 404**: Se ejecuta `action404()` automáticamente

### Configuración de Rewrite
El archivo `public/.htaccess` contiene:
```apache
<IfModule mod_rewrite.c>
  Options -Multiviews
  RewriteEngine On
  RewriteBase /MVC-2024
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
</IfModule>
```

---

## Controladores

### Estructura Básica
```php
<?php 
namespace app\controllers;
use \Controller;
use \Response;

class MiController extends Controller
{
    public function __construct()
    {
        // Inicialización del controlador
    }

    public function actionIndex()
    {
        // Acción por defecto
    }

    public function actionMiAccion($param1 = null, $param2 = null)
    {
        // Lógica de la acción
        $data = ['mensaje' => 'Hola Mundo'];
        
        Response::render($this->viewDir(__NAMESPACE__), "mi_vista", [
            "title" => "Mi Página",
            "head" => SiteController::head(),
            "data" => $data
        ]);
    }

    public function action404()
    {
        // Manejo personalizado de errores 404
        Response::render($this->viewDir(__NAMESPACE__), "404", [
            "title" => "Página no encontrada",
            "head" => SiteController::head()
        ]);
    }

    private function actionMetodoPrivado()
    {
        // Los métodos privados NO son accesibles vía URL
    }
}
```

### Métodos Importantes

#### `viewDir(__NAMESPACE__)`
Convierte el namespace del controlador en la ruta de directorio de vistas:
```php
// app\controllers\UserController -> user/
$viewDir = $this->viewDir(__NAMESPACE__);
```

#### `static::path()`
Obtiene la ruta base para assets:
```php
$basePath = static::path(); // "/MVC-2024/"
```

#### `generarToken($longitud)`
Genera tokens seguros para CSRF:
```php
$token = static::generarToken(32);
```

### Convenciones
1. **Nombres**: `{Nombre}Controller.php`
2. **Namespace**: `app\controllers`
3. **Acciones**: Métodos públicos con prefijo `action`
4. **Parámetros**: Se pasan como argumentos del método

---

## Modelos

### Estructura Básica
```php
<?php
namespace app\models;
use \Model;

class UserModel extends Model
{
    protected $table = "users";
    protected $primaryKey = "id";

    // Propiedades que corresponden a campos de la tabla
    public $id;
    public $nombre;
    public $email;
    public $created_at;

    public function __construct()
    {
        // Inicialización del modelo
    }

    // Métodos personalizados
    public function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $params = ["email" => $email];
        return DataBase::getRecord($sql, $params);
    }

    public static function usuariosActivos()
    {
        $sql = "SELECT * FROM users WHERE status = 'active'";
        return DataBase::getRecords($sql);
    }
}
```

### Métodos Heredados de Model

#### `findId($id)`
Busca un registro por ID:
```php
$user = UserModel::findId(1);
echo $user->nombre;
```

#### `getAll()`
Obtiene todos los registros:
```php
$users = UserModel::getAll();
foreach($users as $user) {
    echo $user->nombre;
}
```

#### `getColumnsNames($table)`
Obtiene nombres de columnas:
```php
$columns = UserModel::getColumnsNames('users');
```

### Uso en Controladores
```php
public function actionListar()
{
    $userModel = new UserModel();
    $users = $userModel->getAll();
    
    Response::render($this->viewDir(__NAMESPACE__), "listar", [
        "title" => "Lista de Usuarios",
        "head" => SiteController::head(),
        "users" => $users
    ]);
}
```

---

## Vistas

### Sistema de Renderizado
Las vistas se renderizan usando `Response::render()`:
```php
Response::render($viewDir, $viewName, $variables);
```

### Estructura de una Vista
```php
<!DOCTYPE html>
<html lang="es">
<head>
    <?=$head?>
    <title><?=$title?></title>
</head>
<body>
    <h1>Bienvenido <?=$nombre?></h1>
    <p><?=$mensaje?></p>
    
    <?php if(isset($users)): ?>
        <ul>
        <?php foreach($users as $user): ?>
            <li><?=$user->nombre?> - <?=$user->email?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
```

### Variables Disponibles
Las variables pasadas a `Response::render()` están disponibles directamente:
```php
// En el controlador
Response::render($viewDir, "vista", [
    "title" => "Mi Título",
    "usuario" => $usuarioObj,
    "lista" => $arrayDatos
]);

// En la vista
echo $title;        // "Mi Título"
echo $usuario->nombre;
print_r($lista);
```

### Head Común
El framework incluye un sistema para manejar el `<head>` común:
```php
// En SiteController::head()
$head = SiteController::head();

// El archivo app/views/inc/head.php contiene:
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="#PATH#css/main.css">
<script src="#PATH#js/jquery.js"></script>
```

El placeholder `#PATH#` se reemplaza automáticamente con la ruta base.

### Organización de Vistas
```
app/views/
├── controlador1/
│   ├── accion1.php
│   ├── accion2.php
│   └── 404.php
├── controlador2/
│   └── index.php
└── inc/
    ├── head.php
    ├── header.php
    └── footer.php
```

---

## Base de Datos

### Configuración
```php
// En app/core/DataBase.php
private static $host = "localhost";
private static $dbname = "mi_base_datos";
private static $dbuser = "root";
private static $dbpass = "";
```

### Métodos Disponibles

#### `query($sql, $params)`
Ejecuta consultas SELECT:
```php
$sql = "SELECT * FROM users WHERE age > :age";
$params = ["age" => 18];
$users = DataBase::query($sql, $params);
```

#### `execute($sql, $params)`
Ejecuta INSERT, UPDATE, DELETE:
```php
$sql = "INSERT INTO users (nombre, email) VALUES (:nombre, :email)";
$params = ["nombre" => "Juan", "email" => "juan@email.com"];
$affected = DataBase::execute($sql, $params);
```

#### `getRecord($sql, $params)`
Obtiene un solo registro:
```php
$sql = "SELECT * FROM users WHERE id = :id";
$params = ["id" => 1];
$user = DataBase::getRecord($sql, $params);
```

#### `getRecords($sql, $params)`
Obtiene múltiples registros:
```php
$sql = "SELECT * FROM users WHERE status = :status";
$params = ["status" => "active"];
$users = DataBase::getRecords($sql, $params);
```

### Prepared Statements
Todas las consultas usan prepared statements automáticamente:
```php
// CORRECTO - Parámetros seguros
$sql = "SELECT * FROM users WHERE email = :email";
$params = ["email" => $_POST['email']];

// INCORRECTO - Vulnerable a SQL injection
$sql = "SELECT * FROM users WHERE email = '" . $_POST['email'] . "'";
```

---

## Seguridad

### Tokens CSRF
```php
// Generar token
$token = Controller::generarToken(32);
$_SESSION['csrf_token'] = $token;

// Verificar token
if($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    throw new Exception("Token CSRF inválido");
}
```

### Prepared Statements
Todas las consultas a la base de datos usan prepared statements:
```php
// Automático en DataBase
$sql = "SELECT * FROM users WHERE id = :id";
$params = ["id" => $userId];
$user = DataBase::getRecord($sql, $params);
```

### Sanitización de URLs
El enrutamiento sanitiza automáticamente las URLs:
```php
// En App::parseUrl()
return explode("/", filter_var(rtrim($_GET["url"], "/"), FILTER_SANITIZE_URL));
```

### Protección de Directorios
Los archivos `.htaccess` protegen directorios sensibles:
```apache
# En app/.htaccess
Options - Indexes
```

---

## Ejemplos Prácticos

### Ejemplo 1: CRUD de Usuarios

#### Controlador
```php
<?php
namespace app\controllers;
use \Controller;
use \Response;
use app\models\UserModel;

class UserController extends Controller
{
    public function actionIndex()
    {
        $users = UserModel::getAll();
        
        Response::render($this->viewDir(__NAMESPACE__), "index", [
            "title" => "Lista de Usuarios",
            "head" => SiteController::head(),
            "users" => $users
        ]);
    }

    public function actionVer($id)
    {
        $user = UserModel::findId($id);
        
        if(!$user->id) {
            $this->action404();
            return;
        }
        
        Response::render($this->viewDir(__NAMESPACE__), "ver", [
            "title" => "Usuario: " . $user->nombre,
            "head" => SiteController::head(),
            "user" => $user
        ]);
    }

    public function actionCrear()
    {
        if($_POST) {
            $userModel = new UserModel();
            $sql = "INSERT INTO users (nombre, email) VALUES (:nombre, :email)";
            $params = [
                "nombre" => $_POST['nombre'],
                "email" => $_POST['email']
            ];
            
            if(DataBase::execute($sql, $params)) {
                header("Location: " . static::path() . "user");
                return;
            }
        }
        
        Response::render($this->viewDir(__NAMESPACE__), "crear", [
            "title" => "Crear Usuario",
            "head" => SiteController::head(),
            "token" => static::generarToken()
        ]);
    }
}
```

#### Modelo
```php
<?php
namespace app\models;
use \Model;
use \DataBase;

class UserModel extends Model
{
    protected $table = "users";
    protected $primaryKey = "id";
    
    public $id;
    public $nombre;
    public $email;
    public $created_at;

    public function guardar()
    {
        if($this->id) {
            // Actualizar
            $sql = "UPDATE {$this->table} SET nombre = :nombre, email = :email WHERE id = :id";
            $params = [
                "nombre" => $this->nombre,
                "email" => $this->email,
                "id" => $this->id
            ];
        } else {
            // Crear
            $sql = "INSERT INTO {$this->table} (nombre, email) VALUES (:nombre, :email)";
            $params = [
                "nombre" => $this->nombre,
                "email" => $this->email
            ];
        }
        
        return DataBase::execute($sql, $params);
    }

    public static function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $params = ["email" => $email];
        return DataBase::getRecord($sql, $params);
    }
}
```

#### Vista (user/index.php)
```php
<!DOCTYPE html>
<html lang="es">
<head>
    <?=$head?>
    <title><?=$title?></title>
</head>
<body>
    <h1>Lista de Usuarios</h1>
    
    <a href="<?=Controller::path()?>user/crear">Crear Usuario</a>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user): ?>
            <tr>
                <td><?=$user->id?></td>
                <td><?=$user->nombre?></td>
                <td><?=$user->email?></td>
                <td>
                    <a href="<?=Controller::path()?>user/ver/<?=$user->id?>">Ver</a>
                    <a href="<?=Controller::path()?>user/editar/<?=$user->id?>">Editar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
```

### Ejemplo 2: API JSON

```php
<?php
namespace app\controllers;
use \Controller;
use app\models\UserModel;

class ApiController extends Controller
{
    public function actionUsers()
    {
        header('Content-Type: application/json');
        
        $users = UserModel::getAll();
        echo json_encode($users);
    }

    public function actionUser($id)
    {
        header('Content-Type: application/json');
        
        $user = UserModel::findId($id);
        
        if(!$user->id) {
            http_response_code(404);
            echo json_encode(["error" => "Usuario no encontrado"]);
            return;
        }
        
        echo json_encode($user);
    }
}
```

---

## Mejores Prácticas

### Estructura de Código
1. **Controladores**: Solo lógica de presentación
2. **Modelos**: Lógica de negocio y acceso a datos
3. **Vistas**: Solo presentación, sin lógica compleja
4. **Validación**: Siempre validar datos de entrada
5. **Seguridad**: Usar tokens CSRF y prepared statements

### Convenciones de Nomenclatura
```php
// Controladores
class UserController extends Controller {}

// Métodos de acción
public function actionListar() {}
public function actionCrear() {}

// Modelos
class UserModel extends Model {}

// Vistas
user/listar.php
user/crear.php
```

### Manejo de Errores
```php
// En controladores
if(!$data) {
    $this->action404();
    return;
}

// En modelos
try {
    $result = DataBase::execute($sql, $params);
} catch(Exception $e) {
    error_log($e->getMessage());
    return false;
}
```

### Organización de Assets
```
public/
├── css/
│   ├── main.css
│   ├── grid.css
│   └── reset.css
├── js/
│   ├── main.js
│   └── jquery.js
└── img/
    └── logos/
```

---

## Resolución de Problemas

### Error 500 - Internal Server Error
**Causa**: mod_rewrite no habilitado
**Solución**: Habilitar mod_rewrite en Apache

### Error 404 en todas las rutas
**Causa**: .htaccess no funciona
**Solución**: Verificar AllowOverride All en httpd.conf

### Clases no encontradas
**Causa**: Namespace incorrecto
**Solución**: Verificar namespace en controladores y modelos

### Variables no definidas en vistas
**Causa**: Variables no pasadas en Response::render()
**Solución**: Verificar array de variables en el controlador

### Error de conexión a base de datos
**Causa**: Configuración incorrecta en DataBase.php
**Solución**: Verificar credenciales y nombre de base de datos

### CSS/JS no cargan
**Causa**: Rutas incorrectas
**Solución**: Verificar uso de #PATH# en head.php

---

## Extensiones y Personalización

### Agregar Nuevo Controlador
1. Crear archivo en `app/controllers/`
2. Usar namespace `app\controllers`
3. Extender `Controller`
4. Crear métodos con prefijo `action`

### Agregar Nuevo Modelo
1. Crear archivo en `app/models/`
2. Extender `Model`
3. Definir `$table` y `$primaryKey`

### Personalizar Error 404
```php
public function action404()
{
    Response::render($this->viewDir(__NAMESPACE__), "404", [
        "title" => "Página no encontrada",
        "head" => SiteController::head(),
        "mensaje" => "La página solicitada no existe"
    ]);
}
```

### Middleware Personalizado
```php
// En el constructor del controlador
public function __construct()
{
    $this->verificarAutenticacion();
}

private function verificarAutenticacion()
{
    if(!isset($_SESSION['user_id'])) {
        header("Location: " . static::path() . "auth/login");
        exit;
    }
}
```

---

## Conclusión

El framework MVC-2024 proporciona una base sólida para desarrollar aplicaciones web PHP siguiendo el patrón MVC. Su diseño simple pero potente permite un desarrollo rápido mientras mantiene la organización y seguridad del código.

Para más información o reportar problemas, consulta la documentación en el repositorio del proyecto.

---

*Manual actualizado: Octubre 2025*