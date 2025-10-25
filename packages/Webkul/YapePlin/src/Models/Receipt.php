<?php

namespace Webkul\YapePlin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Sales\Models\OrderProxy;
use Webkul\User\Models\AdminProxy;
use Webkul\YapePlin\Contracts\Receipt as ReceiptContract;

class Receipt extends Model implements ReceiptContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'yapeplin_receipts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'receipt_path',
        'original_filename',
        'status',
        'admin_notes',
        'verified_at',
        'verified_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Get the order that owns the receipt.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(OrderProxy::modelClass());
    }

    /**
     * Get the admin who verified the receipt.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(AdminProxy::modelClass(), 'verified_by');
    }

    /**
     * Get the full URL for the receipt file.
     */
    public function getReceiptUrlAttribute(): string
    {
        return \Storage::url($this->receipt_path);
    }
}
