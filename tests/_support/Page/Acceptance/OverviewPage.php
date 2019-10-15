<?php
namespace Page\Acceptance;

class OverviewPage extends BasePage
{
    const PAGE_CONTENT_DIV = '#rightPanel>div[ng-app="OverviewAccountsApp"]';
    const TITLE_LBL = 'h1.title';
    const ACCOUNT_TABLE_TBL = '#accountTable';

    public function displays()
    {
        $I = $this->acceptanceTester;

        $I->seeElement(self::PAGE_CONTENT_DIV);
        $I->see('Accounts Overview', self::TITLE_LBL);
        $I->seeElement(self::ACCOUNT_TABLE_TBL);
    }
}
