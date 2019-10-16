<?php

use Page\Acceptance\HomePage;
use Page\Acceptance\OverviewPage;

class LogOutCest
{
    /**
     * @group P1
     */
    public function tc01LogOut(AcceptanceTester $I, HomePage $homePage, OverviewPage $overviewPage)
    {
        $I->comment('I navigate to home page');
        $I->amOnPage('');

        $I->comment('I login with existing credentials');
        $homePage->login(ACCOUNT_DATA['username'], ACCOUNT_DATA['password']);

        $I->comment('I click on Log Out link');
        $overviewPage->logout();

        $I->comment('I verify user logs out successfully');
        $I->seeElement(HomePage::USERNAME);
    }
}
