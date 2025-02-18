<x-filament::widget>
    <x-filament::card>
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium">
                Transacciones por Mes ({{ $year }})
            </h2>
            <select wire:model="year" class="border rounded px-3 py-2">
                @for ($i = now()->year; $i >= now()->subYears(5)->year; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="mt-4">
            <canvas id="transactionsChart" style="width:100%;max-width:700px"></canvas>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('transactionsChart').getContext('2d');
                const transactionsData = @json($this->getTransactionsData());

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                        datasets: [{
                            label: 'Total Amount',
                            data: Object.values(transactionsData),
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
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
            </script>
        @endpush
    </x-filament::card>
</x-filament::widget>