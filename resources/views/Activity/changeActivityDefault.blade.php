@extends('app')
@section('content')
    <div class="container main-container">
        <div class="row">
            @include('includes.side_bar_menu')
            <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper">
                <div class="panel panel-default panel-create">
                    <div class="panel-content-heading panel-title-heading">Change Activity Default Values</div>
                    <div class="panel-body">
                        <div class="create-activity-form">
                            {!! form($form) !!}
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

