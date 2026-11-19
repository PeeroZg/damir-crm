@extends('errors.layout')
@section('code', '403')
@section('title', __('Not allowed'))
@section('message', $exception->getMessage() && $exception->getMessage() !== 'This action is unauthorized.' ? $exception->getMessage() : __('You do not have permission to do that.'))
