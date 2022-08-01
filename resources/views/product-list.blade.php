@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Products List</h2>
    <div class="row">
        <div class="col-md-9"></div>
        <div class="col-md-3">
            <select  id="categoryFilter" class="form-select float-end mb-3">
                <option value="All">Select Category to Filter List</option>
                <option value="All">All</option>
                @foreach($categoryList as $category)
                <option value="{{$category['id']}}">{{$category['title']}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <table id="laravel_datatable" class="table table-bordered yajra-datatable">
        <thead>
            <tr>
                <th>Sku</th>
                <th>title</th>
                <th>Description</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Category</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@endsection