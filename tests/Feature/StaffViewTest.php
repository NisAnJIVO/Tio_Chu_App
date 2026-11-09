<?php

namespace Tests\Feature;

use App\Models\NightSession;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffViewTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::where('email', 'DonLudo@gmail.com')->first();
    }

    public function test_staff_index_renders_with_day_todos(): void
    {
        $response = $this->actingAs($this->user)->get('/staff?day=todos');
        $response->assertStatus(200);
        $response->assertSee('Personal y Turnos');
    }

    public function test_staff_index_renders_for_each_day_filter(): void
    {
        $days = ['viernes', 'sabado', 'domingo', 'todos'];
        foreach ($days as $day) {
            $response = $this->actingAs($this->user)->get("/staff?day={$day}");
            $response->assertStatus(200);
            $response->assertSee('Personal y Turnos');
        }
    }

    public function test_staff_index_renders_without_active_session(): void
    {
        NightSession::query()->update(['status' => 'closed']);

        $response = $this->actingAs($this->user)->get('/staff?day=todos');
        $response->assertStatus(200);
        $response->assertSee('Personal y Turnos');
    }
}

