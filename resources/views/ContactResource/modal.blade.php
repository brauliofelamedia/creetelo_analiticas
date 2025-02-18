<div>
    <style>
        p {
            margin-bottom: 10px;
        }

        .line {
            display: inline;
        }

        .tag {
            background-color:#d8bcdb;
            display: inline;
            padding:2px 5px;
            border-radius: 2px;
        }
    </style>
    <p><strong>ID:</strong> {{ $record->lead_id }}</p>
    <p><strong>Nombre:</strong> {{ $record->fullName }}</p>
    <p><strong>Teléfono:</strong> {{ ($record->phone)? $record->phone :'-' }}</p>
    <p><strong>Correo electrónico:</strong> {{ ($record->email)? $record->email : '-' }}</p>
    <p><strong>Ciudad:</strong> {{ ($record->city)? $record->city : '-'  }}</p>
    <p><strong>Fecha de nacimiento:</strong> {{ ($record->dateOfBirth)? $record->dateOfBirth : '-' }}</p>
    <p><strong>País:</strong> {{ ($record->country)? $record->country : '-' }}</p>
    <p><strong>Dirección:</strong> {{ ($record->address)? $record->address : '-' }}</p>
    <p><strong>Tipo:</strong> {{ ($record->type)? $record->type : '-' }}</p>
    <p class="line"><strong>Etiquetas:</strong>
        <ul class="line">
            @foreach($record->tags as $tag)
                <li class="tag">{{ $tag }}</li>
            @endforeach
        </ul>
    </p>
    <p><strong>Fecha de registro:</strong> {{ Carbon\Carbon::parse($record->dateAdded)->format('d-m-Y H:s') }}</p>
    <p><strong>Última actualización:</strong> {{ Carbon\Carbon::parse($record->dateUpdated)->diffForHumans() }}</p>
</div>