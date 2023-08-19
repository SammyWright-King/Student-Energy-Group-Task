@extends('layouts.app')

@section('content')
    <section class="container">
        <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ url('images/carousel-1.jpeg') }}" class="d-block w-100" alt="meter-image-1">
                </div>
                
            </div>
        </div>
    </section>

    <div class="container">
        <div class="row mt-5">
            <div class="col"></div>
            <div class="col-6">
                <form action="{{ route('meter.save') }} "method="post" id="newForm">
                    @csrf
                    <fieldset>
                        <legend>Add New Meter</legend>
                        <div class="mb-3">
                            <label for="identifier" class="form-label">Serial Number (MPXN)</label>
                            <input type="text" inputmode="numeric" pattern="(^S?)\d+$" id="identifier" class="form-control" 
                                    name="serial_number" placeholder="Unique Meter Identifier" required>
                            <p class="input-description ">accepts only numbers or numbers starting with capital "S"</p>
                        </div>

                        <div class="mb-3">
                            <label for="types" class="form-label">Meter Type</label>
                            <select id="types" class="form-select" name="meter_type">
                                <option disabled>select an option</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->description }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="eac" class="form-label">Estimated Annual Consumption</label>
                            <input type="number" id="eac" class="form-control" 
                                    name="eac"  min=2000 max=8000 required>
                        </div>

                        <div class="mb-3">
                            <label for="date_installed" class="form-label">Date Installed</label>
                            <input type="date" id="date_installed" class="form-control" name="date_installed" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </fieldset>
                </form>
            </div>
            <div class="col"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#newForm').submit(function(e) {
                let form = $(this)

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.post("{{ route('meter.save') }}",  form.serialize(), function(data) {
                    window.location.href = "{{ route('home') }}"
                }).fail(function(data) {
                    alert(data.error);
                });
               
                e.preventDefault();
            });
        });

    </script>
@endsection