<?php
namespace Page\Acceptance;

class HomePage extends BasePage
{
    const USERNAME = 'input[name="username"]';
    const PASSWORD = 'input[name="password"]';
    const LOG_IN = 'input[type="submit"]';

    public function login($username, $password)
    {
        $I =  $this->acceptanceTester;

        $I->fillField(self::USERNAME, $username);
        $I->fillField(self::PASSWORD, $password);
        $I->click(self::LOG_IN);
        $I->waitForPageLoaded();
    }

    public function displays()
    {
        // TODO: Implement display() method.
    }
}
