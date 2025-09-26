<x-app-layout>
    @if(session('ok'))
    <div>{{ session('ok') }}</div>
    @endif

    <h1>Mis solicitudes</h1>

    <p><a href="{{ route('solicitudes.create') }}">➕ Nueva solicitud</a></p>

    <table>
    <thead>
        <tr>
        <th>Tipo</th>
        <th>Fecha programada</th>
        <th>Frecuencia</th>
        <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($solicitudes as $s)
        <tr>
            <td>{{ ucfirst($s->tipo_residuo) }}</td>
            <td>{{ $s->fecha_programada }}</td>
            <td>{{ $s->frecuencia }}</td>
            <td>{{ $s->estado }}</td>
        </tr>
        @empty
        <tr><td colspan="4">Sin solicitudes aún.</td></tr>
        @endforelse
    </tbody>
    </table>

    {{ $solicitudes->links() }}
</x-app-layout>