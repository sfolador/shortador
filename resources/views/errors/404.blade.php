@extends('errors.minimal')

@section('title', __('Not Found'))
@section('code', '404')

@php
    if (!isset($message))
    {
        $message = __('Not Found');
    }
@endphp
@section('message', $message)
