<?php 

use Page\Acceptance\OverviewPage;
use Page\Acceptance\HomePage;

class LogInCest
{
    /**
     * @group P1
     */
    public function tc01SignInWithExistingCredentials(AcceptanceTester $I, HomePage $homePage, OverviewPage $overviewPage)
    {
        $I->comment('I navigate to home page');
        $I->amOnPage('');

        $I->comment('I login with existing credentials');
        $homePage->login(ACCOUNT_DATA['username'], ACCOUNT_DATA['password']);

        $I->comment('I verify user logs in successfully');
        $overviewPage->displays();
        $I->see(sprintf('Welcome %s %s', ACCOUNT_DATA['firstName'], ACCOUNT_DATA['lastName']), OverviewPage::WELCOME_LBL);
        $I->seeElement(sprintf(OverviewPage::LINK, 'Log Out'));
    }
}
