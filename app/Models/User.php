<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'username', 'password', 'is_active', 'faculty', 'study_program', 'faculty_id', 'study_program_id', 'period_id'])]
#[Hidden(['password', 'remember_token', 'is_active'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    protected function getPanelRoles(): array
    {
        return [
            'super-admin' => ['super-admin'],
            'auditor' => ['auditor'],
            'fakultas' => ['ketua-lpm', 'admin-mutu'],
            'prodi' => ['kaprodi', 'sekprodi', 'kepala-unit', 'dosen', 'tendik'],
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $roles = $this->getPanelRoles()[$panel->getId()] ?? [];

        return $this->hasRole($roles);
    }

    public function auditEvidences()
    {
        return $this->hasMany(AuditEvidence::class);
    }

    public function scores()
    {
        return $this->hasMany(AuditScore::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function studyProgram()
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function getStudyProgramNameAttribute(): ?string
    {
        return $this->studyProgram?->name ?? $this->study_program;
    }

    public function getFacultyNameAttribute(): ?string
    {
        return $this->faculty?->name ?? $this->faculty;
    }

    public function userPositions()
    {
        return $this->hasMany(UserPosition::class, 'user_id');
    }

    public function lecturerQualification()
    {
        return $this->hasOne(LecturerQualification::class, 'user_id');
    }

    public function qualityDocuments()
    {
        return $this->hasMany(QualityDocument::class, 'created_by');
    }

    public function evidences()
    {
        return $this->hasMany(Evidence::class, 'created_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class, 'user_id');
    }

    public function meetingParticipants()
    {
        return $this->hasMany(MeetingParticipant::class, 'user_id');
    }

    public function submittedRealizations()
    {
        return $this->hasMany(Realization::class, 'submitted_by');
    }

    public function approvedRealizations()
    {
        return $this->hasMany(Realization::class, 'approved_by');
    }

    public function submittedSelfAssessments()
    {
        return $this->hasMany(SelfAssessment::class, 'submitted_by');
    }

    public function approvedSelfAssessments()
    {
        return $this->hasMany(SelfAssessment::class, 'approved_by');
    }
}
