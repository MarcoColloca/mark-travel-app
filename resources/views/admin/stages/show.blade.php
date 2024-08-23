@extends('layouts.app')

@section('content')


<Stage
:stage="{{$stage}}"
:images="{{$images}}"
></Stage>
@endsection
