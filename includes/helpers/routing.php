<?php

/**
 * The array to store route configurations.
 * @var array<string>
 */
$routes = [];

/**
 * Define a route for HTTP GET requests.
 *
 * @param string $segment The route segment.
 * @param string|null $controller The controller associated with the route.
 */
if (!function_exists('route_get')) {
    function route_get($segment, $controller = null)
    {
        global $routes;

        $routes['GET'][] = [
            'segment' => '/' . ltrim($segment, '/'),
            'controller' => $controller
        ];
    }
}

/**
 * Define a route for HTTP POST requests.
 *
 * @param string $segment The route segment.
 * @param string|null $controller The controller associated with the route.
 */
if (!function_exists('route_post')) {
    function route_post($segment, $controller = null)
    {
        global $routes;
        $routes['POST'][] = [
            'segment' => '/' . ltrim($segment, '/'),
            'controller' => $controller
        ];
    }
}



/**
 * Initialize and process defined routes.
 */
if (!function_exists('route_init')) {
    function route_init()
    {

        global $routes;
        $GET_ROUTES = isset($routes['GET']) ? $routes['GET'] : [];
        $POST_ROUTES = isset($routes['POST']) ? $routes['POST'] : [];

       

        if ($_SERVER["REQUEST_METHOD"] === "GET") {

            foreach ($GET_ROUTES as $rget) {
                if (segment() == $rget['segment']) {
                    include base_path(config('router.api') . "/" . $rget['controller']);
                }
            }
        }

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
   
                foreach ($POST_ROUTES as $rpost) {
                    if (segment() == $rpost['segment']) {

                        include base_path(config('router.api') . "/" . $rpost['controller']);
                    }
                }

             
            }
       
    }
}

/**
 * Generate a URL for a given segment.
 *
 * @param string $segment The URL segment.
 * @return string The generated URL.
 */
if (!function_exists('url')) {
    function url($segment)
    {
        $url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $url .= $_SERVER['HTTP_HOST'];
        return $url . '/' . public_() . '/' . ltrim($segment, '/');
    }
}
/**
 * Get the current URL segment.
 *
 * @return string The current URL segment.
 */
if (!function_exists('segment')) {
    function segment()
    {
        // Get the request URI and remove the leading slash
        $segment = ltrim($_SERVER['REQUEST_URI'], '/');
        
        // Remove any query parameters
        $removeQueryParam = explode('?', $segment)[0];
        
        // Find the position of 'api/' and get everything after it
        $apiSegmentPosition = strpos($removeQueryParam, 'api/index.php/');
        
        // If 'api/' is found, extract everything after it
        if ($apiSegmentPosition !== false) {
            $pathAfterApi = substr($removeQueryParam, $apiSegmentPosition + strlen('api/index.php/'));
            return '/' . ltrim($pathAfterApi, '/');
        }
        
        // If 'api/' is not found, return an empty string or any default value
        return '/';
    }
}

?>