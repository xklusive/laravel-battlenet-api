<?php

namespace Xklusive\BattlenetApi\Test;

use Illuminate\Support\Collection;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class WoWTest extends TestCase
{
    protected $wow;
    protected $realm = 'Arathor';
    protected $guild = 'Rise Legacy';
    protected $character = 'Hellodora';
    protected $achievementId = 2144;
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
    protected $spellId = 133;
    protected $bnet_token = '69ywne7vypnkhtnzhc64ufcv';

    public function setUp()
    {
        parent::setUp();

        $this->wow = app(\Xklusive\BattlenetApi\Services\WowService::class);
    }

    /** @test */
    public function api_can_fetch_wow_achievements()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => sprintf('{"id": "%s"}',$this->achievementId)
	    ])
	]);

        $response = $this->wow->getAchievement($this->achievementId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($this->achievementId, $response->get('id'));
    }

    /** @test */
    public function api_can_fetch_auction_data()
    {
       	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
		'response' => '{
		    "files": [{
		        "url": "http://auction-api-us.worldofwarcraft.com/auction-data/ea1e6287176b1df948bb9155957eb623/auctions.json",
			"lastModified": 1537538519000
                    }]
                }'
	    ])
        ]);

	$response = $this->wow->getAuctionDataStatus($this->realm);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('files', $response->toArray());
        $this->assertObjectHasAttribute('url', $response->get('files')[0]);
    }

    /** @test */
    public function api_can_fetch_supported_bosses()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
		'response' => '{
		    "bosses": [{
		        "id": 86217,
			"name": "Vigilant Kaathar",
			"urlSlug": "vigilant-kaathar"
                    }]
                }'
	    ])
	]);

        $response = $this->wow->getBossMasterList();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('bosses', $response->toArray());
    }

    /** @test */
    public function api_can_fetch_a_boss()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
		'response' => '{
		    "bosses": [{
		        "id": 86217,
			"name": "Vigilant Kaathar",
			"urlSlug": "vigilant-kaathar"
                    }]
                }'
	    ]),
	    collect([
		'code' => 200,
		'response' => '{
		    "id": 86217,
		    "name": "Vigilant Kaathar",
		    "urlSlug": "vigilant-kaathar"
		}'
	    ])
	]);

        $bosses = $this->wow->getBossMasterList();
        $bossId = collect($bosses->get('bosses'))->first()->id;
        $response = $this->wow->getBoss($bossId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertArrayHasKey('urlSlug', $response->toArray());
	$this->assertEquals($bossId, $response->get('id'));
    }

    /** @test */
    public function api_can_fetch_realm_leader_board()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => '{"challenge": [{ "map": { "id": 1763 } }] }'
	    ])
	]);

        $response = $this->wow->getRealmLeaderboard($this->realm);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('challenge', $response->toArray());
    }

    /** @test */
    public function api_can_fetch_region_leader_board()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => '{"challenge": [{ "groups": [] }] }'
	    ])
	]);

        $response = $this->wow->getRegionLeaderboard();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('challenge', $response->toArray());
    }

    /** @test */
    public function api_can_fetch_a_character()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => sprintf('{"name":"%s","class":8,"race":1,"gender":1,"level":120}',$this->character)
	    ])
	]);

        $response = $this->wow->getCharacter($this->realm, $this->character);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertArrayHasKey('class', $response->toArray());
        $this->assertArrayHasKey('race', $response->toArray());
        $this->assertArrayHasKey('gender', $response->toArray());
        $this->assertArrayHasKey('level', $response->toArray());
        $this->assertEquals($this->character, $response->get('name'));
    }

    /** @test */
    public function api_can_fetch_a_guild()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => sprintf('{"name":"%s","level":25,"side":0,"emblem":{"icon":173,"iconColor":"ff376700"}}',$this->guild)
	    ])
	]);

        $response = $this->wow->getGuild($this->realm, $this->guild);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertArrayHasKey('level', $response->toArray());
        $this->assertArrayHasKey('side', $response->toArray());
        $this->assertArrayHasKey('emblem', $response->toArray());
        $this->assertEquals($this->guild, $response->get('name'));
    }

    /** @test */
    public function api_can_fetch_guild_members_with_default_fields()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => sprintf('{"name":"%s","level":25,"realm":"Arathor","members":[{"character":{"name":"Csuvakka"}}]}',$this->guild)
	    ])
	]);

        $response = $this->wow->getGuildMembers($this->realm, $this->guild);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertArrayHasKey('realm', $response->toArray());
        $this->assertArrayHasKey('members', $response->toArray());
        $this->assertObjectHasAttribute('character', $response->get('members')[0]);
        $this->assertEquals($this->guild, $response->get('name'));
    }

    /** @test */
    public function api_can_fetch_guild_members_with_different_fields()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => sprintf('{"name":"%s","level":25,"realm":"Arathor","members":[{"character":{"name":"Csuvakka"}}]}',$this->guild)
	    ])
	]);

        $response = $this->wow->getGuildMembers($this->realm, $this->guild, ['query' => ['fields' => 'characters']]);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertArrayHasKey('realm', $response->toArray());
        $this->assertArrayHasKey('members', $response->toArray());
        $this->assertObjectHasAttribute('character', $response->get('members')[0]);
        $this->assertEquals($this->guild, $response->get('name'));
    }

    /** @test */
    public function api_can_fetch_an_item()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => sprintf('{"id":%d,"disenchantingSkillRank":0,"description":"Property of Finkle Einhorn, Grandmaster Adventurer"}',$this->itemId)
	    ])
	]);

        $response = $this->wow->getItem($this->itemId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($this->itemId, $response->get('id'));
    }

    /** @test */
    public function api_can_fetch_an_item_set()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => sprintf('{"id":%d,"name":"Deep Earth Vestments","items":[76749,76750,76751,76752,76753]}',$this->itemSet)
	    ])
	]);

        $response = $this->wow->getItemSet($this->itemSet);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertArrayHasKey('items', $response->toArray());
        $this->assertEquals($this->itemSet, $response->get('id'));
    }

    /** @test */
    public function api_can_fetch_mount_master_list()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => '{"mounts":[{"name": "High Priest\'s Lightsworn Seeker","isGround": true}]}'
	    ])
	]);

        $response = $this->wow->getMountMasterList();
        $this->assertInstanceOf(Collection::class, $response);

        $this->assertArrayHasKey('mounts', $response->toArray());
        $this->assertObjectHasAttribute('name', collect($response->get('mounts'))->first());
        $this->assertObjectHasAttribute('isGround', collect($response->get('mounts'))->first());
    }

    /** @test */
    public function api_can_fetch_pet_master_list()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => '{"pets":[{"canBattle":true,"name":"Ash\'ana","family":"beast","stats":{"speed":9}}]}'
	    ])
	]);

        $response = $this->wow->getPetList();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('pets', $response->toArray());
        $this->assertObjectHasAttribute('name', $response->get('pets')[0]);
        $this->assertObjectHasAttribute('family', $response->get('pets')[0]);
    }

    /** @test */
    public function api_can_fetch_pet_abilities()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => sprintf('{"id":%d,"name":"Toxic Smoke","cooldown":0,"rounds":1}',$this->abilityId)
	    ])
	]);

        $response = $this->wow->getPetAbility($this->abilityId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertArrayHasKey('name', $response->toArray());
        $this->assertArrayHasKey('cooldown', $response->toArray());
        $this->assertArrayHasKey('rounds', $response->toArray());
        $this->assertEquals($this->abilityId, $response->get('id'));
    }

    /** @test */
    public function api_can_fetch_pet_species()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => sprintf('{"speciesId":%d,"petTypeId":9,"creatureId":42078,"name":"Mini Thor"}',$this->speciesId)
	    ])
	]);

        $response = $this->wow->getPetSpecies($this->speciesId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('speciesId', $response->toArray());
        $this->assertEquals($this->speciesId, $response->get('speciesId'));
    }

    /** @test */
    public function api_can_fetch_pet_stats()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => sprintf('{"speciesId":%d,"breedId":3,"petQualityId":1,"level":1,"health":150,"power":10,"speed":8}',$this->speciesId)
	    ])
	]);

        $response = $this->wow->getPetStats($this->speciesId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('speciesId', $response->toArray());
        $this->assertArrayHasKey('level', $response->toArray());
        $this->assertArrayHasKey('breedId', $response->toArray());
        $this->assertArrayHasKey('petQualityId', $response->toArray());
        $this->assertEquals($this->speciesId, $response->get('speciesId'));
    }

    /** @test */
    public function api_can_fetch_pet_stats_on_specific_level()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
		'response' => sprintf('{"speciesId":%d,"breedId":%d,"petQualityId":%d,"level":%d,"health":150,"power":10,"speed":8}',
			$this->speciesId,
			$this->petBreedId,
			$this->petQualityId,
			$this->petLevel
		)
	    ])
	]);

        $response = $this->wow->getPetStats($this->speciesId, ['level' => $this->petLevel, 'breedId' => $this->petBreedId, 'qualityId' =>$this->petQualityId]);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('speciesId', $response->toArray());
        $this->assertArrayHasKey('level', $response->toArray());
        $this->assertArrayHasKey('breedId', $response->toArray());
        $this->assertArrayHasKey('petQualityId', $response->toArray());
        $this->assertEquals($this->speciesId, $response->get('speciesId'));
        $this->assertEquals($this->petLevel, $response->get('level'));
        $this->assertEquals($this->petBreedId, $response->get('breedId'));
        $this->assertEquals($this->petQualityId, $response->get('petQualityId'));
    }

    /** @test */
    public function api_can_fetch_pvp_leaderboards()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => '{"rows": [{"ranking": 1,"rating": 2707,"name": "Qwilleqwap","realmId": 1301}]}'
	    ])
	]);

        $response = $this->wow->getLeaderboards($this->pvpBracket);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('rows', $response->toArray());
        $this->assertObjectHasAttribute('ranking', $response->get('rows')[0]);
        $this->assertObjectHasAttribute('rating', $response->get('rows')[0]);
    }

    /** @test */
    public function api_can_fetch_quest_data()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => sprintf('{"id":%d,"title":"Generosity Abounds","category":"Icecrown","level":80}',$this->questId)
	    ])
	]);

        $response = $this->wow->getQuest($this->questId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($this->questId, $response->get('id'));
    }

    /** @test */
    public function api_can_fetch_realm_status()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => '{"realms": [{"type": "normal","population": "high","name": "Aegwynn"}]}'
	    ])
	]);

        $response = $this->wow->getRealmStatus();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('realms', $response->toArray());
        $this->assertObjectHasAttribute('type', $response->get('realms')[0]);
        $this->assertObjectHasAttribute('population', $response->get('realms')[0]);
    }

    /** @test */
    public function api_can_fetch_a_recipe()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => sprintf('{"id":%d,"name":"Precise Strikes","profession":"Enchanting"}',$this->recipeId)
	    ])
	]);

        $response = $this->wow->getRecipe($this->recipeId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($this->recipeId, $response->get('id'));
    }

    /** @test */
    public function api_can_fetch_a_spell()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => sprintf('{"id":%d,"name":"Test Spell","range":"20 yd range","castTime":"Instant"}',$this->spellId)
	    ])
	]);

        $response = $this->wow->getSpell($this->spellId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($this->spellId, $response->get('id'));
    }

    /** @test */
    public function api_can_fetch_zone_master_list()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => '{"zones": [{"id": 6912,"name": "Auchindoun","urlSlug": "auchindoun","expansionId": 5}] }'
	    ])
	]);

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
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => '{"zones": [{"id": 6912,"name": "Auchindoun","urlSlug": "auchindoun","expansionId": 5}] }'
	    ]),
	    collect([
	        'code' => 200,
	        'response' => '{"id": 6912, "name": "Auchindoun","urlSlug": "auchindoun"}'
	    ])
	]);

        $zoneId = $this->wow->getZonesMasterList()->get('zones')[0]->id;
        $response = $this->wow->getZone($zoneId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($zoneId, $response->get('id'));
    }

    /** @test */
    public function api_can_fetch_battlegroup_data()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => '{"battlegroups": [{"name": "Cruelty / Crueldad","slug": "cruelty-crueldad"}]}'
	    ])
	]);

        $response = $this->wow->getDataBattlegroups();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('battlegroups', $response->toArray());
        $this->assertObjectHasAttribute('name', $response->get('battlegroups')[0]);
        $this->assertObjectHasAttribute('slug', $response->get('battlegroups')[0]);
    }

    /** @test */
    public function api_can_fetch_character_races()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => '{"races":[{"id": 1,"mask": 1,"side": "alliance","name": "Human"}]}'
	    ])
	]);

        $response = $this->wow->getDataCharacterRaces();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('races', $response->toArray());
        $this->assertObjectHasAttribute('id', $response->get('races')[0]);
        $this->assertObjectHasAttribute('name', $response->get('races')[0]);
    }

    /** @test */
    public function api_can_fetch_character_classes()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => '{"classes":[{"id":1,"mask":1,"powerType":"rage","name":"Warrior"}]}'
	    ])
	]);

        $response = $this->wow->getDataCharacterClasses();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('classes', $response->toArray());
        $this->assertObjectHasAttribute('id', $response->get('classes')[0]);
        $this->assertObjectHasAttribute('name', $response->get('classes')[0]);
    }

    /** @test */
    public function api_can_fetch_character_achievements_data()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => '{"achievements":[{"id": 92,"achievements":[{"id": 6,"title": "Level 10"}]}]}'
	    ])
	]);

        $response = $this->wow->getDataCharacterAchievements();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('achievements', $response->toArray());
        $this->assertObjectHasAttribute('id', $response->get('achievements')[0]);
        $this->assertObjectHasAttribute('achievements', $response->get('achievements')[0]);
    }

    /** @test */
    public function api_can_fetch_guild_rewards_data()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
	        'response' => '{"rewards":[{"minGuildLevel": 0,"minGuildRepLevel": 5,"achievement":{"id": 6626,"title": "Working Better as a Team"}}]}'
	    ])
	]);

        $response = $this->wow->getDataGuildRewards();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('rewards', $response->toArray());
        $this->assertObjectHasAttribute('minGuildLevel', $response->get('rewards')[0]);
        $this->assertObjectHasAttribute('achievement', $response->get('rewards')[0]);
    }

    /** @test */
    public function api_can_fetch_guild_perks()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
		'response' => '{"perks":[{"guildLevel": 1,"spell":{"id": 78633}}]}'
            ])
	]);

        $response = $this->wow->getDataGuildPerks();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('perks', $response->toArray());
        $this->assertObjectHasAttribute('guildLevel', $response->get('perks')[0]);
        $this->assertObjectHasAttribute('spell', $response->get('perks')[0]);
        $this->assertObjectHasAttribute('id', $response->get('perks')[0]->spell);
    }

    /** @test */
    public function api_can_fetch_guild_achievements_data()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
		'response' => '{"achievements":[{"id": 15088,"achievements":[{"id": 5362,"title": "Everyone Needs a Logo"}]}]}'
            ])
	]);

        $response = $this->wow->getDataGuildAchievements();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('achievements', $response->toArray());
        $this->assertObjectHasAttribute('id', $response->get('achievements')[0]);
        $this->assertObjectHasAttribute('achievements', $response->get('achievements')[0]);
    }

    /** @test */
    public function api_can_fetch_item_classes_data()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
		'response' => '{"classes":[{"class":0,"name":"Consumable","subclasses":[{"subclass":0,"name":"Explosives and Devices"}]}]}'
            ])
	]);

        $response = $this->wow->getDataItemClasses();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('classes', $response->toArray());
        $this->assertObjectHasAttribute('name', $response->get('classes')[0]);
        $this->assertObjectHasAttribute('class', $response->get('classes')[0]);
    }

    /** @test */
    public function api_can_fetch_pet_types_data()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
		'response' => '{"petTypes":[{"id": 0,"key": "humanoid","name": "Humanoid","typeAbilityId": 238}]}'
            ])
	]);

        $response = $this->wow->getDataPetTypes();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('petTypes', $response->toArray());
        $this->assertObjectHasAttribute('name', $response->get('petTypes')[0]);
        $this->assertObjectHasAttribute('typeAbilityId', $response->get('petTypes')[0]);
    }

    /** @test */
    public function api_can_fetch_talent_data()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
		'response' => '{"1":{"specs":[{"name":"Arms","role":"DPS"}]}}'
            ])
	]);

        $response = $this->wow->getDataTalents();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertObjectHasAttribute('specs', $response->first());
        $this->assertObjectHasAttribute('name', $response->first()->specs[0]);
        $this->assertObjectHasAttribute('role', $response->first()->specs[0]);
    }

    /** @test */
    public function api_can_fetch_the_user_wow_characters()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 200,
		'response' => '{"characters":[{"name": "Testchar","realm": "Arathor","class": 6,"race": 5}]}'
            ])
	]);

        $options = [];
        $response = $this->wow->getProfileCharacters($options, $this->bnet_token);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('characters', $response->toArray());
        $this->assertObjectHasAttribute('name', $response->get('characters')[0]);
        $this->assertObjectHasAttribute('realm', $response->get('characters')[0]);
    }


    /** @test */
    public function api_should_fail_if_the_given_URL_is_invalid()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 404,
	        'response' => '{"status":"nok", "reason": "When in doubt, blow it up. (page not found)"'
            ])
	]);

        $response = $this->wow->getRecipe('invalid');
        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('error', $response->toArray());
    }

    /** @test */
    public function api_should_fail_if_given_battlenet_domain_is_invalid()
    {
        $oldDomain = config('battlenet-api.domain');
        config(['battlenet-api.domain' => 'not a valid domain']);

        $wowClient = app(\Xklusive\BattlenetApi\Services\WowService::class);
        $response = $wowClient->getDataPetTypes();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('error', $response->toArray());

        config(['battlenet-api.domain' => $oldDomain]);
    }

    /** @test */
    public function api_should_fail_if_access_token_is_expired()
    {
	$this->wow->createMockResponse([
	    collect([
	        'code' => 401,
	        'response' => '{"code": 403,"type": "Forbidden","detail": "Not Authorized"}'
            ])
	]);

        $options = [];
        $response = $this->wow->getProfileCharacters($options, $this->bnet_token);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(401, $response->get('error')->get('code'));
        $this->assertArrayHasKey('error', $response->toArray());
    }

    /** @test */
    public function api_should_retry_in_case_of_504() {
        $this->wow->createMockResponse([
	    collect([
	        'code' => 504,
	        'response' => '{"code": "504","type": "Gateway Time-out"}'
	    ]),
	    collect([
	        'code' => 504,
	        'response' => '{"code": "504","type": "Gateway Time-out"}'
	    ]),
	    collect([
	        'code' => 200,
	        'response' => sprintf('{"id":%d,"name":"Test Spell","range":"20 yd range","castTime":"Instant"}',$this->spellId)
	    ])
	]);

        $response = $this->wow->getSpell($this->spellId);
        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('id', $response->toArray());
        $this->assertEquals($this->spellId, $response->get('id'));

        $this->assertArrayHasKey('attempts', $response->toArray());
        $this->assertEquals(2, $response->get('attempts'));
    }

    /** @test */
    public function api_should_fail_after_maximum_number_of_retries() {
        $this->wow->createMockResponse([
	    collect([
	        'code' => 504,
	        'response' => '{"code": "504","type": "Gateway Time-out"}'
	    ]),
	    collect([
	        'code' => 504,
	        'response' => '{"code": "504","type": "Gateway Time-out"}'
	    ]),
	    collect([
	        'code' => 504,
	        'response' => '{"code": "504","type": "Gateway Time-out"}'
	    ])
	]);

        $response = $this->wow->getSpell($this->spellId);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertArrayHasKey('error', $response->toArray());
        $this->assertArrayHasKey('code', $response->get('error')->toArray());
        $this->assertEquals(504, $response->get('error')->get('code'));

    }
}
