<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;
    use Codeception\Util\Shared\Asserts;

   /**
    * Define custom actions here
    */

    public function seeValueEquals($value, $actual, $message = null)
    {
        if (is_array($value)) {
            sort($value);
            sort($actual);
        }

        $this->comment('I check given value:');
        $this->comment(json_encode($value));
        $this->comment('is equal to actual value:');
        $this->comment(json_encode($actual));
        $this->assertEquals($value, $actual, $message);
    }
}
