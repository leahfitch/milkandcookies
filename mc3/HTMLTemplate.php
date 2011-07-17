<?
require_once('mc3/Template.php');
require_once('mc3/Shrinker.php');

/**
 * A template with html-specific features.
 */
class mc_HTMLTemplate extends mc_Template
{
    public $_js = array();
    public $_css = array();
    
    public function js(/*file1, file2, ...*/)
    {
        $this->_js = array_merge($this->_js, func_get_args());
    }
    
    public function css(/*file1, file2, ...*/)
    {
        $this->_css = array_merge($this->_css, func_get_args());
    }
    
    public function insert($path, $vars=array(), $tpl_class='mc_HTMLTemplate')
    {
        $vars = array_merge($this->vars, $vars);
        $tpl = new $tpl_class($path);
        
        foreach ($vars as $k => $v)
        {
            $tpl->$k = $v;
        }
        
        $data = $tpl->to_string();
        
        if ($tpl_class == 'mc_HTMLTemplate')
        {
            $this->_js = array_merge($this->_js, $tpl->_js);
            $this->_css = array_merge($this->_css, $tpl->_css);
        }
        
        echo $data;
    }
    
    
    public function process()
    {
        $data = parent::process();
		
        if (count($this->_js) == 0 && count($this->_css) == 0)
        {
            return $data;
        }
        
        libxml_disable_entity_loader(true);
        libxml_use_internal_errors(true);
        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->preserveWhiteSpace = true;
        $doc->formatOutput = false;
        $doc->strictErrorChecking = false;
        
        if (!$doc->loadHTML($data))
        {
            $error = libxml_get_last_error();
            
            if ($error)
            {
                $msg = 'In "'.$this->path.'": ';
                $msg .= 'Line: '.$error->line.', '.$error->message;
                throw new Exception($msg);
            }
            else
            {
                throw new Exception('An unknown parsing error occured');
            }
        }
        
        $simple = simplexml_import_dom($doc);
        $parents = array('head' => null, 'body' => null);
        
        foreach ($parents as $k => $v)
        {
            $parents[$k] = $simple->$k;
        }
        
        $this->add_resources('css', $parents['head']);
        $this->add_resources('js', $parents['body']);
        
        $data = $doc->saveHTML();
        
        if (O::get('shrink.html'))
        {
            $filter = new mc_WhitespaceTemplateFilter();
            $data = $filter->run($data);
        }
        
        $data = $this->close_empty_tags('script', $data);
        
        return $data;
    }
    
    private function add_resources($type, $elm)
    {
        $var = '_'.$type;
        $resources = $this->$var;
        
        if (count($resources) == 0)
        {
            return;
        }
        
        $fn = 'add_'.$type.'_element';
        
        if (O::get('shrink.'.$type))
        {
            call_user_func(array($this, $fn), $elm, mc_Shrinker::$type($resources));
        }
        else
        {
            foreach ($resources as $r)
            {
                call_user_func(array($this, $fn), $elm, O::path($type, $r));
            }
        }
    }
    
    
    private function add_js_element($parent, $path)
    {
        $elm = $parent->addChild('script');
        $elm['type'] = 'text/javascript';
        $elm['src'] = $path;
        return $elm;
    }
    
    
    private function add_css_element($parent, $path)
    {
        $elm = $parent->addChild('link');
        $elm['type'] = 'text/css';
        $elm['href'] = $path;
        $elm['rel'] = 'stylesheet';
        return $elm;
    }
    
    
    private function close_empty_tags($tag, $html)
    {
        $index = 0;
        
        while ($index < strlen($html))
        {
            $pos = strpos($html, '<$tag ', $index);
            
            if ($pos)
            {
                $post_last = strpos($html, '>', $pos);
                
                if ($html[$post_last - 1] == '/')
                {
                    $html = substr_replace($html, '></$tag>', $post_last-1, 2);
                }
                
                $index = $post_last;
            }
            else
            {
                break;
            };
        }
        
        return $html;
    }
}

?>
