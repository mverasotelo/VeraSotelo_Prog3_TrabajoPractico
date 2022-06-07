<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';

require_once './controllers/UsuarioController.php';

require_once './controllers/ProductoController.php';

require_once './controllers/LoginController.php';

require_once './middlewares/VerificadorPerfiles.php';

require_once './middlewares/VerificadorUsuario.php';

require_once './middlewares/MiddlewareJWT.php';

require_once './middlewares/AutentificadorJWT.php';


// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// // Set base path
// $app->setBasePath('/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/login', function (RouteCollectorProxy $group) {
  $group->post('[/]', \LoginController::class . ':LoguearUsuario');
});

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
  })->add(\MiddlewareJWT::class.':verificarToken');

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{producto}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno')->add(\VerificadorPerfiles::class.':VerificarPerfilSocio');
})->add(\MiddlewareJWT::class.':verificarToken');

// $app->group('/mesas', function (RouteCollectorProxy $group) {
//   $group->get('[/]', \UsuarioController::class . ':TraerTodos');
//   $group->post('[/]', \UsuarioController::class . ':ChequearUno');
// })->add(\Logger::class.':verificarCredencialesJson');

// $app->group('/pedidos', function (RouteCollectorProxy $group) {
//   $group->get('[/]', \UsuarioController::class . ':TraerTodos');
//   $group->post('[/]', \UsuarioController::class . ':ChequearUno') -> add(\Verificadora::class . ':verificarUsuario');
// });

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Slim Framework 4 PHP");
    return $response;
});

$app->run();
