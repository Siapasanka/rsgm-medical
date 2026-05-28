<?php

namespace App\Support;

use App\Models\AuditLog;

class Audit
{
    public static function log(string $action, string $entityType, int|string|null $entityId, string $description, array $metadata = []): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
