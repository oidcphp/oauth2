<?php

namespace Tests\OAuth2;

use OpenIDConnect\OAuth2\Sample;
use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{
    public function testSample()
    {
        $this->assertTrue((new Sample())->alwaysTrue());
    }
}
