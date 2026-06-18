<?php

namespace Tests\Feature;

use App\Filament\SuperAdmin\Resources\Faculties\Pages\EditFaculty;
use App\Filament\SuperAdmin\Resources\Faculties\RelationManagers\UsersRelationManager as FacultyUsersRelationManager;
use App\Filament\SuperAdmin\Resources\Periods\Pages\EditPeriod;
use App\Filament\SuperAdmin\Resources\Periods\RelationManagers\UsersRelationManager as PeriodUsersRelationManager;
use App\Filament\SuperAdmin\Resources\StudyPrograms\Pages\EditStudyProgram;
use App\Filament\SuperAdmin\Resources\StudyPrograms\RelationManagers\UsersRelationManager as StudyProgramUsersRelationManager;
use App\Models\Faculty;
use App\Models\Period;
use App\Models\StudyProgram;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SuperAdminRelationManagersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_faculty_edit_page_lists_only_users_in_that_faculty(): void
    {
        $this->actingAs($this->createSuperAdmin());

        $faculty = Faculty::create(['name' => 'Fakultas A']);
        $otherFaculty = Faculty::create(['name' => 'Fakultas B']);
        $period = Period::create([
            'name' => '2026',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'is_active' => true,
        ]);

        $facultyUser = User::factory()->create([
            'name' => 'Faculty User',
            'faculty_id' => $faculty->id,
            'period_id' => $period->id,
        ]);
        $otherUser = User::factory()->create([
            'name' => 'Other Faculty User',
            'faculty_id' => $otherFaculty->id,
            'period_id' => $period->id,
        ]);

        Livewire::test(FacultyUsersRelationManager::class, [
            'ownerRecord' => $faculty,
            'pageClass' => EditFaculty::class,
        ])
            ->assertOk()
            ->assertCanSeeTableRecords([$facultyUser])
            ->assertCanNotSeeTableRecords([$otherUser]);
    }

    public function test_study_program_edit_page_lists_only_users_in_that_study_program(): void
    {
        $this->actingAs($this->createSuperAdmin());

        $faculty = Faculty::create(['name' => 'Fakultas A']);
        $studyProgram = StudyProgram::create([
            'faculty_id' => $faculty->id,
            'name' => 'Informatika',
        ]);
        $otherStudyProgram = StudyProgram::create([
            'faculty_id' => $faculty->id,
            'name' => 'Akuntansi',
        ]);
        $period = Period::create([
            'name' => '2026',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'is_active' => true,
        ]);

        $studyProgramUser = User::factory()->create([
            'name' => 'Study Program User',
            'faculty_id' => $faculty->id,
            'study_program_id' => $studyProgram->id,
            'period_id' => $period->id,
        ]);
        $otherUser = User::factory()->create([
            'name' => 'Other Study Program User',
            'faculty_id' => $faculty->id,
            'study_program_id' => $otherStudyProgram->id,
            'period_id' => $period->id,
        ]);

        Livewire::test(StudyProgramUsersRelationManager::class, [
            'ownerRecord' => $studyProgram,
            'pageClass' => EditStudyProgram::class,
        ])
            ->assertOk()
            ->assertCanSeeTableRecords([$studyProgramUser])
            ->assertCanNotSeeTableRecords([$otherUser]);
    }

    public function test_period_edit_page_lists_only_users_in_that_period(): void
    {
        $this->actingAs($this->createSuperAdmin());

        $period = Period::create([
            'name' => '2026',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'is_active' => true,
        ]);
        $otherPeriod = Period::create([
            'name' => '2027',
            'start_date' => now()->addYear(),
            'end_date' => now()->addYears(2),
            'is_active' => true,
        ]);
        $faculty = Faculty::create(['name' => 'Fakultas A']);
        $studyProgram = StudyProgram::create([
            'faculty_id' => $faculty->id,
            'name' => 'Informatika',
        ]);

        $periodUser = User::factory()->create([
            'name' => 'Period User',
            'faculty_id' => $faculty->id,
            'study_program_id' => $studyProgram->id,
            'period_id' => $period->id,
        ]);
        $otherUser = User::factory()->create([
            'name' => 'Other Period User',
            'faculty_id' => $faculty->id,
            'study_program_id' => $studyProgram->id,
            'period_id' => $otherPeriod->id,
        ]);

        Livewire::test(PeriodUsersRelationManager::class, [
            'ownerRecord' => $period,
            'pageClass' => EditPeriod::class,
        ])
            ->assertOk()
            ->assertCanSeeTableRecords([$periodUser])
            ->assertCanNotSeeTableRecords([$otherUser]);
    }

    private function createSuperAdmin(): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('super-admin');

        return $user;
    }
}
