<?

require_once('milkandcookies/validators/Validator.php');

class mc_FloatValidator extends mc_Validator
{
	public function validate($value)
	{
		if (!is_numeric($value))
		{
			throw new mc_InvalidException('The value is not a float');
		}
		
		return (float) $value;
	}
}

?>