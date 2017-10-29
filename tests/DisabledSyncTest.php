<?php

namespace Ventrec\LaravelEntitySync\Test;

use Ventrec\LaravelEntitySync\Test\Models\Article;

class DisabledSyncTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testCreateEntityWithoutSyncing()
    {
        $this->disableSync();

        $article = Article::create(['name' => 'Testing']);
        $data = $this->getJsonBodyFromLatestRequest();

        $this->assertNotEquals($article->id, $data->entity->id);
        $this->assertNotEquals('article', $data->name);
    }

    public function testUpdateEntityWithoutSyncing()
    {
        $this->disableSync();

        $article = Article::findOrFail(2);
        $article->update(['name' => 'Super']);

        $data = $this->getJsonBodyFromLatestRequest();

        $this->assertNotEquals($article->name, $data->entity->name);
        $this->assertNotEquals('article', $data->name);
    }

    public function testDeleteEntityWithoutSyncing()
    {
        $this->disableSync();

        $article = Article::findOrFail(2);
        $article->delete();

        $action = $this->getActionFromLastetRequest();
        $data = $this->getJsonBodyFromLatestRequest();

        $this->assertNotEquals('delete', $action);
        $this->assertNotEquals('article', $data->name);
    }

    protected function disableSync()
    {
        // Disable sync for this test
        $this->app['config']->set('laravelEntitySync.enabled', false);
    }
}
