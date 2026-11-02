<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionRequest extends Model
{
    protected $fillable = ['user_id', 'request_type', 'requested_data', 'status', 'admin_notes', 'reviewed_by', 'reviewed_at'];
    protected function casts(): array { return ['requested_data' => 'array', 'reviewed_at' => 'datetime']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
