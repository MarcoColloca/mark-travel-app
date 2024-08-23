@extends('layouts.app')

@section('title', 'Treni')

@section('content')
<section class="mt-5 py-1">
    <div class="container bg-dark py-4">
        <h1 class="title text-center text-success">Treni!</h1>
    </div>
</section>


<section class="py-5">
    <div class="container">
        <table class="table table-dark table-striped table-bordered border-success">
            <thead>
                <tr>
                    <th scope="col">Agenzia</th>
                    <th scope="col">Codice Treno</th>
                    <th scope="col" class="text-center">Carrozze</th>
                    <th scope="col">Stazione di Partenza</th>
                    <th scope="col">Stazione di Arrivo</th>
                    <th scope="col">Orario di Partenza</th>
                    <th scope="col">Orario di Arrivo</th>
                    <th scope="col">In orario</th>
                    <th scope="col">Cancellato</th>
                    <th scope="col" class="text-danger">Elimina</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($trains as $train)                
                    <tr>
                        <td>{{$train->agency}}</td>
                        <td>{{$train->train_code}}</td>
                        <td class="text-center">{{$train->train_carriages}}</td>
                        <td>{{$train->departure_station}}</td>
                        <td>{{$train->arrival_station}}</td>
                        <td>{{$train->departure_time}}</td>
                        <td>{{$train->arrival_time}}</td>
                        <td class="text-center">{{$train->on_time === 1 ? 'Sì' : 'No'}}</td>
                        <td class="text-center">{{$train->cancelled === 1 ? 'Sì' : 'No'}}</td>
                        <td>
                        <form class="comic-delete-form" action="{{route('trains.destroy', $train)}}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-link link-danger">Delete</button>
  
                            </form>
                        </td>
                    </tr>
                @endforeach               
            </tbody>
        </table>
    </div>
</section>
@endsection