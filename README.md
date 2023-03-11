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






### A Note on HEAD Requests

The HTTP spec requires servers to [support both GET and HEAD methods][2616-511]:

> The methods GET and HEAD MUST be supported by all general-purpose servers

To avoid forcing users to manually register HEAD routes for each resource we fallback to matching an
available GET route for a given resource. The PHP web SAPI transparently removes the entity body
from HEAD responses so this behavior has no effect on the vast majority of users.

However, implementers using FastRoute outside the web SAPI environment (e.g. a custom server) MUST
NOT send entity bodies generated in response to HEAD requests. If you are a non-SAPI user this is
*your responsibility*; FastRoute has no purview to prevent you from breaking HTTP in such cases.

Finally, note that applications MAY always specify their own HEAD method route for a given
resource to bypass this behavior entirely.

### Credits

This library is based on a router that [Levi Morrison][levi] implemented for the Aerys server.

A large number of tests, as well as HTTP compliance considerations, were provided by [Daniel Lowrey][rdlowrey].


[2616-511]: http://www.w3.org/Protocols/rfc2616/rfc2616-sec5.html#sec5.1.1 "RFC 2616 Section 5.1.1"
[blog_post]: http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
[levi]: https://github.com/morrisonlevi
[rdlowrey]: https://github.com/rdlowrey
