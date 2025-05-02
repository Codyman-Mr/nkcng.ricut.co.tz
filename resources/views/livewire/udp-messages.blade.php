<div>
    <h2 class="text-xl font-bold">Received UDP Messages</h2>
    <table class="table-auto w-full border">
        <thead>
            <tr>
                <th>IP</th>
                <th>Port</th>
                <th>Lat,Long</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @foreach($messages as $message)
            <tr>
                <td>{{ $message->ip }}</td>
                <td>{{ $message->port }}</td>
                <td><div class="overflow-x-scroll">
                    {{ $message->latitude }},
                    {{$message->longitude}}
                </div>
                </td>
                <td>{{ $message->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
