<?

require_once('milkandcookies/validators/Validator.php');


class mc_LatLngValidator extends mc_Validator
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
	            throw new mc_InvalidException('Must be a string.');
	        }
	        
	        $parts = explode(',', $value);
	    }
        
        if (count($parts) != 2)
        {
            throw new mc_InvalidException('Must be of the form "<lat>,<lng>"');
        }
        
        $lat = (float) $parts[0];
        $lng = (float) $parts[1];
        
        if ($lat < -180 || $lat > 180 || $lng < -180 || $lng > 180)
        {
            throw new mc_InvalidException('Values must be between -180 and 180.');
        }
        
        return array($lat,$lng);
	}
}

?>