<div class="container mt-2">
    <div class="row py-2">
        <div class="col">
            <form id="bulk-upload" method="post" action="{{ route('bulk.upload', $meter->id)}}" enctype="multipart/form-data">
                @csrf
                <div class="row  align-items-center ">
                    <div class="form-group col-8 col-sm-6">
                        <input type="file" name="file" class="form-control" accept=".csv" id="file" required>
                    </div>
                    <div class="form-group col-4 col-sm-6">
                        <button type="submit" class="btn btn-secondary">Bulk Upload</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col">
            <form id="estimateForm" method="post" action="{{ route('estimate.reading', $meter->id)}}">
                @csrf
                <div class="row gy-2 gx-2 align-items-center d-flex">
                    <div class="form-group col">
                        <input type="date" class="form-control" id="new-date" name="date_read" required>
                    </div>
                    <div class="form-group col-auto">
                        <button type="submit" class="btn btn-primary">Calculate Estimated Reading</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Notice!!!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Estimated Annual Consumption for Meter has not been set yet. Kindly visit the edit page to update your meter information. 
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" href="{{ route('meter.edit', $meter->id) }}">Edit Meter Detail</a>
            </div>
        </div>
    </div>
</div>
<a class="btn btn-primary" id="modalButton" data-bs-toggle="modal" href="#exampleModalToggle" role="button" hidden>Open first modal</a>




