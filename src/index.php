<?php 

require '../config.php';
require '../vendor/autoload.php';

define('PAGESIZE', 25);


spl_autoload_register(function ($class) {
     // echo  __DIR__. '/lib/' . $class . '.php'; exit;
     require __DIR__. '/lib/' . $class . '.php';
});

session_start();

$config = ['settings' => [

    'addContentLengthHeader' => true,
    'displayErrorDetails' => true,    
    ],
    // 'notFoundHandler' => $handler,
    'db' => [
        'user'      => MYSQL_USER,
        'password'  => MYSQL_PASSWORD,
        'dbname'    => MYSQL_DBNAME,// MYSQL_DBNAME
        'host'      => '127.0.0.1',
    ]
];

// $app->config('debug', true);

$app = new \Slim\App($config);
// Fetch DI Container
$container = $app->getContainer();

// Register Twig View helper
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig('templates', [
        // 'cache' => 'cache'
    ]);
    
    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};


$c = $app->getContainer(); //Create Your container

//Override the default Not Found Handler
$c['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $html = $c->get('view')->render($response, 'error-404.html');

        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write($html);
    };
};


// Define app routes
$app->get('/', function ($request, $response, $args) {
    
    return $this->view->render($response, 'test.html', [
        'name' => 'Jon',
    ]);

     // return $response->write("Hello " );
});

$app->get('/admin', function ($request, $response, $args) {
    
    return $this->view->render($response, 'content.html', [
        'name' => 'Jon',
    ]);

});

$app->get('/download', function ($request, $response) {
    
    header(
        'Content-type: application/application/vnd.ms-excel; charset=utf-8');
    header(
       'Content-Disposition:  attachment;filename='.date("d-m-Y").'-export.xls');
    header(
       'Content-Transfer-Encoding: binary');
    
    $query = $_SERVER['QUERY_STRING'];

    $this->view->render($response, 'test2.html', [
        'xxx' => $query,
    ]);

    return $response;
});



$app->post('/login', function ($request, $response, $args) use ($app) {
    
    if (Helper::autorize($request->getParsedBody())) {
        return $response->withHeader('Location', '/orders/');
    };

    // return $response->write('Location false');
    return $response->withHeader('Location', '/');

});

$app->get('/clear', function ($request, $response, $args) {
        unset($_SESSION['isAutorize']);

        var_dump($_SESSION);
        $response->write("Ok" );
});

$app->get('/check', function ($request, $response, $args) {
        
        var_dump($_SESSION);
        $response->write("Ok" );
});


$app->get('/orders/[{id}]', function ($request, $response, $args) use ($app) {
    
    if ( !Helper::checkLogin() ) 
        return $this->view->render($response, 'login.html'); 

    $conf = $app->getContainer();
    $db = new DbOrder( $conf->db);

    $ret = Helper::showPage($request, $response, $args, $db, [
       'route'  => $this,
       'title'  => 'Orders',
       'url'    => 'orders',
       'details'=> 'order',   
       'orderBy' => 'id',
    ]);

      // exit;
    return $ret;
})->setName('order');

$app->get('/order/{id}', function ($request, $response, $args) use ($app) {
    
    if ( !Helper::checkLogin() ) 
        return $this->view->render($response, 'login.html'); 

    $conf = $app->getContainer();
    $db = new DbOrder( $conf->db);
// echo '<pre>';
    $order = $db->getById( $args['id'] );

    return $this->view->render($response, 'order.html', $order); 

})->setName('order');


$app->get('/change-status/{status}/{id}', function($request, $response, $args) use ($app) {


    $conf = $app->getContainer();
    $db = new DbOrder( $conf->db);

    $db->setStatus($args);
 
    echo 'redirect to /order/' . $args['id'];

    // return $response->withHeader('Location', '/order/' . $args['id'] );
});

$app->get('/log/[{id}]', function ($request, $response, $args) use ($app) {
    
    if ( !Helper::checkLogin() ) 
        return $this->view->render($response, 'login.html'); 

    $conf = $app->getContainer();
    $db = new DbLog( $conf->db);

    $ret = Helper::showPage($request, $response, $args, $db, [
       'route'   => $this,
       'title'   =>  'Лог запросов',
       'url'     => 'log',
       'details' => 'loginfo',
       'orderBy' => 'ts',
    ]  );
})->setName('order');


// Run app
$app->run();
