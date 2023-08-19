@extends('layouts.app')

@section('content')
    @include('meter.slide')

    <div class="container mt-2">
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="{{ route('meter.new') }}" class="btn btn-success" type="button">Add New Meter</a>
        </div>
    </div>

    <section class="container">
        @if ($meters->isEmpty())
            <div class="mt-3 text-center">
                <h3>No meters added yet!!!</h3>
            </div>
        @else
            <div class="mt-3">
                <h5>Available Meters</h5>

                <div class="row row-cols-3 row-cols-lg-4 g-2 g-lg-3">
                    @foreach($meters as $meter)
                        <div class="col d-flex align-items-stretch">
                            <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{$meter->mpxn}}</h5>
                                <p class="card-text">({{$meter->type->name}}) {{$meter->type->description}}</p>
                                <a href="{{ route('meter.show', $meter->id) }}" class="btn btn-primary">Check meter</a>
                            </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </section>
@endsection