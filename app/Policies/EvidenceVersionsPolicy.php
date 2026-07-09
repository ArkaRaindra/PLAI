<?php

namespace App\Policies;

use App\Models\EvidenceVersions;
use App\Models\User;

class EvidenceVersionsPolicy
{
    public function view(User $user, EvidenceVersions $evidenceVersion): bool
    {
        return $user->can('ppepp.view');
    }
}
