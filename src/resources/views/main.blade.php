@extends('web::layouts.grids.12')

@section('title', "PushX-Blamer")
@section('page_header', "PushX-Blamer")


@section('full')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">The Guilty</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                        <div class="text-center">

                            {!! img('characters', 'portrait', $blamed->character_id, 256, ['class' => 'profile-user-img img-fluid img-circle']) !!}

                        </div>
                        <h3 class="profile-username text-center">
                            {{ $blamed->character_name }}
                        </h3>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Share of contracts</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <canvas id="piechart"></canvas>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-sm">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">PushX Queue Status</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li>
                                <b>
                                    <span>Queue</span>
                                </b>
                                <span class="float-right">
                                    {{ $queue->outstanding }}
                                </span>
                            </li>
                            <li>
                                <b>
                                    <span>Ongoing</span>
                                </b>
                                <span class="float-right">
                                    {{ $queue->ongoing }}
                                </span>
                            </li>
                            <li>
                                <b>
                                    <span>
                                        Completed(Last day):
                                    </span>
                                </b>
                                <span class="float-right">
                                    {{ $queue->dailycompleted }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('javascript')
    <script>
        const blamed_character_name = {!! json_encode($blamed->character_name) !!};
        const blamed_character_amount = {!! json_encode($blamed->contract_count) !!};
        const total_contracts = {!! json_encode($queue->outstanding) !!};

        $.get("https://koe-eve.com/api/pushx/queue", function (data) {
            console.log(data)
        })

        new Chart($("#piechart"), {
            type: "pie",
            data: {
                labels: [
                    blamed_character_name,
                    'Others',
                ],
                datasets: [
                    {
                        data: [blamed_character_amount, total_contracts],
                        backgroundColor: [
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)',
                        ],
                        hoverOffset: 4
                    }
                ]
            }
        })
    </script>
@endpush

