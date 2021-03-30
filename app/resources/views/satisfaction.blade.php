@extends('layouts.app')

@section('content')
    <satisfaction :dados="{{json_encode($answers)}}"></satisfaction>
@endsection
