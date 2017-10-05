<?php

return [
    /**
     * Specify the different entities you would like to sync
     * Example:
     * [
     *      \App\Models\User::class,
     * ]
     */
    'entities' => [],

    /**
     * The endpoint that the entities will be synced towards
     */
    'endpoint' => '',

    /**
     * The auth token that should be used to verify the request at the other end
     */
    'api_auth_token' => '',
];
