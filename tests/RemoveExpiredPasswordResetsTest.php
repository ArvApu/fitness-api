<?php

use App\Console\Commands\RemoveExpiredPasswordResets;
use App\Models\PasswordReset;
use Laravel\Lumen\Testing\DatabaseMigrations;

class RemoveExpiredPasswordResetsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $command = RemoveExpiredPasswordResets::class;

    public function test_if_removes_expired_password_resets()
    {
        $p1 = PasswordReset::factory()->expired()->create();
        $p2 = PasswordReset::factory()->expired()->create();

        $this->artisan($this->command);

        $table = (new PasswordReset())->getTable();

        $this->assertDatabaseMissing($table, ['id' => $p1->id]);
        $this->assertDatabaseMissing($table, ['id' => $p2->id]);
    }

    public function test_if_does_not_remove_not_expired_password_resets()
    {
        $p1 = PasswordReset::factory()->create();
        $p2 = PasswordReset::factory()->create();

        $this->artisan($this->command);

        $table = (new PasswordReset())->getTable();

        $this->assertDatabaseHas($table, ['id' => $p1->id]);
        $this->assertDatabaseHas($table, ['id' => $p2->id]);
    }

    public function test_if_leaves_out_not_expired_password_resets()
    {
        $p1 = PasswordReset::factory()->expired()->create();
        $p2 = PasswordReset::factory()->create();

        $this->artisan($this->command);

        $table = (new PasswordReset())->getTable();

        $this->assertDatabaseMissing($table, ['id' => $p1->id]);
        $this->assertDatabaseHas($table, ['id' => $p2->id]);
    }
}
