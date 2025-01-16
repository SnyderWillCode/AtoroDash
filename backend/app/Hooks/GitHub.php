<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * # MythicalSystems License v2.0
 *
 * ## Copyright (c) 2021–2025 MythicalSystems and Cassian Gherman
 *
 * Breaking any of the following rules will result in a permanent ban from the MythicalSystems community and all of its services.
 */

namespace MythicalClient\Hooks;

use GuzzleHttp\Client;
use MythicalClient\Cache\Cache;

class GitHub
{
    private $cacheKey = 'github_repo_data';
    private $cacheTTL = 3600; // 1 hour in seconds
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Retrieves repository data from GitHub API, using cache if available.
     *
     * @return array the repository data
     */
    public function getRepoData()
    {
        // Check if data is cached
        if (Cache::exists($this->cacheKey)) {
            return Cache::getJson($this->cacheKey);
        }

        // Make GET request to GitHub API
        $response = $this->client->request('GET', 'https://api.github.com/repos/mythicalltd/mythicaldash', [
            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
                'User-Agent' => 'MythicalClient',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        // Cache the response
        Cache::putJson($this->cacheKey, $data, $this->cacheTTL);

        return $data;
    }
}
