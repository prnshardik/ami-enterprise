@php $i = 1; @endphp
@foreach($data as $row)
    <tr>
        <td>{{ $i }}</td>
        <td>{{ $row->user_name }}</td>
        <td>{{ $row->party_name }}</td>
        <td>{{ $row->next_date }}</td>
        <td>{{ $row->note }}</td>
    </tr>
    @php $i++; @endphp
@endforeach
