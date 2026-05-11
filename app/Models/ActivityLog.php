<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    
    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'target_type',
        'target_id',
        'target_name',
        'parent_type',
        'parent_id',
        'parent_name',
        'description',
        'extra_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'extra_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(array $data): self
    {
        $user = auth()->user();
        
        return self::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? $user?->email,
            'user_role' => $user?->roles->first()?->name,
            'action' => $data['action'],
            'module' => $data['module'],
            'target_type' => $data['target_type'] ?? null,
            'target_id' => $data['target_id'] ?? null,
            'target_name' => $data['target_name'] ?? null,
            'parent_type' => $data['parent_type'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'parent_name' => $data['parent_name'] ?? null,
            'description' => $data['description'] ?? null,
            'extra_data' => $data['extra_data'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
