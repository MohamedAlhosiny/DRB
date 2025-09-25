@extends('layouts.app')
@section('title') list of products @endsection
@section('content')
<table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Category name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($product as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->price }} EGP</td>
                <td>{{ $item->desc }}</td>
                <td>{{ $item->category->name }}</td>
                <td><a href="" class="btn btn-info"> Show </a>
                    <a href="" class="btn btn-primary">Edit </a>
                    <a href="" class="btn btn-danger">Delete</a>
                   </td>

            </tr>
        @endforeach
    </tbody>
</table>


@endsection
