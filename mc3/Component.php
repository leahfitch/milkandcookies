<?
require_once('mc3/validators/CompoundValidator.php');


class mc_ComponentMethodNotFoundException extends Exception
{
}


class mc_Component
{
    public $exposed;
    
    public function __construct()
    {
        $this->exposed = array();
    }
    
    
    public function call($name, $args=array())
    {
        if (!isset($this->exposed[$name]))
        {
            throw new mc_ComponentMethodNotFoundException('No method found called "'.$name.'"');
        }
        
        if (!method_exists($this, $name))
        {
            throw new Exception('The method "'.$name.'" is undefined.');
        }
        
        list($vargs, $roles) = $this->exposed[$name];
        
        mc_Bouncer::enforce($roles);
        
        $validator = new mc_CompoundValidator($vargs);
        $validated_params = $validator->validate($args);
        
        return call_user_func(array($this, $name), $args);
    }
    
    
    public function get_arguments($name)
    {
        if (!isset($this->exposed[$name]))
        {
            throw new mc_ComponentMethodNotFoundException('No method found called "'.$name.'"');
        }
        
        return $this->exposed[$name][0];
    }
    
    
    protected function expose($name, array $args=null, array $roles=null)
    {
        $this->exposed[$name] = array($args, $roles);
    }
}

?>
