<?php

namespace Xklusive\BattlenetApi\Test;

use Illuminate\Support\Collection;

class DemoTest extends TestCase
{
    protected $wow;

    public function setUp()
    {
        parent::setUp();

        $this->wow = app(\Xklusive\BattlenetApi\Services\WowService::class);
    }

    public function testCallSuccess()
    {
        $this->assertInstanceOf(Collection::class, $this->wow->getAchievement(2144));
    }
}
