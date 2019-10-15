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
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

   /**
    * Define custom actions here
    */
    public function waitForPageLoaded($timeout = WAIT_FOR_PAGE_LOADED)
    {
        $this->waitForJS('return document.readyState == "complete";', $timeout);
    }

    public function doesControlExist($control)
    {
        $el = $this->findElements($control);
        return count($el) > 0;
    }

    public function doesControlNotExist($control)
    {
        return !$this->doesControlExist($control);
    }

    public function waitForAjax($timeout = WAIT_FOR_CONTROL)
    {
        $this->waitForJS('return $.active == 0;', $timeout);
    }
}
