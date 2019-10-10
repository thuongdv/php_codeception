<?php

namespace Page\UI;

use UITester;

class SignInPage extends BasePage
{
    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    public static $emailAddressTxt = '#email';
    public static $passwordTxt = '#passwd';
    public static $signInBtn = '#SubmitLogin';
    public static $alertDangerLbl = '.alert.alert-danger';

    public function signIn($email, $password)
    {
        $I = $this->uiTester;

        $I->fillField(self::$emailAddressTxt, $email);
        $I->fillField(self::$passwordTxt, $password);
        $I->click(self::$signInBtn);
        $I->waitForPageLoaded(WAIT_FOR_PAGE_LOADED);
    }

    public function verifyUserCannotSignIn()
    {
        $I = $this->uiTester;

        $I->seeElement(self::$alertDangerLbl);
        $I->see('There is 1 error', self::$alertDangerLbl);
        $I->see('Authentication failed.', self::$alertDangerLbl);
        $I->seeElement(self::$signInLnk);
    }

    /**
     * @var UITester;
     */
    protected $uiTester;

    public function __construct(UITester $I)
    {
        $this->uiTester = $I;
    }
}
