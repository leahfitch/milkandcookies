<?

require_once('PHPUnit/Framework/TestCase.php');
require_once('milkandcookies/Template.php');
require_once('milkandcookies/Options.php');


class SillyFilter extends mc_TemplateFilter
{
    public function run($data)
    {
        return 'filter';
    }
}


class TemplateTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        O::set('path.templates', dirname(__FILE__).DIRECTORY_SEPARATOR.'templates');
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
    
    
    public function test_filter()
    {
        $tpl = new mc_Template('simple.tpl.php');
        $tpl->type = 'foo';
        $tpl->add_filter(new SillyFilter);
        $this->assertEquals('filter', $tpl->to_string());
    }
}

?>