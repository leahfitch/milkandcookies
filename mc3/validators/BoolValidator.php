<?

require_once('mc3/validators/Validator.php');


class mc_BoolValidator extends mc_Validator
{
	public function validate($value)
	{
	    if ($value == 'true')
		{
		    return true;
		}
		else if ($value == 'false')
		{
		    return false;
		}
		
		if (!is_bool($value) && 
		        ($value != 1) && 
				($value != 0) && 
				($value != '1') &&
				($value != '0')
            )
		{
			throw new mc_InvalidException('The value is not a boolean');
		}
		
		return (bool)((int)$value);
	}
}
?>