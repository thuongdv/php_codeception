<?php
namespace Page\Acceptance;

use AcceptanceTester;

abstract class BasePage implements LeftMenuConst
{
    use LeftPanel;

    /** @var AcceptanceTester $I */
    protected $acceptanceTester;

    public function __construct(AcceptanceTester $I)
    {
        $this->acceptanceTester = $I;
    }

    abstract public function displays();
}
