<?
ob_start();

require_once('PHPUnit/Framework/TestCase.php');
require_once('milkandcookies/RestHandler.php');

class RootRestHandler extends mc_RestHandler
{
    public function __construct()
    {
        parent::__construct();
        
        $this->add_children(array(
            'children' => 'ChildRestHandler'
        ));
    }
}

class ChildRestHandler extends mc_RestHandler
{
    public function GET()
    {
        return 'all';
    }
    
    
    public function GET_one($id)
    {
        return 'one:'.$id;
    }
    
    public function GET_children($id)
    {
        return sprintf('all %s\'s children', $id);
    }
}


class RestHandlerTest extends PHPUnit_Framework_TestCase
{
    public function test_get_all()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $handler = new RootRestHandler();
        $this->assertEquals('all', $handler->call('children', ''));
    }
    
    public function test_get_one()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $handler = new RootRestHandler();
        $this->assertEquals('one:1', $handler->call('children', '1'));
    }
    
    function test_get_relationship()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $handler = new RootRestHandler();
        $this->assertEquals('all 1\'s children', $handler->call('children', '1/children'));
    }
}
?>
