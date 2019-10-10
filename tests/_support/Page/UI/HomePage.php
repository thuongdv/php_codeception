<?php

namespace Page\UI;

use UITester;

class HomePage extends BasePage
{
    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    /**
     * @var UITester;
     */
    protected $uiTester;

    public function __construct(UITester $I)
    {
        $this->uiTester = $I;
    }
}
