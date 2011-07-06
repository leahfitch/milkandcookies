<?

require_once('milkandcookies/validators/Validator.php');

class mc_EnumValidator extends mc_Validator
{
	public function __construct(array $values)
	{
		$this->values = $values;
	}
	
	public function validate($value)
	{
		if (!in_array($value, $this->values))
        {
            throw new mc_InvalidException('Expected one of "'.implode('", "', $this->values).'"');
        }
	}
}

?>