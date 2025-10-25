<x-admin::layouts>
    <x-slot:title>
        Comprobante #{{ $receipt->id }}
    </x-slot>

    <div class="flex gap-4 justify-between items-center max-sm:flex-wrap">
        <p class="text-xl text-gray-800 dark:text-white font-bold">
            Comprobante de Pago #{{ $receipt->id }}
        </p>

        <a href="{{ route('admin.yapeplin.receipts.index') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Volver al listado
        </a>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
        <!-- Order Information -->
        <div class="box-shadow rounded bg-white dark:bg-gray-900 p-6">
            <h3 class="text-lg font-semibold mb-4">Información del Pedido</h3>

            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">ID Pedido</p>
                    <p class="font-semibold">
                        <a href="{{ route('admin.sales.orders.view', $receipt->order_id) }}"
                           class="text-blue-600 hover:underline">
                            #{{ $receipt->order->increment_id }}
                        </a>
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Cliente</p>
                    <p class="font-semibold">
                        {{ $receipt->order->customer_first_name }} {{ $receipt->order->customer_last_name }}
                    </p>
                    <p class="text-sm text-gray-600">{{ $receipt->order->customer_email }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Total del Pedido</p>
                    <p class="font-semibold">{{ core()->formatPrice($receipt->order->grand_total, $receipt->order->order_currency_code) }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Fecha del Pedido</p>
                    <p class="font-semibold">{{ $receipt->order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Receipt Information -->
        <div class="box-shadow rounded bg-white dark:bg-gray-900 p-6">
            <h3 class="text-lg font-semibold mb-4">Información del Comprobante</h3>

            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Estado</p>
                    <p class="font-semibold">
                        @if($receipt->status === 'pending')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded">
                                Pendiente de Revisión
                            </span>
                        @elseif($receipt->status === 'approved')
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded">
                                Aprobado
                            </span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded">
                                Rechazado
                            </span>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Archivo Original</p>
                    <p class="font-semibold">{{ $receipt->original_filename }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Fecha de Subida</p>
                    <p class="font-semibold">{{ $receipt->created_at->format('d/m/Y H:i') }}</p>
                </div>

                @if($receipt->verified_at)
                    <div>
                        <p class="text-sm text-gray-500">Verificado por</p>
                        <p class="font-semibold">{{ $receipt->verifiedBy->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600">{{ $receipt->verified_at->format('d/m/Y H:i') }}</p>
                    </div>
                @endif

                @if($receipt->admin_notes)
                    <div>
                        <p class="text-sm text-gray-500">Notas del Administrador</p>
                        <p class="text-sm">{{ $receipt->admin_notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Receipt Image -->
    <div class="mt-4 box-shadow rounded bg-white dark:bg-gray-900 p-6">
        <h3 class="text-lg font-semibold mb-4">Comprobante</h3>

        <div class="flex justify-center">
            @if(str_ends_with($receipt->receipt_path, '.pdf'))
                <div class="w-full">
                    <embed src="{{ $receipt->receipt_url }}"
                           type="application/pdf"
                           width="100%"
                           height="600px" />
                    <p class="mt-2 text-center">
                        <a href="{{ $receipt->receipt_url }}"
                           target="_blank"
                           class="text-blue-600 hover:underline">
                            Abrir PDF en nueva pestaña
                        </a>
                    </p>
                </div>
            @else
                <img src="{{ $receipt->receipt_url }}"
                     alt="Comprobante"
                     class="max-w-full h-auto max-h-[600px] rounded shadow-lg" />
            @endif
        </div>
    </div>

    <!-- Actions -->
    @if($receipt->status === 'pending')
        <div class="mt-4 box-shadow rounded bg-white dark:bg-gray-900 p-6">
            <h3 class="text-lg font-semibold mb-4">Acciones</h3>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <!-- Approve Form -->
                <form method="POST"
                      action="{{ route('admin.yapeplin.receipts.approve', $receipt->id) }}"
                      class="border-r pr-4">
                    @csrf

                    <h4 class="font-semibold text-green-700 mb-3">Aprobar Comprobante</h4>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">
                            Notas (opcional)
                        </label>
                        <textarea name="notes"
                                  rows="3"
                                  class="w-full border rounded p-2"
                                  placeholder="Agregar notas sobre la aprobación..."></textarea>
                    </div>

                    <button type="submit"
                            class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Aprobar Pago
                    </button>
                </form>

                <!-- Reject Form -->
                <form method="POST"
                      action="{{ route('admin.yapeplin.receipts.reject', $receipt->id) }}"
                      class="pl-4">
                    @csrf

                    <h4 class="font-semibold text-red-700 mb-3">Rechazar Comprobante</h4>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">
                            Razón del rechazo <span class="text-red-500">*</span>
                        </label>
                        <textarea name="notes"
                                  rows="3"
                                  required
                                  class="w-full border rounded p-2"
                                  placeholder="Explica por qué se rechaza el comprobante..."></textarea>
                    </div>

                    <button type="submit"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                            onclick="return confirm('¿Estás seguro de rechazar este comprobante?')">
                        Rechazar Pago
                    </button>
                </form>
            </div>
        </div>
    @endif
</x-admin::layouts>
