<?php

namespace Webkul\YapePlin\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\YapePlin\Repositories\ReceiptRepository;
use Webkul\Sales\Repositories\OrderRepository;

class ReceiptController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ReceiptRepository $receiptRepository,
        protected OrderRepository $orderRepository
    ) {}

    /**
     * Display listing of receipts
     */
    public function index()
    {
        $receipts = $this->receiptRepository->with(['order', 'order.customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('yapeplin::admin.receipts.index', compact('receipts'));
    }

    /**
     * Show receipt details
     */
    public function show($id)
    {
        $receipt = $this->receiptRepository->with(['order', 'order.customer', 'verifiedBy'])
            ->findOrFail($id);

        return view('yapeplin::admin.receipts.show', compact('receipt'));
    }

    /**
     * Approve receipt
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $receipt = $this->receiptRepository->approve(
                $id,
                auth()->guard('admin')->user()->id,
                $request->notes
            );

            // Update order status to processing
            $this->orderRepository->update([
                'status' => 'processing',
            ], $receipt->order_id);

            return redirect()->back()
                ->with('success', 'Comprobante aprobado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al aprobar: ' . $e->getMessage());
        }
    }

    /**
     * Reject receipt
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:500',
        ], [
            'notes.required' => 'Debes proporcionar una razón para el rechazo',
        ]);

        try {
            $receipt = $this->receiptRepository->reject(
                $id,
                auth()->guard('admin')->user()->id,
                $request->notes
            );

            // Update order status to cancelled
            $this->orderRepository->update([
                'status' => 'cancelled',
            ], $receipt->order_id);

            return redirect()->back()
                ->with('success', 'Comprobante rechazado');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al rechazar: ' . $e->getMessage());
        }
    }
}
