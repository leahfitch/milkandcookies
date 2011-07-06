<?

require_once('milkandcookies/validators/Validator.php');

class mc_UrlValidator extends mc_Validator
{
	public function validate($value)
	{
		if (!is_string($value))
		{
			throw new mc_InvalidException('Invalid URL');
		}
		$pattern = '@^(http(s)?://)([^\s<>])+$@';
		if (!preg_match($pattern, $value))
		{
			throw new mc_InvalidException($value.' is not a valid URL');
		}
	}
}

?>