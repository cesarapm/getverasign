<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SignatureDocument extends Model
{
    protected $fillable = [
        'owner_id',
        'document_name',
        'description',
        'pdf_path',
        'signers',
        'signature_fields',
        'signing_order',
        'expiration_days',
        'notify_on_signing',
        'notify_on_rejection',
        'notify_on_expiration',
        'status',
    ];

    protected $casts = [
        'signers' => 'array',
        'signature_fields' => 'array',
        'expiration_days' => 'integer',
        'notify_on_signing' => 'boolean',
        'notify_on_rejection' => 'boolean',
        'notify_on_expiration' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(DocumentActivity::class, 'document_id')->orderByDesc('created_at');
    }
}
