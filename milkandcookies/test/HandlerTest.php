<?
require_once('PHPUnit/Framework/TestCase.php');
require_once('milkandcookies/Handler.php');

class RootHandler extends mc_Handler
{
    public function __construct()
    {
        parent::__construct();
        
        $this->expose(array(
            'foo' => '',
            'skidoo' => '@^(\d+)$@'
        ));
        
        $this->add_children(array(
            'baby' => 'ChildHandler'
        ));
    }
    
    
    public function handle()
    {
        return 'handle';
    }
    
    
    public function foo()
    {
        return 'foo';
    }
    
    
    public function skidoo($some_numeric_arg)
    {
        return $some_numeric_arg;
    }
}


class ChildHandler extends mc_Handler
{
    public function __construct()
    {
        parent::__construct();
        
        $this->expose(array(
            'boo' => ''
        ));
    }
    
    
    public function handle()
    {
        return 'handle';
    }
    
    
    public function boo()
    {
        return 'BOO!';
    }
}


class HandlerTest extends PHPUnit_Framework_TestCase
{
    public function test_simple_call()
    {
        $handler = new RootHandler();
        $this->assertEquals('foo', $handler->call('foo'));
        $this->assertEquals('23', $handler->call('skidoo', '23'));
    }
    
    public function test_child_call()
    {
        $handler = new RootHandler();
        $handler = $handler->get_child('baby');
        $this->assertEquals('BOO!', $handler->call('boo'));
    }
    
    function test_simple_route()
    {
        $handler = new RootHandler();
        $this->assertEquals('foo', $handler->route('/foo'));
        
        try
        {
            $handler->route('/dfkgjhdfgkjh');
        }
        catch (mc_HandlerNotFoundException $e)
        {
            return;
        }
        
        $this->fail('The expected mc_HandlerNotFoundException was not raised.');
    }
    
    function test_default_route()
    {
        $handler = new RootHandler();
        $this->assertEquals('handle', $handler->route('/'));
        $this->assertEquals('handle', $handler->route(''));
    }
    
    function test_child_route()
    {
        $handler = new RootHandler();
        $this->assertEquals('BOO!', $handler->route('/baby/boo'));
    }
}

?>