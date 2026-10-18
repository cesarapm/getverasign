<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentActivity extends Model
{
    protected $fillable = [
        'document_id',
        'event_type',
        'title',
        'description',
        'actor_name',
        'actor_email',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(SignatureDocument::class);
    }
}
