<?

class mc_AuthorizationException extends Exception
{
}

class mc_Bouncer
{
    protected static $roles;
    
    
    public static function init()
    {
        self::$roles = array();
    }
    
    
    public static function set($name, $value)
    {
        self::$roles[$name] = $value;
    }
    
    
    public static function get($name)
    {
        if (!isset(self::$roles[$name]))
        {
            throw new mc_AuthorizationException;
        }
        
        return self::$roles[$name];
    }
    
    
    public static function enforce($roles)
    {
        foreach ($roles as $r)
        {
            if (!isset(self::$roles[$r]))
            {
                throw new mc_AuthorizationException;
            }
        }
    }
}

mc_Bouncer::init();

?>
