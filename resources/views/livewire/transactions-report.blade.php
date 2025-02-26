<div>    
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3">

                <div class="form">
                    <div class="form-group">
                        <label for="start_date" class="mb-2">Fecha de inicio:</label>
                        <input type="date" id="start_date" class="form-control" wire:model="startDate">
                    </div>
                    
                    <div class="form-group">
                        <label for="end_date" class="mb-2">Fecha de fin:</label>
                        <input type="date" id="end_date" wire:model="endDate" class="form-control"/>
                    </div>
            
                    <button wire:click="loadTransactions" class="btn-filter">Filtrar</button>
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

        input {
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
                        label: 'Ingresos x día',
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