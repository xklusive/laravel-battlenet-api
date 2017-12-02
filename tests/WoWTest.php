<?php

namespace Xklusive\BattlenetApi\Test;

use Illuminate\Support\Collection;

class WowTest extends TestCase
{
    protected $wow;
    protected $realm = 'Arathor';
    protected $guild = 'Rise Legacy';
    protected $character = 'Hellodora';
    protected $itemId = 18803;
    protected $itemSet = 1060;
    protected $abilityId = 640;
    protected $speciesId = 258;
    protected $petLevel = 25;
    protected $petBreedId = 5;
    protected $petQualityId = 4;
    protected $pvpBracket = '2v2';
    protected $questId = 13146;
    protected $recipeId = 33994;
    protected $spellId = 133; // Fireball

    public function setUp()
    {
        parent::setUp();

        $this->wow = app(\Xklusive\BattlenetApi\Services\WowService::class);
    }

    /** @test */
    public function api_can_fetch_wow_achievements()
    {
        $id = 2144; # Achievement ID
        $response = $this->wow->getAchievement($id);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($id,$response->get('id'));
    }

    /** @test */
    public function api_can_fetch_auction_data()
    {
        $response = $this->wow->getAuctionDataStatus($this->realm);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('files', $response->toArray());
        $this->assertObjectHasAttribute('url', $response->get('files')[0]);
    }

    /** @test */
    public function api_can_fetch_supported_bosses()
    {
        $response = $this->wow->getBossMasterList();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('bosses', $response->toArray());
    }

