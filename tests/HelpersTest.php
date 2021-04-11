<?php

use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class HelpersTest extends TestCase
{
    public function test_take_using_array()
    {
        $data = [
            'id' => 1,
            'name' => 'fake_data',
            'foo' => 'bar'
        ];

        $result = take($data, 'foo');

        $this->assertEquals('bar', $result);

        $this->assertArrayNotHasKey('foo', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
    }

    public function test_take_using_object()
    {
        $data = (object) [
            'id' => 1,
            'name' => 'fake_data',
            'foo' => 'bar'
        ];

        $result = take($data, 'foo');

        $this->assertEquals('bar', $result);

        $this->assertObjectNotHasAttribute('foo', $data);
        $this->assertObjectHasAttribute('id', $data);
        $this->assertObjectHasAttribute('name', $data);
    }
}
