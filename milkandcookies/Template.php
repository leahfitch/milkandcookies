<?

/**
 * A simple, php file-based templating system.
 * 
 * This templating system doesn't have its own language. It uses PHP which was
 * designed for templating and does it well and fast. This class handles variable 
 * scope, performs output buffering and provides a simple mechanism for including
 * other templates.
 * 
 * Path resolution is, by default, the same as whatever you are used to with PHP
 * on your OS. You can set a base directory by calling mc_Template::set_template_directory().
 * Then all the paths will be relative to this directory.
 * 
 * A note about scope: variables set directly on a template are available within
 * the template file. These variables are also available to any included templates.
 * The template itself is executed in an mc_Template instance method so it can access
 * any methods of the template instance using $this.
 * 
 * Here's an example...
 *
 * <code>
 * // foo.tpl.php
 * I am a template file. I like to <?= $activity ?>. How about you?
 * </code>
 * 
 * And processing...
 * <code>
 * <?
 * $tpl = new mc_Template();
 * $tpl->activity = 'throw rocks at the moon';
 * echo $tpl->to_string();
 * // -> I am a template file. I like to throw rocks at the moon. How about you?
 * ?>
 * </code>
 */
class mc_Template
{
    private $path;
    private $vars;
    private static $tpl_dir = '';
    
    /**
     * Set the base directory used to resolve paths.
     * 
     * @param string $path
     */
    public static function set_template_directory($path)
    {
        self::$tpl_dir = $path;
    }
    
    /**
     * Create a new template.
     *
     * @param string Path to a PHP file.
     */
    public function __construct($path)
    {
        $path = self::$tpl_dir.DIRECTORY_SEPARATOR.$path;
        $_path = realpath($path);
        
        if (!$_path)
        {
            throw new Exception('No such file "'.$path.'"');
        }
        
        $this->path = $_path;
        $this->vars = array();
    }
    
    
    /**
     * Evaluate the template file and return the result.
     * 
     * @param string
     */
    public function to_string()
    {
        $_data = file_get_contents($this->path);
        extract($this->vars);
		ob_start();
		eval('?>'.$_data.'<?');
		$_data = ob_get_contents();
		ob_end_clean();
		
		return $_data;
    }
    
    
    /**
     * Evaluates the given template using this templates scope and any 
     * additional variables. Usually this method is called from a template
     * file to insert the result of evaluating another template file.
     * 
     * @param string $path
     * @param array $vars
     */
    public function insert($path, $vars=array())
    {
        $vars = array_merge($this->vars, $vars);
        $tpl = new mc_Template($path);
        
        foreach ($vars as $k => $v)
        {
            $tpl->$k = $v;
        }
        
        echo $tpl->to_string();
    }
    
    
    public function __get($k)
    {
        return $this->vars[$k];
    }
    
    
    public function __set($k, $v)
    {
        $this->vars[$k] = $v;
    }
}

?>