<?php

namespace Xklusive\BattlenetApi\Services;

use Illuminate\Support\Collection;
use Xklusive\BattlenetApi\BattlenetHttpClient;

/**
 * @author Guillaume Meheust <xklusive91@gmail.com>
 */
class DiabloService extends BattlenetHttpClient
{
    /**
     * {@inheritdoc}
     */
    protected $gameParam = '/d3';


    /**
     * Get a Hero profile data
     *
     * Returns the hero profile of a Battle Tag's hero
     *
     * @param string $battleTag Battle Tag in name-#### format (ie. Noob-1234)
     * @param array ...$options
     * @return \GuzzleHttp\Exception\ClientException|Collection
     */
    public function getCareerProfile($battleTag, ...$options)
    {
        return $this->cache('/profile/'.(string) $battleTag.'/', $options, __FUNCTION__);
    }

    /**
     * Get profile information
     *
     * Returns the hero profile of a Battle Tag's hero
     *
     * @param string $battleTag Battle Tag in name-#### format (ie. Noob-1234)
     * @param int $heroId The hero id of the hero to look up
     * @param array ...$options
     * @return \GuzzleHttp\Exception\ClientException|Collection
     */
    public function getHeroProfile($battleTag, $heroId, ...$options)
    {
        return $this->cache('/profile/'.(string) $battleTag.'/hero/'.(int) $heroId, $options, __FUNCTION__);
    }

    /**
     * Get data about an item
     *
     * Returns data for a profile item
     *
     * @param string $itemDataString The item data string (from a profile) containing the item to lookup
     * @param array ...$options
     * @return \GuzzleHttp\Exception\ClientException|Collection
     */
    public function getItem($itemDataString, ...$options)
    {
        return $this->cache('/data/item/'.(string) $itemDataString, $options, __FUNCTION__);
    }

    /**
     * Get data about a follower
     *
     * Returns data for a follower
     *
     * @param string $follower possible values (templar, scoundrel, enchantress)
     * @param array ...$options
     * @return \GuzzleHttp\Exception\ClientException|Collection
     */
    public function getFollowerData($follower, ...$options)
    {
        return $this->cache('/data/follower/'.(string) $follower, $options, __FUNCTION__);
    }

    /**
     * Get data about an artisan
     *
     * Returns data for an artisan
     *
     * @param string $artisan The data about an artisan. Possible values (blacksmith, jeweler, mystic)
     * @param array ...$options
     * @return \GuzzleHttp\Exception\ClientException|Collection
     */
    public function getArtisanData($artisan, ...$options)
    {
        return $this->cache('/data/artisan/'.(string) $artisan, $options, __FUNCTION__);
    }
}
