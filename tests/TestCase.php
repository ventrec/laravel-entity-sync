<?php

namespace Ventrec\LaravelEntitySync\Test;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Ventrec\LaravelEntitySync\LaravelEntitySyncProvider;
use Ventrec\LaravelEntitySync\Test\Models\Article;
use Ventrec\LaravelEntitySync\Test\Models\Page;

abstract class TestCase extends OrchestraTestCase
{
    protected $requestHistory = [];

    public function setUp()
    {
        parent::setUp();

        $this->mockGuzzle();
        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelEntitySyncProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('queue.default', 'sync');
        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => $this->getTempDirectory() . '/database.sqlite',
            'prefix' => '',
        ]);

        $app['config']->set('app.key', '6rE9Nz59bGRbeMATftriyQjrpF7DcOQm');

        $app['config']->set('laravelEntitySync.enabled', true);
        $app['config']->set('laravelEntitySync.endpoint', '');
        // Initialize with empty array first in order to avoid triggering syncing of seed data
        $app['config']->set('laravelEntitySync.entities', [
            Article::class,
            Page::class,
        ]);
    }

    protected function setUpDatabase()
    {
        $this->resetDatabase();

        $this->createTables('articles', 'pages');
        $this->seedModels(Article::class, Page::class);
    }

    protected function resetDatabase()
    {
        file_put_contents($this->getTempDirectory().'/database.sqlite', null);
    }

    public function getTempDirectory()
    {
        return __DIR__ . '/temp';
    }

    protected function createTables(...$tableNames)
    {
        collect($tableNames)->each(function ($tableName) {
            $this->app['db']->connection()->getSchemaBuilder()->create($tableName, function (Blueprint $table) use ($tableName) {
                $table->increments('id');
                $table->string('name')->nullable();

                if ($tableName === 'pages') {
                    $table->timestamp('deleted_at')->nullable();
                }

                $table->timestamps();
            });
        });
    }

    protected function seedModels(...$modelClasses)
    {
        collect($modelClasses)->each(function ($modelClass) {
            foreach (range(1, 0) as $index) {
                $modelClass::create(['name' => "name {$index}"]);
            }
        });
    }

    protected function mockGuzzle()
    {
        $mock = new MockHandler([
            new Response(200),
            new Response(200),
            new Response(200),
            new Response(200),
            new Response(200),
            new Response(200),
            new Response(200),
        ]);

        $stack = HandlerStack::create($mock);
        $history = Middleware::history($this->requestHistory);

        // Push a history middleware in order to track all requests
        $stack->push($history);

        $client = new Client(['handler' => $stack]);

        app()->singleton(
            Client::class,
            function () use ($client) {
                return $client;
            }
        );
    }

    public function doNotMarkAsRisky()
    {
        $this->assertTrue(true);
    }

    public function getTheLatestRequest()
    {
        return $this->requestHistory[count($this->requestHistory) - 1];
    }

    public function getJsonBodyFromLatestRequest()
    {
        return json_decode($this->getTheLatestRequest()['request']->getBody()->getContents());
    }

    public function getActionFromLastetRequest()
    {
        $actions = [
            'post' => 'create',
            'patch' => 'update',
            'delete' => 'delete',
        ];

        $latestRequest = $this->getTheLatestRequest()['request'];

        return $actions[strtolower($latestRequest->getMethod())];
    }
}
