<?

require_once('milkandcookies/validators/Validator.php');

class mc_IntValidator extends mc_Validator
{
	public function validate($value)
	{
		if (!is_numeric($value))
		{
			throw new ValidatorException('This value must be a number');
		}
		if (is_string($value))
		{
			if (!preg_match('@^[1-9]{1}([0-9]+)?$@', $value) > 0)
			{
				throw new ValidatorException('This value must be an integer');
			}
		}
		else
		{
			if (!is_int($value))
			{
				throw new ValidatorException('This value must be an integer');
			}
		}
		
		return intval($value);
	}
}

?>