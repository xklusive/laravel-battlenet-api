<?php

namespace Xklusive\BattlenetApi\Services;

use Illuminate\Support\Collection;
use Xklusive\BattlenetApi\BattlenetHttpClient;

/**
 * @author Guillaume Meheust <xklusive91@gmail.com>
 */
class WowService extends BattlenetHttpClient
{
    /**
     * {@inheritdoc}
     */
    protected $gameParam = '/wow';

    /**
     * Get achievement information by id.
     *
     * This provides data about an individual achievement
     *
     * @param int   $achievementId Id of the achievement to retrieve
     * @param array $options       Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getAchievement($achievementId, array $options = [])
    {
        return $this->cache('/achievement/'.(int) $achievementId, $options, __FUNCTION__);
    }

    /**
     * Get auction data.
     *
     * Auction APIs currently provide rolling batches of data about current auctions. Fetching auction dumps is a two
     * step process that involves checking a per-realm index file to determine if a recent dump has been generated and
     * then fetching the most recently generated dump file if necessary.
     *
     * This API resource provides a per-realm list of recently generated auction house data dumps
     *
     * @param string $realm   Realm being requested
     * @param array  $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getAuctionDataStatus($realm, array $options = [])
    {
        return $this->cache('/auction/data/'.(string) $realm, $options, __FUNCTION__);
    }

    /**
     * Get boss master list.
     *
     * A list of all supported bosses. A 'boss' in this context should be considered a boss encounter, which may include
     * more than one NPC.
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getBossMasterList(array $options = [])
    {
        return $this->cache('/boss/', $options, __FUNCTION__);
    }

    /**
     * Get boss information by id.
     *
     * The boss API provides information about bosses. A 'boss' in this context should be considered a boss encounter,
     * which may include more than one NPC.
     *
     * @param int   $bossId  Id of the boss you want to retrieve
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getBoss($bossId, array $options = [])
    {
        return $this->cache('/boss/'.(int) $bossId, $options, __FUNCTION__);
    }

    /**
     * Get realm leaderboards.
     *
     * The data in this request has data for all 9 challenge mode maps (currently). The map field includes the current
     * medal times for each dungeon. Inside each ladder we provide data about each character that was part of each run.
     * The character data includes the current cached spec of the character while the member field includes the spec of
     * the character during the challenge mode run.
     *
     * @param string $realm   Realm's name being requested
     * @param array  $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getRealmLeaderboard($realm, array $options = [])
    {
        return $this->cache('/challenge/'.(string) $realm, $options, __FUNCTION__);
    }

    /**
     * Get region leaderboards.
     *
     * The region leaderboard has the exact same data format as the realm leaderboards except there is no realm field.
     * It is simply the top 100 results gathered for each map for all of the available realm leaderboards in a region.
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getRegionLeaderboard(array $options = [])
    {
        return $this->cache('/challenge/region', $options, __FUNCTION__);
    }

    /**
     * Get character information.
     *
     * The Character Profile API is the primary way to access character information. This Character Profile API can be
     * used to fetch a single character at a time through an HTTP GET request to a URL describing the character profile
     * resource. By default, a basic dataset will be returned and with each request and zero or more additional fields
     * can be retrieved. To access this API, craft a resource URL pointing to the character who's information is to be
     * retrieved
     *
     * @param string $realm         Character's realm. Can be provided as the proper realm name or the normalized realm name
     * @param string $characterName Name of the character you want to retrieve
     * @param array  $options       Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getCharacter($realm, $characterName, array $options = [])
    {
        return $this->cache('/character/'.(string) $realm.'/'.(string) $characterName, $options, __FUNCTION__);
    }

    /**
     * Get guild profile.
     *
     * The guild profile API is the primary way to access guild information. This guild profile API can be used to fetch
     * a single guild at a time through an HTTP GET request to a url describing the guild profile resource. By default,
     * a basic dataset will be returned and with each request and zero or more additional fields can be retrieved.
     *
     * There are no required query string parameters when accessing this resource, although the fields query string
     * parameter can optionally be passed to indicate that one or more of the optional datasets is to be retrieved.
     * Those additional fields are listed in the method titled "Optional Fields".
     *
     * @param string $realm     Realm the guild lives on
     * @param string $guildName Name of the guild being retrieved
     * @param array  $options   Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getGuild($realm, $guildName, array $options = [])
    {
        return $this->cache('/guild/'.(string) $realm.'/'.(string) $guildName, $options, __FUNCTION__);
    }

    /**
     * Get guild members.
     *
     * This provides a list of characters currently in the guild Roster.
     * This is a wrapper function for the getGuild data function for easier access.
     *
     * @param string $realm     Realm the guild lives on
     * @param string $guildName Name of the guild being retrieved
     * @param array  $options   Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getGuildMembers($realm, $guildName, array $options = [])
    {
        $options = $this->wrapCollection($options);
        $query = $this->wrapCollection($options->get('query'));
        $fields = $this->wrapCollection($query->get('fields'));

        if ($fields->isEmpty()) {
            // The options doesn't contain a fields section which is required for this query.
            // Lets add one with the default value 'members'
            $query->put('fields', 'members');
        }

        if ($fields->contains('members') === false) {
            // The options does contain a fileds element but 'members' is not part of it
            // Lets add one 'members' to the list.
            $fields->push('members');
            $query->put('fields', $fields->implode(','));
        }

        $options->put('query', $query);

        // We have everything what we need, lets call the getGuild function to proceed
        return $this->getGuild($realm, $guildName, $options->toArray());
    }

    /**
     * Get item information by id.
     *
     * The item API provides detailed item information. This includes item set information if this item is part of a set.
     *
     * @param int   $itemId  Id of the item being retrieved
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getItem($itemId, array $options = [])
    {
        return $this->cache('/item/'.(int) $itemId, $options, __FUNCTION__);
    }

    /**
     * Get set information by id.
     *
     * The item API provides detailed item information. This includes item set information if this item is part of a set.
     *
     * @param int   $setId   Id of the set being requested
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getItemSet($setId, array $options = [])
    {
        return $this->cache('/item/set/'.(int) $setId, $options, __FUNCTION__);
    }

    /**
     * Get mount master list.
     *
     * A list of all supported mounts
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getMountMasterList(array $options = [])
    {
        return collect($options);
    }

    /**
     * Get pet lists.
     *
     * A list of all supported battle and vanity pets
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getPetList(array $options = [])
    {
        return $this->cache('/pet/', $options, __FUNCTION__);
    }

    /**
     * Get pet ability information by id.
     *
     * This provides data about a individual battle pet ability ID. We do not provide the tooltip for the ability yet.
     * We are working on a better way to provide this since it depends on your pet's species, level and quality rolls.
     *
     * @param int   $abilityId Id of the ability you want to retrieve
     * @param array $options   Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getPetAbility($abilityId, array $options = [])
    {
        return $this->cache('/pet/ability/'.(int) $abilityId, $options, __FUNCTION__);
    }

    /**
     * Get pet species information by id.
     *
     * This provides the data about an individual pet species. The species IDs can be found your character profile
     * using the options pets field. Each species also has data about what it's 6 abilities are.
     *
     * @param int   $speciesId The species you want to retrieve
     * @param array $options   Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getPetSpecies($speciesId, array $options = [])
    {
        return $this->cache('/pet/species/'.(int) $speciesId, $options, __FUNCTION__);
    }

    /**
     * Get pet stats by species id.
     *
     * Retrieve detailed information about a given species of pet
     *
     * @param int   $speciesId Pet's species id. This can be found by querying a users' list of pets via the Character Profile API
     * @param array $options   Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getPetStats($speciesId, array $options = [])
    {
        // Create a collection from the options, easier to work with.
        $options = $this->wrapCollection($options);

        foreach ($options as $key => $option) {
            if (in_array($key, ['level', 'breedId', 'qualityId'])) {
                // We have some valid options for this query, lets add them to the query options
                if ($options->has('query') === false) {
                    // We don't have any query options. Lets create an empty collection.
                    $options->put('query', collect());
                }

                // We already have some query options lets add the new ones to the list.
                $options->get('query')->put($key, $option);
            }
        }

        return $this->cache('/pet/stats/'.(int) $speciesId, $options->toArray(), __FUNCTION__);
    }

    /**
     * Get leaderboards.
     *
     * The Leaderboard API endpoint provides leaderboard information for the 2v2, 3v3, 5v5 and Rated Battleground
     * leaderboards.
     *
     * @param int   $bracket Type of leaderboard you want to retrieve. Example: 2v2, 3v3, 5v5, and rbg
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getLeaderboards($bracket, array $options = [])
    {
        return $this->cache('/leaderboard/'.(string) $bracket, $options, __FUNCTION__);
    }

    /**
     * Get quest information by id.
     *
     * Retrieve metadata for a given quest.
     *
     * @param int   $questId Id of the quest you want to retrieve
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getQuest($questId, array $options = [])
    {
        return $this->cache('/quest/'.(int) $questId, $options, __FUNCTION__);
    }

    /**
     * Get realm status.
     *
     * The realm status API allows developers to retrieve realm status information. This information is limited to
     * whether or not the realm is up, the type and state of the realm, the current population, and the status of the
     * two world PvP zones
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getRealmStatus(array $options = [])
    {
        return $this->cache('/realm/status', $options, __FUNCTION__);
    }

    /**
     * Get recipe information by id.
     *
     * The recipe API provides basic recipe information
     *
     * @param int   $recipeId Id for the desired recipe
     * @param array $options  Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getRecipe($recipeId, array $options = [])
    {
        return $this->cache('/recipe/'.(int) $recipeId, $options, __FUNCTION__);
    }

    /**
     * Get spell information by id.
     *
     * The spell API provides some information about spells
     *
     * @param int   $spellId Id of the desired spell
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getSpell($spellId, array $options = [])
    {
        return $this->cache('/spell/'.(int) $spellId, $options, __FUNCTION__);
    }

    /**
     * Get zone master list.
     *
     * A list of all supported zones and their bosses. A 'zone' in this context should be considered a dungeon, or a
     * raid, not a zone as in a world zone. A 'boss' in this context should be considered a boss encounter, which may
     * include more than one NPC.
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getZonesMasterList(array $options = [])
    {
        return $this->cache('/zone/', $options, __FUNCTION__);
    }

    /**
     * Get zone information by id.
     *
     * The Zone API provides some information about zones.
     *
     * @param int   $zoneId  Id of the zone you want to retrieve
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getZone($zoneId, array $options = [])
    {
        return $this->cache('/zone/'.(int) $zoneId, $options, __FUNCTION__);
    }

    /**
     * Get data battlegroups.
     *
     * The battlegroups data API provides the list of battlegroups for this region. Please note the trailing '/' on this URL
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getDataBattlegroups(array $options = [])
    {
        return $this->cache('/data/battlegroups/', $options, __FUNCTION__);
    }

    /**
     * Get data character races.
     *
     * The character races data API provides a list of each race and their associated faction, name, unique ID, and skin
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getDataCharacterRaces(array $options = [])
    {
        return $this->cache('/data/character/races', $options, __FUNCTION__);
    }

    /**
     * Get data character classes.
     *
     * The character classes data API provides a list of character classes
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getDataCharacterClasses(array $options = [])
    {
        return $this->cache('/data/character/classes', $options, __FUNCTION__);
    }

    /**
     * Get data character achievements.
     *
     * The character achievements data API provides a list of all of the achievements that characters can earn as well
     * as the category structure and hierarchy
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getDataCharacterAchievements(array $options = [])
    {
        return $this->cache('/data/character/achievements', $options, __FUNCTION__);
    }

    /**
     * Get data guild rewards.
     *
     * The guild rewards data API provides a list of all guild rewards
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getDataGuildRewards(array $options = [])
    {
        return $this->cache('/data/guild/rewards', $options, __FUNCTION__);
    }

    /**
     * Get data guild perks.
     *
     * The guild perks data API provides a list of all guild perks
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getDataGuildPerks(array $options = [])
    {
        return $this->cache('/data/guild/perks', $options, __FUNCTION__);
    }

    /**
     * Get data guild achievements.
     *
     * The guild achievements data API provides a list of all of the achievements that guilds can earn as well as the
     * category structure and hierarchy
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getDataGuildAchievements(array $options = [])
    {
        return $this->cache('/data/guild/achievements', $options, __FUNCTION__);
    }

    /**
     * Get data item classes.
     *
     * The item classes data API provides a list of item classes
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getDataItemClasses(array $options = [])
    {
        return $this->cache('/data/item/classes', $options, __FUNCTION__);
    }

    /**
     * Get data talents.
     *
     * The talents data API provides a list of talents, specs and glyphs for each class
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getDataTalents(array $options = [])
    {
        return $this->cache('/data/talents', $options, __FUNCTION__);
    }

    /**
     * Get data pet types.
     *
     * The different bat pet types (including what they are strong and weak against)
     *
     * @param array $options Options
     *
     * @return null|\Xklusive\BattlenetApi\Collection api response
     */
    public function getDataPetTypes(array $options = [])
    {
        return $this->cache('/data/pet/types', $options, __FUNCTION__);
    }

