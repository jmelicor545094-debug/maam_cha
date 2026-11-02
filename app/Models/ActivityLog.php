<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'module', 'action', 'description', 'ip_address', 'created_at'];
    protected function casts(): array { return ['created_at' => 'datetime']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class)->withTrashed(); }

    public static function record(string $module, string $action, string $description): void
    {
        self::create(['user_id' => auth()->id(), 'module' => $module, 'action' => $action, 'description' => $description, 'ip_address' => request()?->ip(), 'created_at' => now()]);
    }
}
