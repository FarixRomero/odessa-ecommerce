<x-shop::layouts>
    <x-slot:title>
        Subir Comprobante de Pago - Pedido #{{ $order->increment_id }}
    </x-slot>

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-green-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="ml-3 text-sm text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="ml-3 text-sm text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-yellow-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="ml-3 text-sm text-yellow-800 font-medium">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Subir Comprobante de Pago
            </h1>
            <p class="mt-2 text-gray-600">
                Pedido #{{ $order->increment_id }} - Total: {{ core()->formatPrice($order->grand_total, $order->order_currency_code) }}
            </p>
        </div>

        @if($hasReceipt)
            <!-- Already uploaded -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-blue-900">
                            Comprobante ya subido
                        </h3>
                        <p class="mt-2 text-sm text-blue-800">
                            Ya has subido un comprobante para este pedido. Estado:
                            @if($hasReceipt->status === 'pending')
                                <span class="font-semibold">Pendiente de revisión</span>
                            @elseif($hasReceipt->status === 'approved')
                                <span class="font-semibold text-green-700">Aprobado</span>
                            @else
                                <span class="font-semibold text-red-700">Rechazado</span>
                            @endif
                        </p>
                        @if($hasReceipt->admin_notes)
                            <p class="mt-2 text-sm text-blue-800">
                                <strong>Notas del administrador:</strong> {{ $hasReceipt->admin_notes }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <!-- Upload form -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Instructions -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Instrucciones de Pago</h2>
                    <div class="mt-3 text-sm text-gray-700 space-y-2">
                        <p>{{ core()->getConfigData('sales.payment_methods.yapeplin.instructions') ?: 'Por favor realiza el pago y sube tu comprobante.' }}</p>

                        <div class="mt-4 grid grid-cols-2 gap-4 bg-white p-4 rounded-md border border-gray-200">
                            <div>
                                <p class="text-xs text-gray-500">Número de Yape/Plin:</p>
                                <p class="font-semibold text-gray-900">{{ core()->getConfigData('sales.payment_methods.yapeplin.account_number') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Titular:</p>
                                <p class="font-semibold text-gray-900">{{ core()->getConfigData('sales.payment_methods.yapeplin.account_holder') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('yapeplin.upload.process', $order->id) }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="px-6 py-6">
                    @csrf

                    <div class="space-y-6">
                        <!-- File upload -->
                        <div>
                            <label for="yapeplin_receipt" class="block text-sm font-medium text-gray-700 mb-2">
                                Comprobante de Pago <span class="text-red-500">*</span>
                            </label>

                            <div id="drop-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors cursor-pointer">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="yapeplin_receipt" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Haz clic aquí para seleccionar archivo</span>
                                            <input id="yapeplin_receipt"
                                                   name="yapeplin_receipt"
                                                   type="file"
                                                   class="sr-only"
                                                   accept="image/jpeg,image/jpg,image/png,application/pdf"
                                                   required
                                                   onchange="updateFileName(this)">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        o arrastra y suelta tu archivo aquí
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, PDF hasta 5MB
                                    </p>
                                    <p id="file-name" class="text-sm font-medium text-green-600 mt-2"></p>
                                </div>
                            </div>

                            @error('yapeplin_receipt')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Important notes -->
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>Importante:</strong> Asegúrate de que el comprobante sea legible y muestre claramente:
                                    </p>
                                    <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside space-y-1">
                                        <li>Monto transferido</li>
                                        <li>Fecha y hora de la transacción</li>
                                        <li>Número de operación o referencia</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Submit button -->
                        <div class="flex justify-between items-center pt-4 mt-6">
                            <a href="{{ route('shop.customers.account.orders.view', $order->id) }}"
                               class="text-sm text-gray-600 hover:text-gray-900">
                                ← Ver pedido
                            </a>
                            <button type="submit"
                                    class="flex w-max items-center rounded-xl bg-blue-600 text-white px-7 py-2.5 font-medium hover:bg-blue-700 max-md:px-5 max-md:text-xs max-sm:rounded-lg max-sm:px-4 max-sm:py-2"
                                    style="background-color: #2563EB; color: white; border: none;"
                                    onmouseover="this.style.backgroundColor='#1D4ED8'"
                                    onmouseout="this.style.backgroundColor='#2563EB'">
                                Subir Comprobante
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <!-- Back link -->
        <div class="mt-6 text-center">
            <a href="{{ route('shop.customers.account.orders.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                ← Volver a mis pedidos
            </a>
        </div>
    </div>

    @push('scripts')
        <script>
            function updateFileName(input) {
                const fileName = input.files[0]?.name;
                const fileNameElement = document.getElementById('file-name');
                if (fileName) {
                    fileNameElement.textContent = `✓ Archivo seleccionado: ${fileName}`;
                } else {
                    fileNameElement.textContent = '';
                }
            }

            // Drag and drop functionality
            const dropArea = document.getElementById('drop-area');
            const fileInput = document.getElementById('yapeplin_receipt');

            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Highlight drop area when item is dragged over it
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                dropArea.classList.add('border-blue-500', 'bg-blue-50');
            }

            function unhighlight(e) {
                dropArea.classList.remove('border-blue-500', 'bg-blue-50');
            }

            // Handle dropped files
            dropArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files.length > 0) {
                    fileInput.files = files;
                    updateFileName(fileInput);
                }
            }

            // Click on drop area to open file selector
            dropArea.addEventListener('click', function(e) {
                if (e.target === dropArea || e.target.closest('#drop-area')) {
                    fileInput.click();
                }
            });
        </script>
    @endpush
</x-shop::layouts>
