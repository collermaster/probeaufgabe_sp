{{--Hier musste ich mit Table arbeiten weil das composer package das ich benutzte um die PDFs zu erstellen nur begrenzt css zulässt, --}}
{{--das heraus zu finden hat auch erst mal eine weile gedauert ...--}}
{{--Sonst ist das hier einfach nur eine simple blade die als Template für die PDF fungiert--}}

    <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Das der Title</title>
</head>
<body>
<h1>Ausgewertete Logs</h1>
<h2>Aufgabe 1</h2>
<table>
    <thead>
    <tr>
        <th>Platz</th>
        <th>Serial number</th>
        <th>Zugriffe</th>
    </tr>
    </thead>
    <tbody>
    @foreach($sortedSerialCount as $serial => $count)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$serial}}</td>
            <td>{{$count}}</td>
            @endforeach
        </tr>
    </tbody>
</table>
<h2>Aufgabe 2</h2>
<table>
    <thead>
    <tr>
        <th>Platz</th>
        <th>Serial number</th>
        <th>Anzahl der verschiedenen MACs</th>
    </tr>
    </thead>
    <tbody>
    @foreach($sortedSerialToMacs as $serial => $macs)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$serial}}</td>
            <td>{{count($macs)}}</td>
            @endforeach
        </tr>
    </tbody>
</table>
</body>
</html>
