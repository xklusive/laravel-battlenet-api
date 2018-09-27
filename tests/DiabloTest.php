<?php

namespace Xklusive\BattlenetApi\Test;

use Illuminate\Support\Collection;

class DiabloTest extends TestCase
{
    protected $diablo;
    protected $notFoundResponseCode = 'NOTFOUND';
    protected $battleTag = 'Atraides#2274';
    protected $heroId = '96206105';
    protected $itemSlug = 'corrupted-ashbringer';
    protected $itemId = 'Unique_Sword_2H_104_x1';
    protected $followers = ['enchantress', 'templar', 'scoundrel'];
    protected $artisans = ['blacksmith', 'mystic', 'jeweler'];

    public function setUp()
    {
        parent::setUp();

        $this->diablo = app(\Xklusive\BattlenetApi\Services\DiabloService::class);
    }

    /** @test */
    public function api_can_fetch_career_profile()
    {
        $this->diablo->createMockResponse([
        collect([
            'code' => 200,
            'response' => sprintf('{"battleTag": "%s","paragonLevel": 903}', $this->battleTag),
        ]),
    ]);

        $response = $this->diablo->getCareerProfile($this->battleTag);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('battleTag', $response->toArray());
        $this->assertEquals($this->battleTag, $response->get('battleTag'));
    }

    /** @test */
    public function api_can_fetch_hero_profile()
    {
        $this->diablo->createMockResponse([
        collect([
            'code' => 200,
            'response' => sprintf('{"name": "Test","id": %d}', $this->heroId),
        ]),
    ]);

        $response = $this->diablo->getHeroProfile($this->battleTag, $this->heroId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertEquals($this->heroId, $response->get('id'));
    }

    /** @test */
    public function api_can_fetch_item_data()
    {
        $this->diablo->createMockResponse([
        collect([
            'code' => 200,
            'response' => sprintf('{"id": "%s","name": "Test","slug": "%s"}', $this->itemId, $this->itemSlug),
        ]),
    ]);

        $response = $this->diablo->getItem(sprintf('%s-%s', $this->itemSlug, $this->itemId));

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertEquals(
        sprintf('%s-%s', $this->itemSlug, $this->itemId),
        sprintf('%s-%s', $response->get('slug'), $response->get('id'))
    );
    }

    /** @test */
    public function api_can_fetch_follower_data()
    {
        $rand = rand(0, 2);
        $this->diablo->createMockResponse([
        collect([
            'code' => 200,
            'response' => sprintf('{"slug": "%s"}', $this->followers[$rand]),
        ]),
    ]);

        $response = $this->diablo->getFollowerData($this->followers[$rand]);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('slug', $response->toArray());
        $this->assertEquals($this->followers[$rand], $response->get('slug'));
    }

    /** @test */
    public function api_can_fetch_artisan_data()
    {
        $rand = rand(0, 2);
        $this->diablo->createMockResponse([
        collect([
            'code' => 200,
            'response' => sprintf('{"slug": "%s"}', $this->artisans[$rand]),
        ]),
    ]);

        $response = $this->diablo->getArtisanData($this->artisans[$rand]);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('slug', $response->toArray());
        $this->assertEquals($this->artisans[$rand], $response->get('slug'));
    }

    /** @test */
    public function api_should_fail_if_hero_id_is_invalid()
    {
        $this->diablo->createMockResponse([
        collect([
            'code' => 404,
            'response' => '{"code": "NOTFOUND","reason": "The hero could not be found."}',
        ]),
    ]);

        $response = $this->diablo->getHeroProfile($this->battleTag, 'a');

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('error', $response->toArray());
    }

    /** @test */
    public function api_should_fail_if_battletag_is_invalid()
    {
        $this->diablo->createMockResponse([
        collect([
            'code' => 404,
            'response' => '{"code": "NOTFOUND","reason": "The account could not be found."}',
        ]),
    ]);

        $response = $this->diablo->getCareerProfile('aaaa');

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('error', $response->toArray());
    }

    /** @test */
    public function api_should_fail_if_item_data_is_invalid()
    {
        $this->diablo->createMockResponse([
        collect([
            'code' => 404,
            'response' => '{"code": "NOTFOUND","reason": "The requested data could not be found."}',
        ]),
    ]);

        $response = $this->diablo->getItem('a');
        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('error', $response->toArray());
    }

    /** @test */
    public function api_should_fail_if_follower_name_is_invalid()
    {
        $this->diablo->createMockResponse([
        collect([
            'code' => 404,
            'response' => '{"code": "NOTFOUND","reason": "The requested data could not be found."}',
        ]),
    ]);

        $response = $this->diablo->getFollowerData('fail');
        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('error', $response->toArray());
    }

    /** @test */
    public function api_should_fail_if_artisan_name_is_invalid()
    {
        $this->diablo->createMockResponse([
        collect([
            'code' => 404,
            'response' => '{"code": "NOTFOUND","reason": "The requested data could not be found."}',
        ]),
    ]);

        $response = $this->diablo->getArtisanData('fail');
        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('error', $response->toArray());
    }

    /** @test */
    public function api_should_retry_in_case_of_504()
    {
        $this->diablo->createMockResponse([
        collect([
            'code' => 504,
            'response' => '{"code": "504","type": "Gateway Time-out"}',
        ]),
        collect([
            'code' => 504,
            'response' => '{"code": "504","type": "Gateway Time-out"}',
        ]),
        collect([
            'code' => 200,
            'response' => sprintf('{"id": "%s"}', $this->battleTag),
        ]),
    ]);

        $response = $this->diablo->getHeroProfile($this->battleTag, $this->heroId);
        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($this->battleTag, $response->get('id'));

        $this->assertArrayHasKey('attempts', $response->toArray());
        $this->assertEquals(2, $response->get('attempts'));
    }

    /** @test */
    public function api_should_fail_after_maximum_number_of_retries()
    {
        $this->diablo->createMockResponse([
        collect([
            'code' => 504,
            'response' => '{"code": "504","type": "Gateway Time-out"}',
        ]),
        collect([
            'code' => 504,
            'response' => '{"code": "504","type": "Gateway Time-out"}',
        ]),
        collect([
            'code' => 504,
            'response' => '{"code": "504","type": "Gateway Time-out"}',
        ]),
    ]);

        $response = $this->diablo->getHeroProfile($this->battleTag, $this->heroId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('error', $response->toArray());
        $this->assertArrayHasKey('code', $response->get('error')->toArray());
        $this->assertEquals(504, $response->get('error')->get('code'));
    }
}
