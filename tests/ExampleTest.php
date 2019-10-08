<?php

use Jenky\LaravelAPI\Test\FeatureTestCase;

class ExampleTest extends FeatureTestCase
{
    public function test_api_v1_prefix()
    {
        $this->getJson('/api/v1')
            ->assertStatus(200)
            ->assertJson([
                'version' => [
                    'set' => 'v1',
                    'route' => 'v1',
                ],
            ]);
    }

    public function test_api_v2_prefix()
    {
        $this->getJson('/api/v2')
            ->assertStatus(200)
            ->assertJson([
                'version' => [
                    'set' => 'v2',
                    'route' => 'v2',
                ],
            ]);
    }

    public function test_example_3()
    {
        $this->assertTrue(true);
    }
}
