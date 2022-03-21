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
                        @if(isset($blamed))
                            <div class="text-center">
                                {!! img('characters', 'portrait', $blamed->character_id, 256, ['class' => 'profile-user-img img-fluid img-circle']) !!}
                            </div>
                            <h3 class="profile-username text-center">
                                {{ $blamed->character_name }}
                            </h3>
                        @else
                            <div class="text-center">
                                {!! img('characters', 'portrait', 2119442600, 256, ['class' => 'profile-user-img img-fluid img-circle']) !!}
                            </div>
                            <h3 class="profile-username text-center">
                                It seem like no one is guilty of blocking the queue
                            </h3>
                            <p class="text-center">
                                I would advise against creating a contract right now, as it will make you guilty of
                                blocking the queue.
                            </p>
                            @endif
                            </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            @isset($blamed)
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
            @endisset


                <div class="col-sm">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">PushX Queue Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <b>Queue</b>
                                            </td>
                                            <td>
                                                {{ $queue->outstanding }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <b>Ongoing</b>
                                            </td>
                                            <td>
                                                {{ $queue->ongoing }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <b>
                                                    Completed(Last day):
                                                </b>
                                            </td>
                                            <td>
                                                {{ $queue->dailycompleted }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Highscore</h5>
                        </div>
                        <div class="card-body">
                            @if(isset($record))
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <b>Character</b>
                                            </td>
                                            <td>
                                                {!! img('characters', 'portrait', $record->character_id, 32, ['class' => 'img-circle eve-icon medium-icon'], false) !!}
                                                {{ $record->character_name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <b>Contract Count</b>
                                            </td>
                                            <td>
                                                {{ $record->contract_count }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <b>Percentage of Queue</b>
                                            </td>
                                            <td>
                                                {{ round($record->contract_count / $record->queue_status->outstanding * 100,1) }}
                                                %
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>No one has set up a record yet. You can be here if you create a contract to Pushx
                                    now!</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @stop

        @push('javascript')
            @isset($blamed)
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
    @endisset
    @endpush

