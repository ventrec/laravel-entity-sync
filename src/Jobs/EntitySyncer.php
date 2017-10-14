<?php

namespace Ventrec\LaravelEntitySync\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Ventrec\LaravelEntitySync\Exceptions\InvalidActionException;

class EntitySyncer implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    private $model;
    /**
     * A string that will be either 'create', 'update' or 'delete'
     * @var String
     */
    private $event;
    private $actions = [
        'created' => 'post',
        'updated' => 'patch',
        'deleted' => 'delete',
    ];
    /**
     * @var String
     */
    private $entityName;

    public function __construct($model, $entityName, $event)
    {
        $this->model = $model;
        $this->event = $event;
        $this->entityName = $entityName;
    }

    public function handle(Client $client)
    {
        $client->{$this->resolveAction()}(
            config('laravelEntitySync.endpoint'),
            [
                'headers' => [
                    'X-AUTH-API-TOKEN' => config('laravelEntitySync.api_auth_token'),
                ],
                'json' => [
                    'entity' => $this->model,
                    'name' => $this->entityName,
                ],
            ]
        );
    }

    private function resolveAction()
    {
        if (!isset($this->actions[$this->event])) {
            throw new InvalidActionException("No actions is defined for the event {$this->event}");
        }

        return $this->actions[$this->event];
    }
}
