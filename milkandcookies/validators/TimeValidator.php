<?

require_once('milkandcookies/validators/Validator.php');


class mc_TimeValidator extends mc_Validator
{
	public function validate($value)
	{
	    if (is_numeric($value))
	    {
	        return $value;
	    }
	    
	    $value = strtotime($value);
        
	    if (!$value)
        {
            throw new mc_InvalidException('Unrecognized date/time format');
        }
        
        return $value;
	}
}

?>