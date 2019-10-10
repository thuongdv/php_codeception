<?php

namespace Page\UI;

use UITester;

abstract class BasePage
{
    use TopMenu;

    /** @var UITester $I */
    protected $uiTester;

    public function __construct(UITester $I)
    {
        $this->uiTester = $I;
    }

    public static function generateEmail()
    {
        return sprintf('UI_testing_%s_%s@sharklasers.com', time(), random_int(1000, 9999));
    }
}
