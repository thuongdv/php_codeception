<?php

class CustomConsole extends Codeception\Subscriber\Console
{
    /**
     * When we run test by PhpStorm for example, the step output is cut off if the step length is over 60 characters
     * e.g. $I->sendGET("/api/1/toptransac...").
     * This method is used to show more than 60 characters
     * @return array|false|int|string
     */
    public function detectWidth()
    {
        $this->width = 350;

        return $this->width;
    }
}
