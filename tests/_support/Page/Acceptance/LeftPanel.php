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

}
