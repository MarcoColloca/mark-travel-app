@extends('layouts.app')

@section('content')

<Days
:days="{{$days}}"
:trip="{{$trip}}"
></Days>
@endsection
