<?

/**
 * A glorified global array.
 */
class mc_Options
{
    protected static $options;
    protected static $handlers;
    
    
    public static function init()
    {
        self::$options = array();
        self::$handlers = array();
    }
    
    
    /**
     * Set a value. Just like an array.
     *
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        self::$options[$key] = $value;
    }
    
    
    /**
     * Get a value. If the key isn't set, returns null.
     *
     * @param string $key
     * @return mixed|null
     */
    public static function get($key)
    {
        if (isset(self::$options[$key]))
        {
            $value = self::$options[$key];
        }
        else
        {
            $value = null;
        }
        
        if (isset(self::$handlers[$key]))
        {
            $args = func_get_args();
            $args[0] = $value;
            $value = call_user_func_array(self::$handlers[$key], $args);
        }
        
        return $value;
    }
    
    
    /**
     * Do a path join.
     * 
     * The first argument is a path option. That is, an option whose
     * key starts with "path.". This will be replaced by the value of the
     * path option and joined with the rest of the arguments using the system's
     * path separator.
     * 
     * For example...
     * <code>
     * <?
     * mc_Options::set('path.mystuff', '/home/me');
     * echo mc_Options::path('mystuff', 'things/morestuff/afile.file');
     * // -> "/home/me/things/morestuff/afile.file"
     * ?>
     * </code>
     * 
     * @param string $path_option
     * @param string $path_part
     */
    public static function path($path_option)
    {
        $key = 'path.'.$path_option;
        $base = isset(self::$options[$key]) ? self::$options[$key] : null;
        $args = func_get_args();
        
        if (isset(self::$handlers[$key]))
        {
            $args[0] = $base;
            return call_user_func_array(self::$handlers[$key], $args);
        }
        
        if (count($args) == 1)
        {
            return $base;
        }
        
        $parts = array();
        $sep = DIRECTORY_SEPARATOR;
        
        foreach (array_slice($args, 1) as $p)
        {
            $parts[] = implode($sep, array_filter(preg_split('@\\'.$sep.'\b@', $p)));
        }
        
        return implode($sep, array($base, implode($sep, $parts)));
    }
    
    
    /**
     * Set a function that returns the value for a particular key.
     * 
     * An option handler will be called with the value of the requested key and then
     * whatever arguments are passed to the mc_options::get() or mc_Options::set()
     * call after the key argument.
     * 
     * @param string $key
     * @param callback $handler
     */
    public static function set_handler($key, $handler)
    {
        self::$handlers[$key] = $handler;
    }
}

mc_Options::init();


class O extends mc_Options {}
?>