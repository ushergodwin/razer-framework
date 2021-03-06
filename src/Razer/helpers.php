<?php
use League\BooBoo\BooBoo;
use Razer\Http\CRSF\CRSF;
use Razer\Http\Redirect\Redirect;
use Razer\Http\Request\Request;
use Razer\Http\Response\Alert\Alert;
use Razer\Http\Response\Response;
use Razer\Password\Password;
use Razer\Views\Views;

function dotEnvPath()
{
    $base_path = $_SERVER['DOCUMENT_ROOT'];

    if (strpos($base_path, 'public') !== false) {
        $base_path = str_replace('public', '', $_SERVER['DOCUMENT_ROOT']);
        $base_path = substr($base_path, 0, strlen($base_path) - 1);
    }

    return $base_path;
}

$dotenv = Dotenv\Dotenv::createImmutable(dotEnvPath());
$dotenv->safeLoad();

//exception handling
$booboo = new BooBoo([new League\BooBoo\Formatter\HtmlTableFormatter()]);

$booboo->register(); // Registers the handlers


if(!function_exists('env')) {
    /**
     * Get the environment settings
     */
	function env(string $key, $default = null) {
		return isset($_ENV[$key]) ? $_ENV[$key]: $default;
	}

}

if (!function_exists('dd')) {
    function dd()
    {
        $args = func_get_args();
        call_user_func_array('dump', $args);
        die();
    }
}

if (!function_exists('d')) {
    function d()
    {
        $args = func_get_args();
        call_user_func_array('dump', $args);
    }
}

if(!function_exists('url'))
{
    /**
    * @static base_url
    * @return string The a full main url
    * eg http://bluefaces.tech/
    */
    function url(string $url = '') {
        $base = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
        $base .= "://".$_SERVER['HTTP_HOST']."/";
        !empty(trim($url)) ? $base .= $url : $base;
        return $base;
    }
} 


if(!function_exists('array_to_object'))
{
    /**
     * Convert an array to an Object
     *
     * @param array $array
     * @return object
     */
    function array_to_object(array $array) {
        return json_decode(json_encode($array), FALSE);
    }
}



if(!function_exists('object_to_array'))
{
    /**
     * Object to array
     *
     * @param mixed $data
     * @return array
     */
    function object_to_array($data)
    {
        if (is_array($data) || is_object($data))
        {
            $result = [];
            foreach ($data as $key => $value)
            {
                $result[$key] = (is_array($data) || is_object($data)) ? object_to_array($value) : $value;
            }
            return $result;
        }
        return $data;
    }
}



if(!function_exists('session'))
{
    /**
     * Get|set session kesys
     *
     * @param string|array $key The sesion key to get or an associative array of key and value to set in a session
     * @param mixed $default The deafult value to return if the key is not found
     * @return mixed
     */
    function session($key, $default =NULL) {
        if (is_array($key)) {
            foreach($key as $session_key => $value) {
                $_SESSION[$session_key] = $value;
            }
            return;
        }
        if (!isset($_SESSION[$key])) {
            return $default;
        }

        return $_SESSION[$key];
    }
}



if(!function_exists('asset'))
{
    /**
     * Access app assets
     *
     * @param string $asset
     * @return string asset url
     */
    function asset(string $asset){
        return url($asset);
    }
}


if(!function_exists('route'))
{   
    /**
     * Get uri using roote name
     *
     * @param string $name
     * @return string
     */
    function route(string $name)
    {
        if(!isset($_SESSION[$name]))
        {
            throw new Exception
            ("Error Processing Route. The route name {$name} is not registered.\n
            Call the name method on a route request to register its name",
            
            1);
            
        }
        return url(session($name));
    }
}

if(!function_exists('request'))
{
    /**
     * Http Request helper
     *
     * @return \Razer\Http\Request\Request
     */
    function request()
    {
        return new Request();
    }
}


if(!function_exists('render'))
{
    /**
     * Return an HttpResponse rendering a request file with optional data parsed 
     *
     * @param string $view
     * @param array $context
     * @return void HttpResponse
     */
    function render(string $view, array $context)
    {
        $view = str_replace('.', '/', $view);
        Views::render($view, $context);
        if(isset($_SESSION['responseMessage']))
        {
            unset($_SESSION['responseMessage']);
        }
    }
}


if(!function_exists('redirect'))
{
        
    /**
     * Send an Http Redirect
     * @param string $url redirect url
     * @return \Razer\Http\Redirect\Redirect
     */
    function redirect(string $url = '') {
        $redirect = new Redirect();
        $redirect->url = $url;
        return $redirect;
    }
}


if(!function_exists('old'))
{
    /**
     * Get the previously submitted data after a request as a requet of calling redirect()->back()->withInput()
     *
     * @param string $key
     * @return string
     */
    function old(string $key = '')
    {
        $old = isset($_SESSION['responseData']) ? session('responseData') : [];
        $old_key = array_keys($old);
        if(!empty($old_key))
        {
            $old_len = count($old_key);
            $last_key = $old_key[$old_len - 1];
            if($key == $last_key)
            {
                unset($_SESSION['responseData']);
            }
        }
        return isset($old[$key]) ? $old[$key] : '';

    }
}

if(!function_exists('csrf_field'))
{
    function csrf_field()
    {
        $token_id = CRSF::crsfTokenId();
        $token = CRSF::crsfTokenValue();
        return "<input type='hidden' name='$token_id' value ='$token'>";
    }
}

if(!function_exists('method_field'))
{
    function method_field(string $method)
    {
        $method = strtoupper($method);
        return "<input type='hidden' name='_method' value ='$method'>";
    }
}

if(!function_exists('crsf'))
{
    /**
     * CRSF TOKEN ID
     *
     * @return string
     */
    function crsf()
    {
        return $_SESSION['token_id'];
    }
}

if(!function_exists('password'))
{
    
    /**
     * Password helper
     *
     * @return \Razer\Password\Password
     */
    function password()
    {
        return new Password();
    }
}

if(!function_exists('response'))
{
    /**
     * Response
     * @return \Razer\Http\Response\Response
     */
    
    function response()
    {
        return new Response();
    }
}



if(!function_exists('alert'))
{
     /**
     * Boostrap 4 Alerts
     *
     * @return \Razer\Http\Response\Alert\Alert
     */
    function alert()
    {
    return new Alert(); 
    }
}

if(!function_exists('_token'))
{
    /**
    * Get the CRSF Token
    */
    function _token()
    {
        return CRSF::crsfTokenValue();
    }
}


if(!function_exists('public_path'))
{
    /**
     * Get the app public path
     *@param string $path
     * @return string public path
     */
    function public_path($path = '')
    {
        $path = empty(trim($path)) ? '/public' : $path;
        return BASE_PATH . $path;
    }
}

if(!function_exists('storage_path'))
{
    /**
     * Get the app storage path
     *@param string $path
     * @return string storage path
     */
    function storage_path($path = '')
    {
        $path = empty(trim($path)) ? '/storage/app' : "/storage/{$path}";
        return BASE_PATH . $path;
    }
}

if(!function_exists('resource_path'))
{
    /**
     * Get the views path
     *@param string $path
     * @return string resource path
     */
    function resource_path($path = '')
    {
        $path = empty(trim($path)) ? "/resources/views" : "/resources/{$path}";
        return BASE_PATH .$path;
    }
}

if(!function_exists('base_path'))
{
    /**
     * Get the app's absolue base path
     *@param string $path
     * @return string
     */
    function base_path($path = '')
    {
        return empty(trim($path)) ? BASE_PATH : BASE_PATH . "/{$path}";
    }
}
