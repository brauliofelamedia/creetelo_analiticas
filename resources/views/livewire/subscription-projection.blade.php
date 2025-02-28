<div class="p-4 space-y-4">
    <div class="bg-white rounded-lg shadow p-4">
        {{ $this->form }}
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Canceladas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencidas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Incompletas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($subscriptionData as $data)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ strtoupper(\Carbon\Carbon::createFromFormat('Y-m', $data['month'])->translatedFormat('M Y')) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $data['total'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="stat-value active">{{ $data['active'] }}</span>
                        @if($data['active_growth'] != 0)
                            <span class="growth-indicator {{ $data['active_growth'] > 0 ? 'growth-positive' : 'growth-negative' }}">
                                ({{ $data['active_growth'] > 0 ? '↑' : '↓' }} {{ number_format(abs($data['active_growth']), 1) }}%)
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="stat-value canceled">{{ $data['canceled'] }}</span>
                        @if($data['canceled_growth'] != 0)
                            <span class="growth-indicator {{ $data['canceled_growth'] > 0 ? 'growth-negative' : 'growth-positive' }}">
                                ({{ $data['canceled_growth'] > 0 ? '↑' : '↓' }} {{ number_format(abs($data['canceled_growth']), 1) }}%)
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="stat-value past-due">{{ $data['past_due'] }}</span>
                        @if($data['past_due_growth'] != 0)
                            <span class="growth-indicator {{ $data['past_due_growth'] > 0 ? 'growth-negative' : 'growth-positive' }}">
                                ({{ $data['past_due_growth'] > 0 ? '↑' : '↓' }} {{ number_format(abs($data['past_due_growth']), 1) }}%)
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="stat-value incomplete">{{ $data['incomplete_expired'] }}</span>
                        @if($data['incomplete_growth'] != 0)
                            <span class="growth-indicator {{ $data['incomplete_growth'] > 0 ? 'growth-negative' : 'growth-positive' }}">
                                ({{ $data['incomplete_growth'] > 0 ? '↑' : '↓' }} {{ number_format(abs($data['incomplete_growth']), 1) }}%)
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">USD ${{ number_format($data['total_amount'], 2) }}</td>
                </tr>
                @endforeach

                @foreach($projectedData as $projection)
                <tr class="projection-row">
                    <td class="px-6 py-4 whitespace-nowrap projection-text">
                        {{ strtoupper(\Carbon\Carbon::createFromFormat('Y-m', $projection['month'])->translatedFormat('M Y')) }}
                        <span class="text-xs">(Proyección)</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-blue-600">{{ $projection['total'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="stat-value active">{{ $projection['active'] }}</span>
                        @if($projection['active_growth'] != 0)
                            <span class="growth-indicator {{ $projection['active_growth'] > 0 ? 'growth-positive' : 'growth-negative' }}">
                                ({{ $projection['active_growth'] > 0 ? '↑' : '↓' }} {{ number_format(abs($projection['active_growth']), 1) }}%)
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="stat-value canceled">{{ $projection['canceled'] }}</span>
                        @if($projection['canceled_growth'] != 0)
                            <span class="growth-indicator {{ $projection['canceled_growth'] > 0 ? 'growth-negative' : 'growth-positive' }}">
                                ({{ $projection['canceled_growth'] > 0 ? '↑' : '↓' }} {{ number_format(abs($projection['canceled_growth']), 1) }}%)
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="stat-value past-due">{{ $projection['past_due'] }}</span>
                        @if($projection['past_due_growth'] != 0)
                            <span class="growth-indicator {{ $projection['past_due_growth'] > 0 ? 'growth-negative' : 'growth-positive' }}">
                                ({{ $projection['past_due_growth'] > 0 ? '↑' : '↓' }} {{ number_format(abs($projection['past_due_growth']), 1) }}%)
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="stat-value incomplete">{{ $projection['incomplete_expired'] }}</span>
                        @if($projection['incomplete_growth'] != 0)
                            <span class="growth-indicator {{ $projection['incomplete_growth'] > 0 ? 'growth-negative' : 'growth-positive' }}">
                                ({{ $projection['incomplete_growth'] > 0 ? '↑' : '↓' }} {{ number_format(abs($projection['incomplete_growth']), 1) }}%)
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-blue-600">USD ${{ number_format($projection['total_amount'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-6 py-3 font-medium">Totales Históricos</td>
                    <td class="px-6 py-3 font-medium">{{ collect($subscriptionData)->sum('total') }}</td>
                    <td class="px-6 py-3 font-medium">{{ collect($subscriptionData)->sum('active') }}</td>
                    <td class="px-6 py-3 font-medium">{{ collect($subscriptionData)->sum('canceled') }}</td>
                    <td class="px-6 py-3 font-medium">{{ collect($subscriptionData)->sum('past_due') }}</td>
                    <td class="px-6 py-3 font-medium">{{ collect($subscriptionData)->sum('incomplete_expired') }}</td>
                    <td class="px-6 py-3 font-medium">USD ${{ number_format(collect($subscriptionData)->sum('total_amount'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('styles')
<style>
    .stat-value {
        font-weight: 500;
    }

    .stat-value.active {
        color: #059669;
    }

    .stat-value.canceled {
        color: #DC2626;
    }

    .stat-value.past-due {
        color: #D97706;
    }

    .stat-value.incomplete {
        color: #4B5563;
    }

    .growth-indicator {
        font-size: 0.75rem;
        font-weight: 500;
        margin-left: 0.25rem;
    }

    .growth-positive {
        color: #10B981;
    }

    .growth-negative {
        color: #EF4444;
    }

    .projection-row {
        background-color: #EFF6FF;
    }

    .projection-text {
        color: #2563EB;
        font-weight: 500;
    }

</style>
@endpush