@extends('layouts.app')

@section('title', 'User List')

@section('content')

    
    <h2 class="text-center my-5">Add User</h2>
    <form id="add-user-form" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" class="form-control" placeholder="Name" required>
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="col-md-6">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="col-md-6">
            <label for="image" class="form-label">Profile Image</label>
            <input type="file" id="image" class="form-control" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success w-100">Add User</button>
        </div>
    </form>

    <!-- Display error messages -->
    <div id="error-messages" class="text-danger mt-3"></div>

    <!-- Loader -->
    <div id="loader" class="text-center mt-4" style="display: none;">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- User List Section -->
    <h1 class="text-center mb-4">User List</h1>

    <div id="user-list" class="row gy-4">
        <!-- Users will be appended here -->
    </div>

    <div class="text-center mt-4">
        <button id="show-more" class="btn btn-primary">Show More</button>
    </div>
@endsection
