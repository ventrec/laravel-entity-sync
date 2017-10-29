<?php

namespace Ventrec\LaravelEntitySync\Test;

use Ventrec\LaravelEntitySync\Test\Models\Article;
use Ventrec\LaravelEntitySync\Test\Models\Page;

class EnabledSyncTest extends TestCase
{
    public function testEntityIsSyncedOnCreate()
    {
        $article = Article::create(['name' => 'TestArticle']);
        $data = $this->getJsonBodyFromLatestRequest();

        $this->assertEquals($article->id, $data->entity->id);
        $this->assertEquals('article', $data->name);
    }

    public function testEntityIsSyncedOnUpdate()
    {
        $article = Article::findOrFail(2);
        $article->update(['name' => 'Nice read']);
        $data = $this->getJsonBodyFromLatestRequest();

        $this->assertEquals($article->name, $data->entity->name);
        $this->assertEquals('article', $data->name);
    }

    public function testEntityIsSyncedOnDelete()
    {
        $article = Article::findOrFail(2);
        $article->delete();
        $data = $this->getJsonBodyFromLatestRequest();
        $action = $this->getActionFromLastetRequest();

        $this->assertEquals('delete', $action);
        $this->assertEquals($article->id, $data->entity_id);
        $this->assertEquals('article', $data->name);
    }

    public function testEntityIsSyncedWhenForceDeleted()
    {
        $page = Page::findOrFail(2);
        $page->forceDelete();
        $data = $this->getJsonBodyFromLatestRequest();
        $action = $this->getActionFromLastetRequest();

        $this->assertEquals('delete', $action);
        $this->assertEquals($page->id, $data->entity_id);
        $this->assertTrue($data->force_delete);
    }
}
