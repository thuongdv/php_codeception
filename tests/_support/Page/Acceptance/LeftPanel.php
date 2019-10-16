<?php
namespace Page\Acceptance;

interface LeftMenuConst
{
    const LEFT_PANEL = '#leftPanel';
    const WELCOME_LBL = self::LEFT_PANEL . '>p.smallText';
    const LINK = '//*[@id="leftPanel"]//a[text()="%s"]';
}

trait LeftPanel
{
    public function logout()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->acceptanceTester;

        $I->click(sprintf(self::LINK, 'Log Out'));
        $I->waitForPageLoaded();
    }
}
