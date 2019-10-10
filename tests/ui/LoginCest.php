<?php

use Page\UI\HomePage;
use Page\UI\SignInPage;

class LoginCest extends BaseCest
{
    /**
     * @group P1
     */
    public function tc01SignInWithNonexistingCredentials(UITester $I, SignInPage $signInPage)
    {
        $I->comment('I navigate home page');
        $I->amOnPage('/');

        $I->comment('I go to sign in page');
        $I->click(HomePage::$signInLnk);
        $I->waitForPageLoaded(WAIT_FOR_PAGE_LOADED);

        $I->comment('I login with non existing credentials');
        $signInPage->signIn('nonexisting_email@test.com', '123456');

        $I->comment('I verify user cannot sign in and authentication failed message displays');
        $signInPage->verifyUserCannotSignIn();
    }
}