    /** @test */
    public function api_can_fetch_a_boss()
    {
        $bosses = $this->wow->getBossMasterList();
        $bossId = collect($bosses->get('bosses'))->first()->id;
        $response = $this->wow->getBoss($bossId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertArrayHasKey('urlSlug', $response->toArray());
    }

    /** @test */
    public function api_can_fetch_realm_leader_board()
    {
        $response = $this->wow->getRealmLeaderboard($this->realm);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('challenge', $response->toArray());
    }

    /** @test */
    public function api_can_fetch_region_leader_board()
    {
        $response = $this->wow->getRegionLeaderboard();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('challenge', $response->toArray());
    }

    /** @test */
    public function api_can_fetch_a_character()
    {
        $response = $this->wow->getCharacter($this->realm, $this->character);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertArrayHasKey('class', $response->toArray());
        $this->assertArrayHasKey('race', $response->toArray());
        $this->assertArrayHasKey('gender', $response->toArray());
        $this->assertArrayHasKey('level', $response->toArray());
        $this->assertEquals($this->character,$response->get('name'));
    }

    /** @test */
    public function api_can_fetch_a_guild()
    {
        $response = $this->wow->getGuild($this->realm, $this->guild);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertArrayHasKey('level', $response->toArray());
        $this->assertArrayHasKey('side', $response->toArray());
        $this->assertArrayHasKey('emblem', $response->toArray());
        $this->assertEquals($this->guild,$response->get('name'));
    }

    /** @test */
    public function api_can_fetch_guild_members()
    {
        $response = $this->wow->getGuildMembers($this->realm,$this->guild);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertArrayHasKey('realm', $response->toArray());
        $this->assertArrayHasKey('members', $response->toArray());
        $this->assertObjectHasAttribute('character', $response->get('members')[0]);
        $this->assertEquals($this->guild,$response->get('name'));
    }

    /** @test */
    public function api_can_fetch_an_item()
    {
        $response = $this->wow->getItem($this->itemId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($this->itemId,$response->get('id'));
    }

    /** @test */
    public function api_can_fetch_an_item_set()
    {
        $response = $this->wow->getItemSet($this->itemSet);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($this->itemSet,$response->get('id'));
    }

    /** @test */
    // public function api_can_fetch_mount_master_list() // Currently not producing a proper json a result # see https://us.battle.net/forums/en/bnet/topic/20759527665#1
    // {
    //     $response = $this->wow->getMountMasterList();

    //     $this->assertInstanceOf(Collection::class, $response);
    //     $this->assertArrayHasKey('mounts', $response->toArray());
    //     $this->assertObjectHasAttribute('name', $response->get('mounts')[0]);
    //     $this->assertObjectHasAttribute('spellId', $response->get('mounts')[0]);
    // }

    /** @test */
    public function api_can_fetch_pet_master_list()
    {
        $response = $this->wow->getPetList();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('pets', $response->toArray());
        $this->assertObjectHasAttribute('name', $response->get('pets')[0]);
        $this->assertObjectHasAttribute('family', $response->get('pets')[0]);
    }

    /** @test */
    public function api_can_fetch_pet_abilities()
    {
        $response = $this->wow->getPetAbility($this->abilityId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($this->abilityId,$response->get('id'));
    }

    /** @test */
    public function api_can_fetch_pet_species()
    {
        $response = $this->wow->getPetSpecies($this->speciesId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('speciesId', $response->toArray());
        $this->assertEquals($this->speciesId,$response->get('speciesId'));
    }

    /** @test */
    public function api_can_fetch_pet_stats()
    {
        $response = $this->wow->getPetStats($this->speciesId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('speciesId', $response->toArray());
        $this->assertArrayHasKey('level', $response->toArray());
        $this->assertArrayHasKey('breedId', $response->toArray());
        $this->assertArrayHasKey('petQualityId', $response->toArray());
        $this->assertEquals($this->speciesId,$response->get('speciesId'));
    }

    /** @test */
    public function api_can_fetch_pet_stats_on_specific_level()
    {
        $response = $this->wow->getPetStats($this->speciesId, ['level' => $this->petLevel, 'breedId' => $this->petBreedId, 'qualityId' =>$this->petQualityId]);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('speciesId', $response->toArray());
        $this->assertArrayHasKey('level', $response->toArray());
        $this->assertArrayHasKey('breedId', $response->toArray());
        $this->assertArrayHasKey('petQualityId', $response->toArray());
        $this->assertEquals($this->speciesId,$response->get('speciesId'));
        $this->assertEquals($this->petLevel,$response->get('level'));
        $this->assertEquals($this->petBreedId,$response->get('breedId'));
        $this->assertEquals($this->petQualityId,$response->get('petQualityId'));
    }

    /** @test */
    public function api_can_fetch_pvp_leaderboards()
    {
        $response = $this->wow->getLeaderboards($this->pvpBracket);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('rows', $response->toArray());
        $this->assertObjectHasAttribute('ranking', $response->get('rows')[0]);
        $this->assertObjectHasAttribute('rating', $response->get('rows')[0]);
    }

    /** @test */
    public function api_can_fetch_quest_data()
    {
        $response = $this->wow->getQuest($this->questId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($this->questId,$response->get('id'));
    }

    /** @test */
    public function api_can_fetch_realm_status()
    {
        $response = $this->wow->getRealmStatus();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('realms', $response->toArray());
        $this->assertObjectHasAttribute('type', $response->get('realms')[0]);
        $this->assertObjectHasAttribute('population', $response->get('realms')[0]);
    }

    /** @test */
    public function api_can_fetch_a_recipe()
    {
        $response = $this->wow->getRecipe($this->recipeId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($this->recipeId,$response->get('id'));
    }

    /** @test */
    public function api_can_fetch_a_spell()
    {
        $response = $this->wow->getSpell($this->spellId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($this->spellId,$response->get('id'));
    }

    /** @test */
    public function api_can_fetch_zone_master_list()
    {
        $response = $this->wow->getZonesMasterList();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('zones', $response->toArray());
        $this->assertObjectHasAttribute('id', $response->get('zones')[0]);
        $this->assertObjectHasAttribute('name', $response->get('zones')[0]);
        $this->assertObjectHasAttribute('expansionId', $response->get('zones')[0]);
    }

    /** @test */
    public function api_can_fetch_zone_details()
    {
        $zoneId = $this->wow->getZonesMasterList()->get('zones')[0]->id;
        $response = $this->wow->getZone($zoneId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($zoneId, $response->get('id'));
    }

    /** @test */
    public function api_can_fetch_battlegroup_data()
    {
        $response = $this->wow->getDataBattlegroups();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('battlegroups', $response->toArray());
        $this->assertObjectHasAttribute('name', $response->get('battlegroups')[0]);
        $this->assertObjectHasAttribute('slug', $response->get('battlegroups')[0]);
    }
}