@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Field Mapping</h2>
    <p>Select fields from the dropdown to map.</p>            
    <table class="table">
        <thead>
            <tr>
                <th>System Fields</th>
                <th>File Fields</th>
            </tr>
        </thead>
        <tbody>
            <form method="POST" action="{{ route('save-import-data') }}">
                @csrf
            @foreach($tableCoulmns as $tableCoulmn)
            @if($tableCoulmn != 'id' && $tableCoulmn != 'status')
            <tr>
                <td>
                    {{$tableCoulmn}}
                    <input type="hidden" name="systemFields[]" value="{{$tableCoulmn}}">
                </td>
                <td>
                    <select name="tableCoumns[]" class="form-select" required="">
                        <option value="">Select</option>
                        @php $i=0 @endphp
                        @foreach($sheetCoulmns as $i=>$sheetCoulmn)
                        <option value="{{$i}}">{{$sheetCoulmn}}</option>
                        @php $i++ @endphp
                        @endforeach
                    </select>
                </td>
            </tr>
            @endif
            @endforeach
            <tr class="border-white"><td></td><td><button class="btn btn-primary float-end">Submit</button></td></tr>
            </form>
        </tbody>
    </table>
</div>
@endsection