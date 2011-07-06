<?

require_once('milkandcookies/validators/Validator.php');


class mc_BoundingBoxValidator extends mc_Validator
{
	public function validate($value)
	{
	    if (is_array($value))
	    {
	        $parts = $value;
	    }
	    else
	    {
	        if (!is_string($value))
	        {
	            throw new mc_InvalidException('Must be a string or array.');
	        }
	        
	        $parts = explode(',', $value);
	    }
        
        if (count($parts) != 4)
        {
            throw new mc_InvalidException('Must be of the form "<lat1>,<lng1>,<lat2>,<lng2>"');
        }
        
        foreach ($parts as $p)
        {
            if ($p < -180 || $p > 180)
            {
                throw new mc_InvalidException('Values must be between -180 and 180.');
            }
        }
        
        return array_map('floatval', $parts);;
	}
}

?>