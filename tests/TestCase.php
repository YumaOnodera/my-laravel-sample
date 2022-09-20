<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function tearDown(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('posts')->truncate();
        DB::table('users')->truncate();

        Schema::enableForeignKeyConstraints();
    }
}
