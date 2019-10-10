<?php

use Codeception\Actor;
use Codeception\Lib\Friend;
use PHPUnit\Framework\AssertionFailedError;


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
 * @method Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class UITester extends Actor
{
    use _generated\UITesterActions;

    public function clickIfSeeElement($element, $waitTime = null, $timeout = WAIT_FOR_CONTROL)
    {
        if ($this->isElementVisible($element, $timeout)) {
            if ($waitTime !== null) {
                $this->waitThenClick($element, $waitTime);
            } else {
                $this->click($element);
            }
        }
    }

    private function waitThenClick($selector, $waitTime)
    {
        $this->wait($waitTime);
        $this->click($selector);
    }

    public function isElementVisible($selector, $timeout = WAIT_FOR_CONTROL)
    {
        try {
            $this->waitForElementVisible($selector, $timeout);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function setComboBox($menu, $item, $waitTime = 0.1)
    {
        $this->click($menu);
        $this->waitThenClick($item, $waitTime);
    }

    public function waitForPageLoaded($timeout = WAIT_FOR_PAGE_LOADED)
    {
        $this->waitForJS('return document.readyState == "complete";', $timeout);
    }

    public function getCurrentConfigUrl($config)
    {
        return $this->getScenario()->current('modules')[$config]->_getConfig('url');
    }

    public function getCSSValue($control, $cssAttribute)
    {
        try {
            return $this->findElements($control)[0]->getCSSValue($cssAttribute);
        } catch (Exception $e) {
            return false;
        }
    }

    public function scrollToIfExists($control)
    {
        if ($this->doesControlExist($control)) {
            $this->scrollTo($control);
        }
    }

    public function doesControlExist($control)
    {
        $el = $this->findElements($control);
        return count($el) > 0;
    }

    public function clearLocalStorage()
    {
        $this->executeJS(
            'localStorage.clear();'
        );
    }

    function clearThenFillField($field, $value)
    {
        $this->click($field);
        $this->pressKey($field, array('ctrl', 'a'), $value);
    }

    function getSize($element)
    {
        return $this->findElements($element)[0]->getSize();
    }

    public function scrollIntoViewByXpath($selector)
    {
        $el = $this->findElements($selector)[0];
        $this->executeJS('arguments[0].scrollIntoView();', [$el]);
    }

    public function waitForAjax($timeout = WAIT_FOR_CONTROL)
    {
        $this->waitForJS('return $.active == 0;', $timeout);
    }

    function clickUntilSeeElement($elementClick, $elementWait, $retry = 3, $waitPerRetry = 1)
    {
        for ($i = 1; $i <= $retry; $i++) {
            $this->clickAndRetry($elementClick);
            $this->wait($waitPerRetry);
            if ($this->doesControlExist($elementWait)) {
                break;
            }
            if ($i == $retry) {
                throw new AssertionFailedError("Cannot see $elementWait");
            }
        }
    }

    public function clickAndRetry($element, $retry = 3, $waitPerRetry = 1)
    {
        try {
            if ($retry > 0) {
                $this->click($element);
            }
        } catch (Exception $ex) {
            if (strpos($ex->getMessage(), 'is not clickable at point')) {
                $this->wait($waitPerRetry);
                $this->clickAndRetry($element, $retry - 1);
            }
        }
    }

    function clickUntilDontSeeElement($elementCLick, $elementWait, $retry = 3, $waitPerRetry = 1)
    {
        for ($i = 1; $i <= $retry; $i++) {
            $this->clickAndRetry($elementCLick);
            $this->wait($waitPerRetry);
            if ($this->doesControlNotExist($elementWait)) {
                break;
            }
            if ($i == $retry) {
                throw new AssertionFailedError("Can see $elementWait");
            }
        }
    }

    public function doesControlNotExist($control)
    {
        return !$this->doesControlExist($control);
    }

    public function verifyControlExist($control)
    {
        $exist = $this->doesControlExist($control);
        $this->verifyTrue($exist);
    }

    public function verifyControlNotExist($control)
    {
        $notExist = $this->doesControlNotExist($control);
        $this->verifyTrue($notExist);
    }
}
