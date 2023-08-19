<div class="container mt-3">
    <table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">mpxn</th>
            <th scope="col">Reading(Kwh)</th>
            <th scope="col">Date Taken</th>
            <!-- <th scope="col">Action</th> -->
            </tr>
        </thead>
        <tbody>
            @foreach($meter->readings as $key => $item)
                <tr>
                    <th scope="row">{{$key + 1}}</th>
                    <td> {{$meter->mpxn}} </td>
                    <td>{{$item->reading_value }}</td>
                    <td>{{$item->reading_date}}</td>
                    <!-- <td><button type="button" class="btn btn-md btn-primary">view</button></td> -->
                </tr>
            @endforeach
            
        </tbody>
    </table>
</div>