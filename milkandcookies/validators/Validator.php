<?

/**
 * Oops, something wasn't valid.
 */
class mc_InvalidException extends Exception {}

/**
 * A validator ensures that it's input value meets certain constraints and
 * sometimes performs normalization.
 */
abstract class mc_Validator
{
    abstract public function validate($value);
}

?>
