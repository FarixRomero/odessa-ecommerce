<x-admin::layouts>
    <x-slot:title>
        Comprobante - Pedido #{{ $receipt->order->increment_id }}
    </x-slot>

    <div class="flex gap-4 justify-between items-center mb-6">
        <div>
            <p class="text-xl text-gray-800 dark:text-white font-bold">
                Comprobante de Pago - Pedido #{{ $receipt->order->increment_id }}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Cliente: {{ $receipt->order->customer_first_name }} {{ $receipt->order->customer_last_name }} |
                Total: {{ core()->formatPrice($receipt->order->grand_total, $receipt->order->order_currency_code) }}
            </p>
        </div>

        <a href="{{ route('admin.sales.orders.view', $receipt->order_id) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            ← Volver al Pedido
        </a>
    </div>

    <!-- Receipt Image -->
    <div class="box-shadow rounded bg-white dark:bg-gray-900 p-6">
        <div class="flex justify-center mb-6">
            @if(str_ends_with($receipt->receipt_path, '.pdf'))
                <div class="w-full">
                    <embed src="{{ $receipt->receipt_url }}"
                           type="application/pdf"
                           width="100%"
                           height="800px"
                           class="rounded border border-gray-300 dark:border-gray-700" />
                    <p class="mt-4 text-center">
                        <a href="{{ $receipt->receipt_url }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                            📄 Abrir PDF en nueva pestaña
                        </a>
                    </p>
                </div>
            @else
                <img src="{{ $receipt->receipt_url }}"
                     alt="Comprobante de pago"
                     class="max-w-full h-auto rounded shadow-lg border border-gray-300 dark:border-gray-700" />
            @endif
        </div>

        <!-- Actions -->
        @if($receipt->status === 'pending')
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 max-w-4xl mx-auto">
                    <!-- Approve Form -->
                    <form method="POST"
                          action="{{ route('admin.yapeplin.receipts.approve', $receipt->id) }}"
                          class="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg border border-green-200 dark:border-green-800">
                        @csrf

                        <h3 class="font-bold text-green-700 dark:text-green-400 mb-4 text-lg">✅ Aprobar Pago</h3>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">
                                Notas (opcional)
                            </label>
                            <textarea name="notes"
                                      rows="2"
                                      class="w-full border border-gray-300 dark:border-gray-600 rounded p-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                      placeholder="Comprobante válido..."></textarea>
                        </div>

                        <button type="submit"
                                class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition-colors">
                            Aprobar y Procesar Pedido
                        </button>
                    </form>

                    <!-- Reject Form -->
                    <form method="POST"
                          action="{{ route('admin.yapeplin.receipts.reject', $receipt->id) }}"
                          class="bg-red-50 dark:bg-red-900/20 p-6 rounded-lg border border-red-200 dark:border-red-800">
                        @csrf

                        <h3 class="font-bold text-red-700 dark:text-red-400 mb-4 text-lg">❌ Rechazar Pago</h3>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">
                                Razón del rechazo <span class="text-red-500">*</span>
                            </label>
                            <textarea name="notes"
                                      rows="2"
                                      required
                                      class="w-full border border-gray-300 dark:border-gray-600 rounded p-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                      placeholder="Comprobante no válido porque..."></textarea>
                        </div>

                        <button type="submit"
                                class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition-colors"
                                onclick="return confirm('¿Estás seguro? Esto cancelará el pedido.')">
                            Rechazar y Cancelar Pedido
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg text-center">
                    <p class="text-gray-700 dark:text-gray-300">
                        Estado:
                        @if($receipt->status === 'approved')
                            <span class="px-4 py-2 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full font-semibold">
                                ✅ Aprobado
                            </span>
                        @else
                            <span class="px-4 py-2 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full font-semibold">
                                ❌ Rechazado
                            </span>
                        @endif
                    </p>
                    @if($receipt->admin_notes)
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                            <strong>Notas:</strong> {{ $receipt->admin_notes }}
                        </p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-admin::layouts>
