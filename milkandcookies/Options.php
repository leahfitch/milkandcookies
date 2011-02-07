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
     * Set a value or values.
     * 
     * If the first argument is an array it is assumed to be a map and used to
     * update the options map overwriting any existing options with the same key.
     * Otherwise this method sets a single option.
     *
     * @param string|array $key
     * @param mixed $value
     */
    public static function set($key)
    {
        if (is_array($key))
        {
            self::$options = array_merge(self::$options, $key);
        }
        else
        {
            if (func_num_args() == 1)
            {
                return;
            }
            
            self::$options[$key] = func_get_arg(1);
        }
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
        $base = isset(self::$options[$key]) ? self::$options[$key] : '';
        $args = func_get_args();
        
        $parts = array();
        $sep = DIRECTORY_SEPARATOR;
        
        if (count($args) > 1)
        {
            foreach (array_slice($args, 1) as $p)
            {
                $parts[] = implode($sep, array_filter(preg_split('@\\'.$sep.'\b@', $p)));
            }
        }
        
        if (isset(self::$handlers[$key]))
        {
            return call_user_func_array(self::$handlers[$key], array($base, implode($sep, $parts)));
        }
        else if (count($args) == 1)
        {
            return $base;
        }
        
        return implode($sep, array($base, implode($sep, $parts)));
    }
}

mc_Options::init();


class O extends mc_Options {}
?>