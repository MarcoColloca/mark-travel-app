@extends('layouts.app')

@section('content')

<Trips
 :trips="{{$trips}}"
></Trips> 
@endsection
