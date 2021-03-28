@extends('layouts.app')

@section('content')
    <listing
        :items="'{{json_encode($arr_transactions)}}'"
        :plan_list="'{{json_encode($plans)}}'"
        :subscription_list="'{{json_encode($list_subscripion)}}'"
    ></listing>
@endsection
