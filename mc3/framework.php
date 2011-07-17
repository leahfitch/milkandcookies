<?
require_once('mc3/Options.php');
require_once('mc3/Log.php');
require_once('mc3/ComponentRegistry.php');
require_once('mc3/Bouncer.php');

function mcinit($path, $project_name='unknown')
{
    O::set('path.home', $path);
    O::set('path.app', O::path('home', $project_name));
    O::set('path.www', O::path('home', 'www'));
    
    O::set(array(
        'path.handlers' => O::path('app', 'handlers'),
        'path.components' => O::path('app', 'components'),
        'path.templates' => O::path('app', 'templates'),
        'path.log' => O::path('home', 'app.log'),
        'path.img_local' => O::path('www', 'img'),
        'path.js_local' => O::path('www', 'js'),
        'path.css_local' => O::path('www', 'css'),
        'path.cache_local' => O::path('www', 'cache'),
        'path.img' => '/img',
        'path.js' => '/js',
        'path.css' => '/css',
        'path.cache' => '/cache',
        'security.show_errors' => true,
        'logging.level' => mc_Log::INFO,
        'shrink.js' => false,
        'shrink.css' => false,
        'shrink.html' => true,
        'system_component_class' => 'mc_SystemComponent'
    ));
    
    $include_paths = array(
        O::get('path.home'),
        get_include_path()
    );
    
    set_include_path(implode(PATH_SEPARATOR, $include_paths));
    
    $config_path = O::path('home', 'config.php');
    
    if (file_exists($config_path))
    {
        require_once($config_path);
    }
    
    mc_Log::setup(O::path('log'), O::get('logging.level'));
    
    if (O::get('security.show_errors'))
    {
        error_reporting(E_ALL);
    }
    else
    {
        error_reporting(0);
    }
    
    $registered_components_path = O::path('components', 'registered.php');
    
    if (file_exists($registered_components_path))
    {
        require_once($registered_components_path);
    }
}

function mchandle()
{
    if (!isset($_GET['__mcpath']))
    {
        die('Missing __mcpath. Have you set up your rewrite rules?');
    }
    
    try
    {
        $root_handler_path = O::path('handlers', 'RootHandler.php');
        
        if (!file_exists($root_handler_path))
        {
            throw new Exception('Root handler does not exist at '.$root_handler_path.'.');
        }
        
        require_once($root_handler_path);
        
        $root = new RootHandler();
        
        mcclean($_GET);
        mcclean($_POST);
        mcclean($_REQUEST);
        mcclean($_COOKIE);
        
        echo $root->route($_GET['__mcpath']);
    }
    catch (Exception $e)
    {
        if (O::get('security.show_errors'))
        {
            header('Content-Type: text/plain');
            echo $e->getTraceAsString();
        }
    }
}

function mcclean($arr)
{
    if (is_array($arr))
    {
        foreach ($arr as $k => $v)
        {
            if (is_array($v))
            {
                mcclean($v);
            }
            else
            {
                $arr[$k] = strip_tags($v);
            }
        }
    }
}


function mcdie($obj)
{
    header('Content-Type: text/plain; charset=utf8');
    print_r($obj);
    exit();
}

?>
