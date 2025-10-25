<div class="mb-4">
    <p class="mb-2 text-sm text-gray-600">
        {{ $additional['description'] ?? 'Realiza tu pago mediante Yape o Plin y sube el comprobante' }}
    </p>

    @if(!empty($additional['instructions']))
        <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h4 class="font-semibold text-blue-900 mb-2">Instrucciones de pago:</h4>
            <p class="text-sm text-blue-800 whitespace-pre-line">{{ $additional['instructions'] }}</p>
        </div>
    @endif

    @if(!empty($additional['account_number']))
        <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Número de cuenta:</p>
                    <p class="font-semibold text-gray-900">{{ $additional['account_number'] }}</p>
                </div>
                @if(!empty($additional['account_holder']))
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Titular:</p>
                        <p class="font-semibold text-gray-900">{{ $additional['account_holder'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="mb-4">
        <label for="yapeplin_receipt" class="block text-sm font-medium text-gray-700 mb-2">
            Comprobante de pago <span class="text-red-500">*</span>
        </label>
        <input
            type="file"
            id="yapeplin_receipt"
            name="yapeplin_receipt"
            accept="image/jpeg,image/jpg,image/png,application/pdf"
            required
            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:border-blue-500"
        />
        <p class="mt-1 text-xs text-gray-500">
            Formatos permitidos: JPG, PNG, PDF (Máx. 5MB)
        </p>
    </div>

    <div class="text-xs text-gray-500">
        <p>* Tu pedido será verificado una vez que subas el comprobante de pago.</p>
        <p>* Recibirás una confirmación por correo cuando se apruebe el pago.</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('yapeplin_receipt');

        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];

                if (file) {
                    // Validate file size (5MB max)
                    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
                    if (file.size > maxSize) {
                        alert('El archivo es demasiado grande. El tamaño máximo es 5MB.');
                        e.target.value = '';
                        return;
                    }

                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Tipo de archivo no permitido. Solo se aceptan JPG, PNG o PDF.');
                        e.target.value = '';
                        return;
                    }
                }
            });
        }
    });
</script>
