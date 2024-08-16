<?php

require_once __DIR__ . '/WpPusherTestCase.php';

class ExampleTest extends WpPusherTestCase
{
    /**
     * @test
     */
    public function false_is_not_true()
    {
        $this->assertNotTrue(false);
    }
}
