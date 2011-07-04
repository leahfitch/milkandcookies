<?

require_once('milkandcookies/validators/Validator.php');

class mc_UrlValidator extends mc_Validator
{
	public function validate($value)
	{
		if (!is_string($value))
		{
			throw new ValidatorException('Invalid URL');
		}
		$pattern = '@^(http(s)?://)([^\s<>])+$@';
		if (!preg_match($pattern, $value))
		{
			throw new ValidatorException($value.' is not a valid URL');
		}
	}
}

?>