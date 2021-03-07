<?php

use App\Models\Day;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;

class DayTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $resource = '/days';

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_get_all()
    {
        $days = Day::factory()->count(10)->create();

        $this->get($this->resource);

        $this->response->assertStatus(200);
        $this->response->assertJson([
            'data' => $days->toArray()
        ]);
    }

    public function test_get_single()
    {
        /** @var Day $day */
        $day = Day::factory()->create();

        $this->get("$this->resource/$day->id");

        $this->response->assertStatus(200);
        $this->response->assertJson($day->toArray());
    }

    public function test_store()
    {
        $payload = [
            'title' => 'Test day',
            'information' => 'This is only for testing',
            'date' => Carbon::now(),
        ];

        $this->post($this->resource, $payload);

        $this->response->assertStatus(201);
        $this->response->assertJsonFragment($payload);

        $this->assertDatabaseHas((new Day)->getTable(), $payload);
    }

    public function test_update()
    {
        /** @var Day $day */
        $day = Day::factory()->create();

        $payload = [
            'title' => 'Test day',
            'information' => 'This is only for testing',
            'date' => Carbon::now(),
        ];

        $this->put("$this->resource/$day->id", $payload);

        $this->response->assertStatus(200);
        $this->response->assertJsonFragment($payload);

        $table = (new Day)->getTable();

        $this->assertDatabaseHas($table, $payload);
        $this->assertDatabaseMissing($table, $day->toArray());
    }

    public function test_delete()
    {
        /** @var Day $day */
        $day = Day::factory()->create();

        $this->delete("$this->resource/$day->id");

        $this->response->assertStatus(204);
        $this->assertDatabaseMissing((new Day())->getTable(), $day->toArray());
    }
}
