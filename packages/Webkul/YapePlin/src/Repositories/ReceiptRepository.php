<?php

namespace Webkul\YapePlin\Repositories;

use Webkul\Core\Eloquent\Repository;

class ReceiptRepository extends Repository
{
    /**
     * Specify the Model class name
     */
    public function model(): string
    {
        return 'Webkul\YapePlin\Contracts\Receipt';
    }

    /**
     * Get pending receipts for admin review
     */
    public function getPendingReceipts()
    {
        return $this->with(['order', 'order.customer'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    /**
     * Approve receipt
     */
    public function approve($id, $adminId, $notes = null)
    {
        return $this->update([
            'status'      => 'approved',
            'verified_at' => now(),
            'verified_by' => $adminId,
            'admin_notes' => $notes,
        ], $id);
    }

    /**
     * Reject receipt
     */
    public function reject($id, $adminId, $notes)
    {
        return $this->update([
            'status'      => 'rejected',
            'verified_at' => now(),
            'verified_by' => $adminId,
            'admin_notes' => $notes,
        ], $id);
    }
}
