<?php 

require '../vendor/autoload.php';



$config = ['settings' => [
    'addContentLengthHeader' => false,
]];


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


$app->get('/test', function ($request, $response, $args) {
    
    return $this->view->render($response, 'content.html', [
      'entries'  => [ [ 'title'  => 'Garri Potter'],
        [ 'title'  => 'Adventuries'],
        [ 'title'  => 'Tri Tolstjaka'],
        [ 'title'  => 'Buratino'],
      ]
    ]  );
});


// Run app
$app->run();
