<?php
namespace Razer\Http\API;

use FFI\Exception;
use Razer\Http\Request\Request;

class API extends Request
{

    public static function isApiRequest()
    {
        return strpos(strip_tags($_SERVER['REQUEST_URI']), 'api') !== false;
    }

    protected function requestRequiresAuth()
    {
        if($this->isGet())
        {
            return strpos(strip_tags($_SERVER['REQUEST_URI']), 'username') !== false;
        }
        if($this->isPost())
        {
            return isset($_POST['username']);
        }
    }

    public function handleApiAuth()
    {
        if(!class_exists('App\Models\API\API'))
        {
            throw new Exception('API Model not defined. Please run php manage make:api');
        }

    }
}