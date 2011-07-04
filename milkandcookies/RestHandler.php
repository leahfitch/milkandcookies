<?

require_once('milkandcookies/Handler.php');

/**
 * Quickly create RESTful web services or web sites.
 * 
 * Create a base rest handler for all your resources like this:
 * <code>
 * class MyRestHandler extends mc_RestHandler
 * {
 *     public function __construct()
 *     {
 *         parent::__construct();
 *         
 *         $this->add_children(array(
 *             'foos' => 'FoosRestHandler'
 *         ));
 *     }
 * }
 * </code>
 * Then implement whichever methods you want available for a resource.
 * See the complete list at the end of the RestHandler class definition.
 * <code>
 * class FoosRestHandler extends mc_RestHandler
 * {
 *     public function __construct()
 *     {
 *         parent::__construct();
 *         $this->foos = array('foo1', 'foo2');
 *     }
 *     
 *     public function GET()
 *     {
 *         return $this->json_response(200, $this->foos);
 *     }
 *     
 *     
 *     public function GET_one($id)
 *     {
 *         if (!isset($this->foos[$id]))
 *         {
 *             throw new mc_HandlerNotFoundException;
 *         }
 *         
 *         return $this->json_response(200, $this->foos[$id]);
 *     }
 * }
 * </code>
 */
class mc_RestHandler extends mc_Handler
{
    public $version = '';
    private $request = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->handlers = array();
    }
    
    public function handle()
    {
        return $this->json_response(200, array('version' => $this->version));
    }
    
    public function call($name, $path='')
    {
        $handler = $this->get_child($name);
        
        $this->request = array($_SERVER['REQUEST_METHOD'], $name, $path);
        
        $parts = array_filter(explode('/', $path));
        
        if (count($parts) > 3)
        {
            throw new mc_HandlerNotFoundException();
        }
        
        $fn = $_SERVER['REQUEST_METHOD'];
        
        if (count($parts) == 1)
        {
            $fn .= '_one';
        }
        else if (count($parts) == 2)
        {
            $fn .= '_'.$parts[1];
        }
        else if (count($parts) == 3)
        {
            $fn .= '_'.$parts[1].'2';
            $parts = array($parts[0], $parts[2]);
        }
        else
        {
            if (isset($_GET['ids']))
            {
                $fn .= '_some';
                $parts[] = $_GET['ids'];
            }
        }
        
        try
        {
            if (!method_exists($handler, $fn))
            {
                return $this->error_response(404);
            }
            
            return call_user_func_array(array($handler, $fn), $parts);
        }
        catch (Exception $e)
        {
            return $this->error_response(500, $e->getMessage());
        }
    }
    
    public function response($status, $content_type='text/plain', $body=null)
    {
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
        
        header($protocol.' '.$status.' '.$GLOBALS['http_status_codes'][$status]);
        header('Content-Type: '.$content_type);
        
        if ($body !== null)
        {
            return $body;
        }
    }
    
    
    public function error_response($status, $message=null)
    {
        if (is_array($message))
        {
            $error_object = $message;
        }
        else
        {
            $error_object = array($message);
        }
        
        return $this->json_response($status, array('errors' => $error_object));
    }
    
    public function json_response($status, $obj)
    {
        return $this->response($status, 'application/json; charset=utf-8', json_encode($obj));
    }
    
    // Override these methods to create your resources.
    
    // GET /foo
    public function GET()
    {
        throw new mc_HandlerNotFoundException();
    }
    
    // GET /foo/123
    public function GET_one($id)
    {
        throw new mc_HandlerNotFoundException();
    }
    
    // POST /foo
    public function POST()
    {
        throw new mc_HandlerNotFoundException();
    }
    
    // PUT /foo/123
    public function PUT_one($id)
    {
        throw new mc_HandlerNotFoundException();
    }
    
    // DELETE /foo
    public function DELETE()
    {
        throw new mc_HandlerNotFoundException();
    }
    
    // DELETE /foo/123
    public function DELETE_one($id)
    {
        throw new mc_HandlerNotFoundException();
    }
    
    /*
    A relation like "GET /foo/123/bar" is handled like this:
    public function GET_bar($id)
    
    And one like "DELETE /foo/123/bar/789" is handled like this:
    public function DELETE_bar2($id, $other_id)
    */
}

$GLOBALS['http_status_codes'] = array(
	100 => 'Continue',
	101 => 'Switching Protocols',
	102 => 'Processing',
	200 => 'OK',
	201 => 'Created',
	202 => 'Accepted',
	203 => 'Non-Authoritative Information',
	204 => 'No Content',
	205 => 'Reset Content',
	206 => 'Partial Content',
	207 => 'Multi-Status',
	208 => 'Already Reported',
	226 => 'IM Used',
	300 => 'Multiple Choices',
	301 => 'Moved Permanently',
	302 => 'Found',
	303 => 'See Other',
	304 => 'Not Modified',
	305 => 'Use Proxy',
	306 => 'Reserved',
	307 => 'Temporary Redirect',
	400 => 'Bad Request',
	401 => 'Unauthorized',
	402 => 'Payment Required',
	403 => 'Forbidden',
	404 => 'Not Found',
	405 => 'Method Not Allowed',
	406 => 'Not Acceptable',
	407 => 'Proxy Authentication Required',
	408 => 'Request Timeout',
	409 => 'Conflict',
	410 => 'Gone',
	411 => 'Length Required',
	412 => 'Precondition Failed',
	413 => 'Request Entity Too Large',
	414 => 'Request-URI Too Long',
	415 => 'Unsupported Media Type',
	416 => 'Requested Range Not Satisfiable',
	417 => 'Expectation Failed',
	422 => 'Unprocessable Entity',
	423 => 'Locked',
	424 => 'Failed Dependency',
	426 => 'Upgrade Required',
	500 => 'Internal Server Error',
	501 => 'Not Implemented',
	502 => 'Bad Gateway',
	503 => 'Service Unavailable',
	504 => 'Gateway Timeout',
	505 => 'HTTP Version Not Supported',
	506 => 'Variant Also Negotiates (Experimental)',
	507 => 'Insufficient Storage',
	508 => 'Loop Detected',
	510 => 'Not Extended'
);

?>
