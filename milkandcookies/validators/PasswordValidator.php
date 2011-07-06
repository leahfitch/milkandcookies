<?

require_once('milkandcookies/validators/Validator.php');

class mc_PasswordValidator extends mc_Validator
{
	private $letters=false;
	private $capital_letters=false;
	private $numbers=false;
	private $special_chars=false;
	private $minlength;
	
	/**
	 * Create a password field
	 * 
	 * The $required parameter can contain any or none of the following options in any order:
	 * <ul>
	 *	<li>'a' - lowercase letters</li>
	 *	<li>'A' - uppercase letters</li>
	 *	<li>'0' (zero) - numbers</li>
	 *	<li>'!' - special characters</li>
	 * </ul>
	 * So this example:
	 * <code>
	 * $pass = new PasswordValidator('Aa!', 6);
	 * </code>
	 * creates a password field that requires it's value to contain upper and 
	 * lowercase letters and at least one symbol and be at least 6 characters long.
	 *
	 * @param string $required a list of (optional) required password character types
	 * @param integer $minlength minimum password length
	 */
	public function __construct($requiredChars, $minlength = null)
	{
		$this->minlength = $minlength;
		
		if (strpos($requiredChars, 'a'))
		{
			$this->letters = true;
		}
		if (strpos($requiredChars, 'A'))
		{
			$this->capital_letters = true;
		}
		if (strpos($requiredChars, '0'))
		{
			$this->numbers = true;
		}
		if (strpos($requiredChars, '!'))
		{
			$this->special_chars = true;
		}
	}
	
	public function validate($value)
	{
		if (!is_string($value))
		{
			throw new mc_InvalidException('Invalid password');
		}
		
		if ($this->minlength != null)
		{
			if (strlen($value) < $this->minlength)
			{
				throw new mc_InvalidException('The password must be at least '.$this->minlength
					.' characters long');
			}
		}
		
		$errors = array();
		
		if ($this->letters)
		{
			$pattern = '/[a-zA-Z]/';
			if (!preg_match($pattern, $value))
			{
				$errors[] = 'a lowercase letter';
			}
		}
		if ($this->capital_letters)
		{
			$pattern = '/[A-Z]/';
			if (!preg_match($pattern, $value))
			{
				$errors[] = 'a capital letter';
			}
		}
		if ($this->numbers)
		{
			$pattern = '/[0-9]/';
			if (!preg_match($pattern, $value))
			{
				$errors[] = 'a number';
			}
		}
		if ($this->special_chars)
		{
			$pattern = '/[\!@#\$\%\^&\*|(\)]/';
			if (!preg_match($pattern, $value))
			{
				$errors[] = 'a special character';
			}
		}
		if (count($errors) > 0)
		{
			$errormsg = 'The password must contain '.implode(', ',$errors);
			throw new mc_InvalidException($errormsg);
		}
	}
}

?>