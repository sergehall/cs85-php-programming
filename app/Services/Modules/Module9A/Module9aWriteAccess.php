<?php

declare(strict_types=1);

namespace App\Services\Modules\Module9A;

use App\Models\User;

final class Module9aWriteAccess
{
    public function allows(?User $user): bool
    {
        return app()->environment(['local', 'testing']) || $user?->isAdmin() === true;
    }
}
