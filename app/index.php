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
use Illuminate\Database\Capsule\Manager as Capsule;


require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
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
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// Eloquent
$container=$app->getContainer();

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['MYSQL_HOST'],
    'port'      => $_ENV['MYSQL_PORT'],
    'database'  => $_ENV['MYSQL_DB'],
    'username'  => $_ENV['MYSQL_USER'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Routes
$app->group('/login', function (RouteCollectorProxy $group) {
  $group->post('[/]', \LoginController::class . ':LoguearUsuario');
});

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
  })->add(\MiddlewareJWT::class.':verificarToken')->add(\VerificadorPerfiles::class.':VerificarPerfilSocio');

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{producto}', \ProductoController::class . ':TraerUno');
  $group->post('/{producto}', \ProductoController::class . ':ModificarUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
})->add(\MiddlewareJWT::class.':verificarToken');

$app->group('/pendientes', function (RouteCollectorProxy $group) {
  $group->get('/cocina', \ProductoController::class . ':TraerPendientesCocina')->add(\VerificadorPerfiles::class.':VerificarPerfilCocinero');
  $group->get('/cerveceria', \ProductoController::class . ':TraerPendientesCerveceria')->add(\VerificadorPerfiles::class.':VerificarPerfilCervecero');
  $group->get('/barra', \ProductoController::class . ':TraerPendientesBarra')->add(\VerificadorPerfiles::class.':VerificarPerfilBartender');
})->add(\MiddlewareJWT::class.':verificarToken');

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/{codigo}', \MesaController::class . ':TraerUno');
  $group->post('/{codigo}', \MesaController::class . ':ModificarUno');
  $group->put('/cerrarMesa/{codigo}', \MesaController::class . ':CerrarMesa')->add(\VerificadorPerfiles::class.':VerificarPerfilSocio');
  $group->post('[/]', \MesaController::class . ':CargarUno')->add(\VerificadorPerfiles::class.':VerificarPerfilSocio');
})->add(\MiddlewareJWT::class.':verificarToken')->add(\VerificadorPerfiles::class.':VerificarPerfilMozo');

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->get('/{pedido}', \PedidoController::class . ':TraerUno');
  $group->post('/cambiarEstado', \PedidoController::class . ':CambiarEstadoPedido')->add(\VerificadorPerfiles::class.':VerificarPerfilMozo');
  $group->post('[/]', \PedidoController::class . ':CargarUno')->add(\VerificadorPerfiles::class.':VerificarPerfilSocio');
})->add(\MiddlewareJWT::class.':verificarToken');

$app->get('[/]', function (Request $request, Response $response) {
    $response->getBody()->write("TP Programación III Mercedes Vera Sotelo");
    return $response;
});

$app->run();
