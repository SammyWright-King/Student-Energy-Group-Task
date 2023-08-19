@extends('layouts.app')

@section('content')
    <!-- card displaying the meter information -->
    <section class="container">
        <div class="card text-white bg-success mb-3 w-100" style="heigth: 24rem;">
            <div class="card-header">{{ $meter->type->name }}</div>
            <div class="card-body">
                <h2 class="card-title">{{ $meter->mpxn }}</h2>
                <p class="card-text mt-3">{{ $meter->type->description }}</p>
                <p class="card-text mt-3">Estimated Annual Consumption: <span id="estimate">{{ $meter->estimated_annual_consumption }}</span></p>
                <p class="card-text mt-3">Number of Readings: <span id="readings">{{ $meter->readings->count() }}</span></p>
            </div>
        </div>
    </section>

    <!-- include section for bulk uploads and eac calculation -->
    @include('meter.reading.bulk-upload')

    <!-- section for add new meter reading -->
    <div class="container mt-2">
        <form action="{{ route('meter.reading.save', $meter->id) }}" method="post" id="newReadingForm">
            @csrf
            <fieldset>
                <legend>New Meter Reading</legend>

                <div class="row gy-2 gx-2 align-items-center">
                    <div class="col">
                        <label class="form-label" for="new-value">New value (Kwh)</label>
                        <input type="number" class="form-control" id="new-value" name="value" required>
                    </div>
                    <div class="col">
                        <label class="form-label" for="date-read">Date Read</label>
                        <input type="date" class="form-control" id="date-read" name="date_read" required>
                    </div>
                </div>

                <div class="float-end">
                    <button type="submit" class="btn btn-primary mt-3">Add New Reading</button>
                    <!-- <a type="button" class="btn btn-secondary mt-3">Bulk Addition</button> -->
                </div>
                
            </fieldset>
        </form>
    </div>

    <!-- include table to display meter readings -->
    @include('meter.readings')

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            //new meter reading form submit action
            $('#newReadingForm').submit(function(e) {
                let form = $(this)

                //post request
                $.post("{{ route('meter.reading.save', $meter->id) }}",  form.serialize(), function(response) {
                    console.log(response.message);
                    window.location.reload();
                }).fail(function(error) {
                    //return error message
                    alert(error.responseJSON.error);
                });
               
                e.preventDefault();
            });

            //estimate reading form submit action
            $('#estimateForm').submit(function(e) {
                eac = {{$meter->estimated_annual_consumption}}
                if (eac  == 0) {
                    //show modal prompting to go set eac for meter
                    $("#exampleModalToggle").modal('show');

                }else {
                    let form = $(this)

                    $.post("{{ route('estimate.reading', $meter->id)}}",  form.serialize(), function(response) {
                        console.log(response.message);
                        alert(response.message);
                        location.reload();
                    }).fail(function(error, status) {
                        console.log(error);
                        alert(error.responseJSON.error);
                    });
                }
                
                e.preventDefault();
            });

            //for bulk uploading
            $('#bulk-upload').on('submit', function(e){
        
                e.preventDefault();

                let formData = new FormData((this));

                $.ajax({
                    type:'POST',
                    url: "{{ route('bulk.upload', $meter->id) }}",
                    processData: false,
                    contentType: false,
                    data:formData,
                    success: (response)=>{
                        console.log(response.message);

                        alert(response.message);
                        //delay relaod by 2 seconds
                        location.reload();
                    },
                    error: (error)=>{
                        console.log(error);
                        alert(error.responseJSON.error);
                    }
                });
            });
        });

    </script>
@endsection