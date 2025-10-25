<x-admin::layouts>
    <x-slot:title>
        Comprobantes Yape / Plin
    </x-slot>

    <div class="flex gap-4 justify-between items-center max-sm:flex-wrap">
        <p class="text-xl text-gray-800 dark:text-white font-bold">
            Comprobantes de Pago - Yape / Plin
        </p>
    </div>

    <div class="mt-4">
        @if($receipts->isEmpty())
            <div class="box-shadow rounded bg-white dark:bg-gray-900 p-4">
                <p class="text-gray-400">No hay comprobantes para revisar</p>
            </div>
        @else
            <div class="box-shadow rounded bg-white dark:bg-gray-900">
                <table class="w-full">
                    <thead>
                        <tr class="border-b dark:border-gray-800">
                            <th class="p-4 text-left">ID Pedido</th>
                            <th class="p-4 text-left">Cliente</th>
                            <th class="p-4 text-left">Fecha Subida</th>
                            <th class="p-4 text-left">Estado</th>
                            <th class="p-4 text-left">Comprobante</th>
                            <th class="p-4 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receipts as $receipt)
                            <tr class="border-b dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="p-4">
                                    <a href="{{ route('admin.sales.orders.view', $receipt->order_id) }}"
                                       class="text-blue-600 hover:underline">
                                        #{{ $receipt->order->increment_id }}
                                    </a>
                                </td>
                                <td class="p-4">
                                    {{ $receipt->order->customer_first_name }} {{ $receipt->order->customer_last_name }}
                                </td>
                                <td class="p-4">
                                    {{ $receipt->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="p-4">
                                    @if($receipt->status === 'pending')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">
                                            Pendiente
                                        </span>
                                    @elseif($receipt->status === 'approved')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                                            Aprobado
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">
                                            Rechazado
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <a href="{{ $receipt->receipt_url }}"
                                       target="_blank"
                                       class="text-blue-600 hover:underline">
                                        Ver comprobante
                                    </a>
                                </td>
                                <td class="p-4">
                                    <a href="{{ route('admin.yapeplin.receipts.show', $receipt->id) }}"
                                       class="text-blue-600 hover:underline">
                                        Ver detalles
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $receipts->links() }}
                </div>
            </div>
        @endif
    </div>
</x-admin::layouts>
