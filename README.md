FastRoute - Forked
=======================================

nikic/FastRoute => burzum/FastRoute (branch: fastroute-ng) => Current Repo (branch: master)


Rationale
-----
- burzum/FastRoute (branch: fastroute-ng) adds "name" to addRoute(method, pattern, handler, name), which is returned by dispatcher on match

the benefit of the "name":
- can be used for collecting statistics
- can help identify the specific matching route for development


Usage
-----

Here's a basic usage example:

```php
<?php

require '/path/to/vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollection $r) {
    $r->addRoute('GET', '/users', 'get_all_users_handler', 'name1');
    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler', 'name2');
    // The /{title} suffix is optional
    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler', 'name3');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $name = $routeInfo[3];
        // ... call $handler with $vars
        break;
}
```

