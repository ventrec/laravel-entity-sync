<?php

namespace Ventrec\LaravelEntitySync\Test;

use Ventrec\LaravelEntitySync\Test\Models\Article;

class BasicTest extends TestCase
{
    public function testDataExistsInDatabase()
    {
        $articles = Article::all();

        $this->assertCount(2, $articles);
    }
}
