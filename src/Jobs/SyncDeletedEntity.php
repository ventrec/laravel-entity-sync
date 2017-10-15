<?php

namespace Ventrec\LaravelEntitySync\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncDeletedEntity implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * @var string
     */
    private $entityName;
    /**
     * @var int
     */
    private $entityId;
    /**
     * @var boolean
     */
    private $forceDelete;

    public function __construct($entityName, $entityId, $forceDelete)
    {
        $this->entityName = $entityName;
        $this->entityId = $entityId;
        $this->forceDelete = $forceDelete;
    }

    public function handle(Client $client)
    {
        $client->delete(
            config('laravelEntitySync.endpoint'),
            [
                'headers' => [
                    'X-AUTH-API-TOKEN' => config('laravelEntitySync.api_auth_token'),
                ],
                'json' => [
                    'entity_id' => $this->entityId,
                    'name' => $this->entityName,
                    'force_delete' => $this->forceDelete,
                ],
            ]
        );
    }
}
