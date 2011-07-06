<?

require_once('milkandcookies/validators/Validator.php');


class mc_DimensionValidator extends mc_Validator
{
	public function validate($value)
	{
	    if (!is_string($value))
	    {
	        throw new mc_InvalidException('Must be a string.');
	    }
	    
        $parts = explode('x', $value);
        
        if (count($parts) != 2)
        {
            throw new mc_InvalidException('Must be of the form "<width>x<height>"');
        }
        
        $w = (int) $parts[0];
        $h = (int) $parts[1];
        
        return array($w,$h);
	}
}

?>