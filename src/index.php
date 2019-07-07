<?php 

require '../vendor/autoload.php';

spl_autoload_register(function ($class) {
     // echo  __DIR__. '/lib/' . $class . '.php'; exit;
     require __DIR__. '/lib/' . $class . '.php';
});

session_start();

$config = ['settings' => [
    'addContentLengthHeader' => false,
    'displayErrorDetails' => true,    
],
    'notFoundHandler' => $handler,];


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


$app->post('/login', function ($request, $response, $args) use ($app) {
    
    if (Helper::autorize($request->getParsedBody())) {
        return $response->withHeader('Location', '/test');
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


$app->get('/test', function ($request, $response, $args) {
    
    if ( !Helper::checkLogin() ) 
        return $this->view->render($response, 'login.html'); 
    
    return $this->view->render($response, 'content.html', [
      'entries'  => [ [ 'title'  => 'Garri Potter'],
        [ 'title'  => 'Adventuries'],
        [ 'title'  => 'Tri Tolstjaka'],
        [ 'title'  => 'Buratino'],
        [ 'title'  =>   Helper::checkLogin() ? 'true' : 'false'  ]
      ]
    ]  );
});




// Run app
$app->run();
