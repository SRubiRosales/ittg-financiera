@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                    <!--<form action="{{ route('clients.import.excel') }}" method="post" enctype="multipart">
                        @csrf
                        @if(Session::has('message'))
                        <p>{{ Session::get('message') }}</p>
                        @endif
                        <div class="form-group form-row">
                            <div class="col-md-8">
                                <input class="form-control-file" type="file" name="file">
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-secondary">{{ __('Export Clients')}}</button>
                            </div>
                        </div>
                    </form>-->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
