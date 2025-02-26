<x-filament::page>
    <livewire:transactions-report />
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="mb-6">
        <h2 class="text-xl font-bold mb-4">Filtro de fechas</h2>

        <form wire:submit.prevent="updateFilter">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha inicial
                    </label>
                    <input 
                        id="startDate"
                        type="date"
                        wire:model="startDate"
                        value="{{$startDate->format('Y-m-d')}}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                </div>
                <div>
                    <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha final
                    </label>
                    <input 
                        id="endDate"
                        type="date"
                        wire:model="endDate"
                        value="{{$endDate->format('Y-m-d')}}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                </div>
                
                <div class="flex items-end">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Aplicar filtro
                    </button>
                </div>
            </div>
        </form>

        <!-- Valores actuales simplificados -->
        <div class="mt-4 text-sm">
            <p>Fecha inicial actual: <strong>{{ $startDate->format('d-m-Y') }}</strong></p>
            <p>Fecha final actual: <strong>{{ $endDate->format('d-m-Y') }}</strong></p>
        </div>
    </div>

    <!-- Resultados de la consulta -->
    <div class="mb-8">
        <h2 class="text-xl font-bold mb-4">Resumen de Transacciones</h2>

        <div class="grid grid-cols-8 gap-4 mx-auto">
            <div class="col-start-3 col-span-4 bg-blue-500 p-4 text-white text-center">
                <canvas id="myChart"></canvas>
            </div>
        </div>
        
        <!-- Mostrar datos en una tarjeta -->
        <div class="rounded-lg shadow bg-white overflow-hidden">
            <!-- Tabla de datos -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Monto Total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($this->transactionData['transactions'] as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                    {{ number_format($transaction->total_amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay transacciones en el rango de fechas seleccionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @livewireScripts
    <script>
        // Variable global para almacenar la instancia del gráfico
        let myChartInstance = null;

        // Función para inicializar o actualizar el gráfico
        function updateChart(labels, amounts) {
            const ctx = document.getElementById('myChart');

            // Destruir el gráfico existente si ya existe
            if (myChartInstance) {
                myChartInstance.destroy();
            }

            // Crear un nuevo gráfico
            myChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '# de USD',
                        data: amounts,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Inicializar el gráfico con los datos iniciales
        document.addEventListener('DOMContentLoaded', function () {
            const labels = @json($this->transactionData['labels']);
            const amounts = @json($this->transactionData['amounts']);
            updateChart(labels, amounts);
        });

        // Escuchar eventos de Livewire para actualizar el gráfico
        Livewire.on('updateChart', (labels, amounts) => {
            updateChart(labels, amounts);
        });
    </script>
</x-filament::page>