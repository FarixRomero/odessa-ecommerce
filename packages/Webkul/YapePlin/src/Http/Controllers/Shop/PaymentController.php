<?php

namespace Webkul\YapePlin\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;
use Webkul\YapePlin\Repositories\ReceiptRepository;

class PaymentController
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ReceiptRepository $receiptRepository,
        protected OrderRepository $orderRepository
    ) {}

    /**
     * Process the payment and create order.
     * This is called after checkout when customer selects Yape/Plin.
     */
    public function process()
    {
        $cart = Cart::getCart();

        // Create the order
        $data = (new OrderResource($cart))->jsonSerialize();
        $order = $this->orderRepository->create($data);

        // Deactivate cart
        Cart::deActivateCart();

        // Save order ID in session
        session()->flash('order_id', $order->id);

        // Redirect to upload page
        return redirect()->route('yapeplin.upload', ['order_id' => $order->id]);
    }

    /**
     * Show upload form
     */
    public function showUploadForm($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        // Verify that the payment method is yapeplin
        if ($order->payment->method !== 'yapeplin') {
            return redirect()->route('shop.home.index')
                ->with('error', 'Método de pago inválido');
        }

        // Check if already has a receipt
        $hasReceipt = $this->receiptRepository->findWhere(['order_id' => $order->id])->first();

        return view('yapeplin::shop.upload', compact('order', 'hasReceipt'));
    }

    /**
     * Process the receipt upload
     */
    public function processUpload(Request $request, $orderId)
    {
        $request->validate([
            'yapeplin_receipt' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120', // 5MB max
        ], [
            'yapeplin_receipt.required' => 'Debes subir el comprobante de pago',
            'yapeplin_receipt.mimes'    => 'El comprobante debe ser JPG, PNG o PDF',
            'yapeplin_receipt.max'      => 'El comprobante no puede ser mayor a 5MB',
        ]);

        try {
            $order = $this->orderRepository->findOrFail($orderId);

            // Verify that the payment method is yapeplin
            if ($order->payment->method !== 'yapeplin') {
                return redirect()->route('shop.home.index')
                    ->with('error', 'Método de pago inválido');
            }

            // Check if already has a receipt
            $existingReceipt = $this->receiptRepository->findWhere(['order_id' => $order->id])->first();

            if ($existingReceipt) {
                return redirect()->back()
                    ->with('warning', 'Ya has subido un comprobante para este pedido');
            }

            // Store the receipt file
            $file = $request->file('yapeplin_receipt');
            $filename = 'yapeplin_' . $orderId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('receipts/yapeplin', $filename, 'public');

            // Create receipt record
            $this->receiptRepository->create([
                'order_id'          => $order->id,
                'receipt_path'      => $path,
                'original_filename' => $file->getClientOriginalName(),
                'status'            => 'pending',
            ]);

            return redirect()->route('shop.customers.account.orders.view', $order->id)
                ->with('success', 'Comprobante subido exitosamente. Tu pedido será verificado pronto.');
        } catch (\Exception $e) {
            report($e);

            return redirect()->back()
                ->with('error', 'Error al procesar el comprobante: ' . $e->getMessage())
                ->withInput();
        }
    }
}
