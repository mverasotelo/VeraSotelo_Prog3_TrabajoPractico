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
require_once './controllers/EncuestaController.php';
require_once './controllers/RegistroController.php';
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
  $group->post('[/]', \ProductoController::class . ':CargarUno');
  $group->post('/cargarCsv', \ProductoController::class . ':LeerCsv');
  $group->post('/{producto}', \ProductoController::class . ':ModificarUno');
})->add(\MiddlewareJWT::class.':verificarToken');;

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/{codigo}', \MesaController::class . ':TraerUno');
  $group->post('/{codigo}', \MesaController::class . ':ModificarUno');
  $group->put('/mesaServida/{codigo}', \MesaController::class . ':cambiarAMesaServida')->add(\VerificadorPerfiles::class.':VerificarPerfilMozo');
  $group->put('/cerrarMesa/{codigo}', \MesaController::class . ':CerrarMesa')->add(\VerificadorPerfiles::class.':VerificarPerfilSocio');
  $group->post('[/]', \MesaController::class . ':CargarUno')->add(\VerificadorPerfiles::class.':VerificarPerfilSocio');
})->add(\MiddlewareJWT::class.':verificarToken')->add(\VerificadorPerfiles::class.':VerificarPerfilMozo');

$app->group('/comandas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->post('[/]', \PedidoController::class . ':CargarUno')->add(\VerificadorPerfiles::class.':VerificarPerfilMozo');
})->add(\MiddlewareJWT::class.':verificarToken');

//El cliente ingresa el código de la mesa junto con el número de pedido
$app->group('/consultarMiPedido', function (RouteCollectorProxy $group) {
  $group->get('/{codigoPedido}', \PedidoController::class . ':TraerUno');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->post('[/]', \PedidoController::class . ':AgregarProducto');
  $group->get('/todos', \PedidoController::class . ':TraerTodos')->add(\VerificadorPerfiles::class.':VerificarPerfilCocinero');
  $group->get('/pendientes/cocina', \PedidoController::class . ':TraerPendientesCocina')->add(\VerificadorPerfiles::class.':VerificarPerfilCocinero');
  $group->get('/pendientes/cerveceria', \PedidoController::class . ':TraerPendientesCerveceria')->add(\VerificadorPerfiles::class.':VerificarPerfilCervecero');
  $group->get('/pendientes/barra', \PedidoController::class . ':TraerPendientesBarra')->add(\VerificadorPerfiles::class.':VerificarPerfilBartender');
  $group->get('/listosparaservir', \PedidoController::class . ':TraerListosParaServir')->add(\VerificadorPerfiles::class.':VerificarPerfilMozo');
  $group->put('/preparar/cocina', \PedidoController::class . ':PrepararCocina')->add(\VerificadorPerfiles::class.':VerificarPerfilCocinero');
  $group->put('/preparar/barra', \PedidoController::class . ':PrepararBarra')->add(\VerificadorPerfiles::class.':VerificarPerfilBartender');
  $group->put('/preparar/cerveceria', \PedidoController::class . ':PrepararCerveceria')->add(\VerificadorPerfiles::class.':VerificarPerfilCervecero');
  $group->put('/pedidolisto/cocina', \PedidoController::class . ':PedidoListoCocina')->add(\VerificadorPerfiles::class.':VerificarPerfilCocinero');
  $group->put('/pedidolisto/barra', \PedidoController::class . ':PedidoListoBarra')->add(\VerificadorPerfiles::class.':VerificarPerfilBartender');
  $group->put('/pedidolisto/cerveceria', \PedidoController::class . ':PedidoListoCerveceria')->add(\VerificadorPerfiles::class.':VerificarPerfilCervecero');
})->add(\MiddlewareJWT::class.':verificarToken')->add(\VerificadorPerfiles::class.':VerificarPerfilMozo');

$app->group('/registros', function (RouteCollectorProxy $group) {
  $group->get('[/]', \RegistroController::class . ':TraerTodos');
  $group->get('/{idEmpleado}', \RegistroController::class . ':TraerRegistrosPorEmpleado');
})->add(\MiddlewareJWT::class.':verificarToken')->add(\VerificadorPerfiles::class.':VerificarPerfilSocio');;

$app->group('/encuestas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \EncuestaController::class . ':TraerTodos');
  $group->get('/{empleado}', \EncuestaController::class . ':TraerUno');
  $group->post('[/]', \EncuestaController::class . ':CargarUno');
})->add(\MiddlewareJWT::class.':verificarToken')->add(\VerificadorPerfiles::class.':VerificarPerfilSocio');

$app->group('/descargarListaEmpleados', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':DescargarListaEmpleados');
});

$app->group('/descargarCsvProductos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':DescargarCsv');
});

$app->get('[/]', function (Request $request, Response $response) {
    $response->getBody()->write("TP Programación III Mercedes Vera Sotelo");
    return $response;
});

$app->run();
