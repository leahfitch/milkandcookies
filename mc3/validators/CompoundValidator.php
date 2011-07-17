<?

require_once('mc3/validators/Validator.php');

/**
 * One or more fields dare invalid.
 */
class mc_CompoundInvalidException extends Exception
{
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        $this->message = 'These fields are invalid: '.implode(', ', array_keys($errors));
    }
}


/**
 * A group of named validators that must all pass for the group to pass.
 * 
 * Example:
 * 
 * <code>
 * $compound_validator = new mc_CompoundValidator(array(
 *     'foo' => new mc_IntValidator(1,100),
 *     'bar' => new mc_StringValidator(1,6)
 * ));
 * $compound_validator->validate(array('foo' => 23, 'bar' => 'skidoo'));
 * // -> array('foo' => 23, 'bar' => 'skidoo')
 * $compound_validator->validate(array('foo' => 23, 'bar' => 'skidoosh'));
 * // -> mc_CompoundInvalidException ('bar' is too long)
 * </code>
 * 
 * To have optional fields pass in the validator in an array along with it's default value.
 * 
 * <code>
 * $compound_validator = new mc_CompoundValidator(array(
 *     'foo' => new mc_IntValidator(1,100),
 *     'bar' => array(new mc_StringValidator(1,6), 'baz')
 * ));
 * $compound_validator->validate(array('foo' => 77));
 * // -> array('foo' => 77, 'bar' => 'baz')
 * </code>
 */
class mc_CompoundValidator extends mc_Validator
{
    public function __construct($spec)
    {
        $this->spec = $spec;
    }
    
    public function validate($value)
    {
        if (!is_array($value))
        {
            throw new Exception('Expected $value to be an array');
        }
        
        $new_value = array();
        $errors = array();
        
        foreach ($this->spec as $k => $validator)
        {
            if (!isset($value[$k]) || $value[$k] === null || 
                (is_string($value[$k]) && trim($value[$k]) == ''))
            {
                if (is_array($validator))
                {
                    $new_value[$k] = $validator[1];
                }
                else
                {
                    $errors[$k] = 'This field is required.';
                }
                
                continue;
            }
            
            $validator = is_array($validator) ? $validator[0] : $validator;
            
            try
            {
                $new_v = $validator->validate($value[$k]);
                $new_value[$k] = $new_v === null ? $value[$k] : $new_v;
            }
            catch (mc_InvalidException $e)
            {
                $errors[$k] = $e->getMessage();
            }
        }
        
        if (count($errors) > 0)
        {
            throw new mc_CompoundInvalidException($errors);
        }
        
        return $new_value;
    }
}

?>
