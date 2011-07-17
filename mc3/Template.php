<?
require_once('mc3/Options.php');
/**
 * A simple, php file-based templating system.
 * 
 * This templating system doesn't have its own language. It uses PHP which was
 * designed for templating and does it well and fast. This class handles variable 
 * scope, performs output buffering and provides a simple mechanism for including
 * other templates.
 * 
 * Path resolution is, by default, the same as whatever you are used to with PHP
 * on your OS. You can set a base directory by calling mc_Options::set('path.templates', '/path/to/templates')
 * then all the paths will be relative to this directory.
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
 * echo $tpl->process();
 * // -> I am a template file. I like to throw rocks at the moon. How about you?
 * ?>
 * </code>
 */
class mc_Template
{
    protected $path;
    protected $vars = array();
    protected $filters;
    
    /**
     * Create a new template.
     *
     * @param string Path to a PHP file.
     */
    public function __construct($path)
    {
        $path = O::path('templates', $path);
        $_path = realpath($path);
        
        if (!$_path)
        {
            throw new Exception('No such file "'.$path.'"');
        }
        
        $this->path = $_path;
        $this->vars = array();
        $this->filters = array();
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
     * Evaluate the template file and run any registered filters.
     */
    function process()
    {
        $_data = $this->to_string();
        
        foreach ($this->filters as $f)
		{
		    $_data = $f->run($_data);
		}
		
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
    public function insert($path, $vars=array(), $tpl_class='mc_Template')
    {
        $vars = array_merge($this->vars, $vars);
        $tpl = new $tpl_class($path);
        
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
    
    
    /**
     * Add a filter to be run after the template is evaluated.
     * 
     * Filters are run in the order they are added.
     *
     * @param mc_TemplateFilter $filter
     */
    public function add_filter($filter)
    {
        $this->filters[] = $filter;
    }
}


/**
 * Filters take an input string, perform some fitlering, and return a string.
 */
abstract class mc_TemplateFilter
{
    abstract public function run($data);
}


/**
 * HTML-aware whitespace removal filter
 */
class mc_WhitespaceTemplateFilter
{
    // thanks smarty team
	public function run($source)
	{
		// Pull out the script blocks
		 preg_match_all("!<script[^>]+>.*?</script>!is", $source, $match);
		 $_script_blocks = $match[0];
		 $source = preg_replace("!<script[^>]+>.*?</script>!is",
										'@@@MANDK:TRIM:SCRIPT@@@', $source);
		 // Pull out the pre blocks
		 preg_match_all("!<pre>.*?</pre>!is", $source, $match);
		 $_pre_blocks = $match[0];
		 $source = preg_replace("!<pre>.*?</pre>!is",
										'@@@MANDK:TRIM:PRE@@@', $source);
	
		 // Pull out the textarea blocks
		 preg_match_all("!<textarea[^>]+>.*?</textarea>!is", $source, $match);
		 $_textarea_blocks = $match[0];
		 $source = preg_replace("!<textarea[^>]+>.*?</textarea>!is",
										'@@@MANDK:TRIM:TEXTAREA@@@', $source);
	
		 // remove all leading spaces, tabs and carriage returns NOT
		 // preceeded by a php close tag.
		 $source = trim(preg_replace('@[ \t][ \t]+@', ' ', $source));
		 $source = trim(preg_replace('/((?<!\?>)\n)[\s]+/', '\1', $source));
         
		 // replace script blocks
		 $this->trimwhitespace_replace("@@@MANDK:TRIM:SCRIPT@@@",$_script_blocks, $source);
         
         
		 // replace pre blocks
		 $this->trimwhitespace_replace("@@@MANDK:TRIM:PRE@@@",$_pre_blocks, $source);
	
		 // replace textarea blocks
		 $this->trimwhitespace_replace("@@@MANDK:TRIM:TEXTAREA@@@",$_textarea_blocks, $source);
	
		 return $source;
	}
	
	private function trimwhitespace_replace($search_str, $replace, &$subject)
	{
		 $_len = strlen($search_str);
		 $_pos = 0;
		 for ($_i=0, $_count=count($replace); $_i<$_count; $_i++)
			  if (($_pos=strpos($subject, $search_str, $_pos))!==false)
					$subject = substr_replace($subject, $replace[$_i], $_pos, $_len);
			  else
					break;
	}
}
?>