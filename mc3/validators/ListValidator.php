<?

require_once('mc3/validators/Validator.php');

class mc_ListValidator extends mc_Validator
{
    public function __construct($validator)
    {
        $this->validator = $validator;
    }
    
	public function validate($value)
	{
		if (!is_array($value))
		{
			throw new mc_InvalidException('Expected a list.');
		}
		
		$result = array();
		
		foreach ($value as $v)
		{
		    $result[] = $this->validator->validate($v);
		}
		
		return $result;
	}
}

?>