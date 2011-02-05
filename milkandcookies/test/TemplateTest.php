<?

require_once('PHPUnit/Framework/TestCase.php');
require_once('milkandcookies/Template.php');

class TemplateTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        mc_Template::set_template_directory(dirname(__FILE__).DIRECTORY_SEPARATOR.'templates');
    }
    
    public function test_plain()
    {
        $tpl = new mc_Template('simple.tpl.php');
        $tpl->type = 'plain text';
        $this->assertEquals('This is a plain text template.', $tpl->to_string());
    }
    
    public function test_insert()
    {
        $tpl = new mc_Template('parent.tpl.php');
        $this->assertEquals('I\'m the parent. I\'m the child.', $tpl->to_string());
    }
    
    public function test_scope()
    {
        $tpl = new mc_Template('scope_parent.tpl.php');
        $tpl->foo = 'bar';
        $this->assertEquals('A guy walks into a bar...ouch.', $tpl->to_string());
    }
}

?>