@extends('layouts.app')

@section('content')
    <new-transaction token="{{$clientToken}}" :user="{{json_encode($user)}}"></new-transaction>
@endsection
