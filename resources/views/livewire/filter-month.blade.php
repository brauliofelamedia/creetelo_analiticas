<div>    
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3">

                <div class="form">
                    <h3>Primer fecha:</h3>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <select wire:model="month1">
                                    @php
                                        $meses = [
                                            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                        ];
                                    @endphp
                                    @foreach ($meses as $key => $mes)
                                        <option value="{{ $key }}">{{ $mes }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <select wire:model="year1">
                                    @for ($i = now()->year; $i >= now()->year - 5; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <h4>Segunda fecha:</h4>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <select wire:model="month2">
                                    @foreach ($meses as $key => $mes)
                                        <option value="{{ $key }}">{{ $mes }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <select wire:model="year2">
                                    @for ($i = now()->year; $i >= now()->year - 5; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <button wire:click="loadTransactions" class="btn-filter">Comparar</button>
                </div>

            </div>
            <div class="col-lg-9">
                <canvas id="canvas" class="w-full h-full"></canvas>
            </div>
            
        </div>
    </div>
</div>

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap-grid.min.css" rel="stylesheet">
    <style>
        .form {
            background-color: #ececec;
            padding:25px;
            border-radius: 6px;
        }

        label {
            display: block;
            font-weight: bold;
        }

        select {
            width: 100%;
            border-radius: 3px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-filter {
            background-color: #c026d3;
            color: white;
            width: 100%;
            text-align: center;
            border-radius: 5px;
            padding:14px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Variable global para almacenar la instancia del gráfico
        let incomeChart = null;

        // Función para crear o actualizar el gráfico
        function createOrUpdateChart(labels, data) {
            const ctx = document.getElementById('canvas').getContext('2d');

            // Destruir la gráfica anterior si existe
            if (incomeChart) {
                incomeChart.destroy();
            }

            // Crear una nueva gráfica
            incomeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ingresos x mes',
                        data: data,
                        backgroundColor: 'rgba(192, 38, 211, 0.2)',
                        borderColor: 'rgba(192, 38, 211, 1)',
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

        // Escuchar el evento de Livewire
        document.addEventListener('livewire:init', () => {
            Livewire.on('transactionsUpdated', (data) => {
                createOrUpdateChart(data.labels, data.data);
            });
        });
    </script>
@endpush