<?php

class BaseCest
{
    public function _after(UITester $I)
    {
        $I->clearLocalStorage();
    }
}
