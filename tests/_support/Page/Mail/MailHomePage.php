<?php

namespace Page\Mail;

use UITester;

class MailHomePage
{
    const BACK_TO_INBOX_BTN = '#back_to_inbox_link';
    const DELETE_BTN = '#del_button';
    const EDIT_EMAIL_BTN = '#inbox-id';
    const EMAIL_ADDRESS_TXT = "//*[@id='inbox-id']/input";
    const EMAIL_CHX = '(//table[@id="email_table"]//input[@type="checkbox"])';
    const SET_BTN = "//*[@id='inbox-id']/button[text()='Set']";

    public static $URL = 'https://www.guerrillamail.com/inbox';
    protected $UITester;

    public function __construct(UITester $I)
    {
        $this->UITester = $I;
    }

    private function openEmail($email, $selector)
    {
        $I = $this->UITester;

        $this->navigateToEmailPage();
        $this->setEmail($email);
        $I->waitForElement($selector, WAIT_FOR_EMAIL);
        $I->clickLoadingElement($selector);
        $I->waitForAjax();
        // Sometimes mail details automatically backs to inbox, handle this case
        if (!$I->isElementVisible(self::BACK_TO_INBOX_BTN, 5)) {
            $this->setEmail($email);
            $I->waitForElement($selector, WAIT_FOR_EMAIL);
            $I->click($selector);
            $I->waitForAjax();
        }
    }

    private function navigateToEmailPage()
    {
        $I = $this->UITester;
        $I->amOnUrl(self::$URL);
    }

    private function setEmail($email)
    {
        $I = $this->UITester;
        $I->click(self::EDIT_EMAIL_BTN);
        $I->fillField(self::EMAIL_ADDRESS_TXT, $email);
        $I->click(self::SET_BTN);
    }

    public function deleteAllEmail()
    {
        $I = $this->UITester;

        $count = count($I->grabMultiple(self::EMAIL_CHX));
        for ($i = 1; $i <= $count; $i++) {
            $control = sprintf(self::EMAIL_CHX . "[%s]", $i);
            $I->click($control);
            $I->waitForAjax();
        }
        $I->click(self::DELETE_BTN);
        $I->waitForAjax();
    }
}
