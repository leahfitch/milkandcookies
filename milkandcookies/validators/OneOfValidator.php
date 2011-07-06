<?

require_once('milkandcookies/validators/Validator.php');

class mc_OneOfValidator extends mc_Validator
{
	public function __construct(array $validators)
	{
		$this->validators = $validators;
	}
	
	public function validate($value)
	{
		foreach ($this->validators as $v)
		{
		    try
		    {
		        return $v->validate($value);
		    }
		    catch (mc_InvalidException $e) {}
		}
		
		throw new mc_InvalidException('Does not meet the requirements of any validator.');
	}
}

?>