<?

/**
 * A Handler could not be found for a path.
 */
class mc_HandlerNotFoundException extends Exception {}


/**
 * A simple recursive tree with path argument parsing.
 */
abstract class mc_Handler
{
    /**
     * The default handle method called if the handler itself is matched
     * by a path.
     */
    abstract public function handle();
    
    /**
     * Create a new mc_Handler ("Can't touch this!")
     */
    public function __construct()
    {
        $this->exposed_methods = array();
        $this->children = array();
    }
    
    /**
     * Declare some methods of the handler that are "exposed", or may be called
     * using the mc_Handler::call.
     * 
     * The $methods argument is an array where the key is the name of the method
     * to be exposed and the value is a regular expression or the empty string.
     * The regular expression can be used to capture arguments from a path.
     * Matched sub-patterns are mapped to positional arguments in the order they are
     * matched. For example:
     * 
     * <code>
     * <?
     * class ExampleHandler extends mc_Handler
     * {
     *     public function __construct()
     *     {
     *         parent::__construct();
     *         
     *         $this->expose(array(
     *             'foo' => '',
     *             'bar' => '@^([a-zA-Z0-9\s]+)/?(\d+)?$@'
     *         ));
     *     }
     *     
     *     public function foo()
     *     {
     *         return 'foo!';
     *     }
     *     
     *     public function bar($astring, $anumber=23)
     *     {
     *         return 'I '.$astring.' '.$anumber.' times.';
     *     }
     * }
     * 
     * $handler = new ExampleHandler();
     * echo $handler->call('foo'); // -> "foo!"
     * echo $handler->call('bar', 'ate monkey brains'); // -> "I ate monkey brains 23 times."
     * echo $handler->call('bar', 'granola/3650'); // -> "I ate granola 3650 times."
     * ?>
     * </code>
     *
     * @param array $methods
     */
    public function expose($methods)
    {
        $this->exposed_methods = array_merge($this->exposed_methods, $methods);
    }
    
    /**
     * Add child handlers that may be looked up with mc_Handler::get_child.
     * 
     * The children argument is an array where the key is a symbolic name for
     * the child (generally mapped to part of a path like method names) and the 
     * value is the name of a handler class.
     * <code>
     * <?
     * class ParentHandler extends mc_Handler
     * {
     *     public function __construct()
     *     {
     *         parent::__construct();
     *         
     *         $this->add_children(array(
     *             'baby' => 'ChildHandler'
     *         ));
     *     }
     * }
     * 
     * class ChildHandler extends mc_Handler
     * {
     *     public function __construct()
     *     {
     *         parent::__construct();
     *         
     *         $this->expose(array(
     *             'foo' => ''
     *         ));
     *     }
     *     
     *     public function foo()
     *     {
     *         return 'foo!';
     *     }
     * }
     * 
     * $handler = new ParentHandler();
     * $handler = $handler->get_child('baby');
     * echo $handler->call('foo'); // -> "foo!"
     * ?>
     * </code>
     * 
     * @param array $children
     */
    public function add_children($children)
    {
        $this->children = array_merge($this->children, $children);
    }
    
    /**
     * Find out if this handler has an exposed method with the given name.
     *
     * @param string $name
     * @return bool
     */
    public function has_method($name)
    {
        return isset($this->exposed_methods[$name]);
    }
    
    
    /**
     * Call an exposed method.
     * 
     * @param string $name The name of the exposed method
     * @param string $path An optional path to be matched against the argument regex for the handler.
     */
    public function call($name, $path='')
    {
        if (!isset($this->exposed_methods[$name]))
        {
            throw new mc_HandlerNotFoundException();
        }
        
        $pattern = $this->exposed_methods[$name];
        
        if (!$path && !$pattern)
        {
            return call_user_func(array($this, $name));
        }
        
        if (!$pattern)
        {
            throw new mc_HandlerNotFoundException();
        }
        
        $matches = array();
        
		if (preg_match($pattern, $path, $matches))
		{
			if (count($matches) > 1)
			{
				$args = array();
				
				for ($i=1; $i<count($matches); $i++)
				{
					$args[] = $matches[$i];
				}
			}
			else
			{
				$args = null;
			}
			
			if ($args)
			{
				return call_user_func_array(array($this, $name), $args);
			}
			else
			{
				return call_user_func(array($this, $name));
			}
		}
		
		throw new mc_HandlerNotFoundException();
    }
    
    
    /**
     * Find out if this handler has a child handler with the given name.
     * 
     * @param string $name
     * @return bool
     */
    public function has_child($name)
    {
        return isset($this->children[$name]);
    }
    
    
    /**
     * Get the child with the given name.
     *
     * @param string $name
     * @return mc_Handler
     * @throws mc_HandlerNotFoundException
     */
    public function get_child($name)
    {
        if (!isset($this->children[$name]))
        {
            throw new mc_HandlerNotFoundException();
        }
        
        $cls = $this->children[$name];
        
        return new $cls;
    }
    
    
    /**
     * Find a method or child handler of this handler given a path
     *
     * @param string $path
     * @return string
     */
    public function route($path)
    {
        if (!$path || $path == '/')
        {
            return $this->handle();
        }
        
        if ($path[0] == '/')
        {
            $path = substr($path, 1);
        }
        
        if (substr($path, -1) != '/')
        {
            $path .= '/';
        }
        
        return $this->_route(explode('/', $path));
    }
    
    /**
     * Called internally to route a path that's already been split.
     */
    protected function _route($parts)
    {
        $next = str_replace('.', '_', array_shift($parts));
        
        if (!$next)
        {
            return $this->handle();
        }
        
        if ($this->has_child($next))
        {
            $child = $this->get_child($next);
            return $child->_route($parts);
        }
        
        if ($this->has_method($next))
        {
            return $this->call($next, implode('/', $parts));
        }
        
        throw new mc_HandlerNotFoundException();
    }
}
?>