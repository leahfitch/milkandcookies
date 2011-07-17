<?
require_once('mc3/Options.php');

class mc_ComponentNotFoundException extends Exception
{
}


class mc_ComponentRegistry
{
    public static $components;
    
    public static function init()
    {
        self::$components = array();
    }
    
    public static function register($name, $cls_name)
    {
        self::$components[$name] = $cls_name;
    }
    
    public static function add(array $components)
    {
        foreach ($components as $k => $v)
        {
            self::register($k, $v);
        }
    }
    
    public static function get($name)
    {
        if (!isset(self::$components[$name]))
        {
            throw new mc_ComponentNotFoundException('No component found called "'.$name.'"');
        }
        
        $cls_name = self::$components[$name];
        
        if (!class_exists($cls_name))
        {
            require_once(O::path('components', $cls_name.'.php'));
        }
        
        return new $cls_name;
    }
}

mc_ComponentRegistry::init();

?>
