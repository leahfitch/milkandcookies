<?

require_once('PHPUnit/Framework/TestCase.php');
require_once('milkandcookies/Options.php');


class OptionsTest extends PHPUnit_Framework_TestCase
{
    public function test_keyvalue()
    {
        mc_Options::set('key', 'value');
        $this->assertEquals('value', mc_Options::get('key'));
    }
    
    
    public function test_path()
    {
        mc_Options::set('path.foo', '/foo/bar');
        $this->assertEquals('/foo/bar/baz', mc_Options::path('foo', 'baz'));
        $this->assertEquals('/foo/bar/skidoo/23/', mc_Options::path('foo', '/skidoo/23/'));
        mc_Options::set('path.foo', 'foo');
        $this->assertEquals('foo/good', mc_Options::path('foo', '/good'));
        $this->assertEquals('foo', mc_Options::path('foo'));
        $this->assertEquals('', mc_Options::path('dfgdfg'));
    }
    
    
    public function test_convenience()
    {
        $this->assertTrue(class_exists('O'));
        O::set('key', 'value');
        $this->assertEquals('value', O::get('key'));
        O::set('path.foo', '/foo');
        $this->assertEquals('/foo/bar', O::path('foo', 'bar'));
    }
    
    
    public function test_handlers()
    {
        O::set_handler('foo', function ($a, $b) { return 'foo '.$b; });
        $this->assertEquals('foo bar', O::get('foo', 'bar'));
        O::set_handler('path.foo', function ($a, $b) { return 'foo://'.$b; });
        $this->assertEquals('foo://bar', O::path('foo', 'bar'));
    }
}

?>