    /**
     * Get profile characters.
     *
     * This provides data about the current logged in OAuth user's - or the given user - WoW profile
     *
     * @param array $options Options
     *
     * @return Illuminate\Support\Collection api response
     */
    public function getProfileCharacters(array $options = [])
    {
        $options = $this->wrapCollection($options);
        $query = $this->wrapCollection($options->get('query'));
        $cache = $this->wrapCollection($options->get('cache'));

        $query->push('access_token',$this->getAccessToken($options));
        $cache->push('user_id',$this->getUserId($options));

        if (strpos($this->getScope($options), 'wow.profile') === true) {
            return $this->cache('/user/characters', $options->toArray(), __FUNCTION__);
        }

        return;
    }

    /**
     * Return the access token from the options, if it is provided.
     * Otherwise get the logged in users access_token.
     *
     * @param array $options Options
     *
     * @return string access_token
     */
    private function getAccessToken(array $options = [])
    {
        $options = $this->wrapCollection($options);
        return ($options->has('access_token') 
            ? $options->get('access_token') 
            : auth()->user()->bnet_token);
    }

    /**
     * Return the access_scope from the options, if it is provided.
     * Otherwise get the logged in users access_scope.
     *
     * @param array $options Options
     *
     * @return string access_scope
     */
    private function getScope(array $options = [])
    {
        $options = $this->wrapCollection($options);
        return ($options->has('access_scope') 
            ? $options->get('access_scope') 
            : auth()->user()->bnet_scope);
    }

    /**
     * Return the user_id from the options, if it is provided.
     * Otherwise get the logged in users user_id.
     *
     * @param array $options Options
     *
     * @return string user_id
     */
    private function getUserId(array $options = [])
    {
        $options = $this->wrapCollection($options);
        return ($options->has('user_id') 
            ? $options->get('user_id') 
            : auth()->user()->id);
    }
}
