<?

require_once('milkandcookies/validators/Validator.php');


class mc_CSVValidator extends mc_Validator
{
    public function __construct($minlength=null,$maxlength=null)
	{
		$this->minlength = $minlength;
		$this->maxlength = $maxlength;
	}
	
	public function validate($value)
	{
		if ($value)
		{
			if (!is_string($value) && ($value != ''))
			{
				throw new ValidatorException('Invalid text value');
			}
			if ($this->minlength != null)
			{
				if (strlen($value) < $this->minlength)
				{
					throw new ValidatorException('The text is too short (min. '.$this->minlength.' characters)');
				}
			}
			if ($this->maxlength != null)
			{
				if (strlen($value) > $this->maxlength)
				{
					throw new ValidatorException('The text is too long (max. '.$this->maxlength.' characters)');
				}
			}
			
			return array_map('trim', explode(',', $value));
		}
	}
}

?>