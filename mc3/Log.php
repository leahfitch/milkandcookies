<?

require_once('mc3/Options.php');

class mc_Log
{
    const DEBUG = 1;
    const INFO = 2;
    const WARN = 3;
    const ERROR = 4;
    
    static $fp;
    static $level = 2;
    static $level_strings = null;
    
    private $name = '';
    
    public static function setup($path, $level=2)
    {
        self::$level_strings = array(
            self::DEBUG => 'DEBUG',
            self::INFO => 'INFO',
            self::WARN => 'WARN',
            self::ERROR => 'ERROR'
        );
        
        self::$fp = fopen($path, 'a+');
        self::$level = array_search($level, self::$level_strings);
    }
    
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function debug()
    {
        $args = func_get_args();
        $this->log(self::DEBUG, $args);
    }
    
    public function info()
    {
        $args = func_get_args();
        $this->log(self::INFO, $args);
    }
    
    public function warn()
    {
        $args = func_get_args();
        $this->log(self::WARN, $args);
    }
    
    public function error()
    {
        $args = func_get_args();
        $this->log(self::ERROR, $args);
    }
    
	public function log($level, $args)
    {
        if ($level < self::$level)
        {
            return;
        }
        
        if (count($args) > 1)
        {
            foreach ($args as $k => $v)
            {
                if (is_array($v) || is_object($v))
                {
                    $args[$k] = print_r($v, true);
                }
            }
            $str = call_user_func_array('sprintf', $args);
        }
        else
        {
            $str = array_shift($args);
            
            if (is_array($str) || is_object($str))
            {
                $str = print_r($str, true);
            }
        }
        
        $str = date('c').' '.self::$level_strings[$level].' ['.$this->name.'] '.$str."\n";
        fwrite(self::$fp, $str);
    }
}

?>
