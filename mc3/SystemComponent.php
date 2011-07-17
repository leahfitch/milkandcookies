<?
require_once('mc3/Component.php');
require_once('mc3/validators/all.php');

/**
 * Some milk and cookies system commands.
 */
class mc_SystemComponent extends mc_Component
{
    public function __construct()
    {
        parent::__construct();
        
        $this->expose(
            'create',
            array(
                'name' => new mc_StringValidator(1,1024)
            ),
            array('internal')
        );
    }
    
    /**
     * Create a new framework template project directory.
     */
    public function create($args)
    {
        if (file_exists($args['name']))
        {
            throw new Exception($args['name'].' already exists!\n');
        }
        
        $tpl_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'{project}';
        $this->copy_dir($tpl_dir, $args['name'], $args['name']);
    }
    
    private function copy_dir($src, $dest, $project_name)
    {
        $dest = str_replace('{project}', $project_name, $dest);
        mkdir($dest);
        $cur = opendir($src);
        
        while ($f = readdir($cur))
        {
            if ($f == '.' || $f == '..')
            {
                continue;
            }
            
            if (is_dir($src.DIRECTORY_SEPARATOR.$f))
            {
                $this->copy_dir(
                    $src.DIRECTORY_SEPARATOR.$f,
                    $dest.DIRECTORY_SEPARATOR.$f,
                    $project_name
                );
            }
            else
            {
                $data = file_get_contents($src.DIRECTORY_SEPARATOR.$f);
                $data = str_replace('{project}', $project_name, $data);
                file_put_contents($dest.DIRECTORY_SEPARATOR.$f, $data);
            }
        }
        
        closedir($cur);
    }
}

?>
