@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Client</h2>
    <form action="{{ route('clients.store') }}" method="POST">
        @csrf

        <!-- Basic client info -->
        <div class="form-group">
            <label>First Name:</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Last Name:</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Sex:</label>
            <select name="sex" class="form-control" required>
                <option value="">Select Sex</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <div class="form-group">
            <label>Age:</label>
            <input type="number" name="age" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Municipality:</label>
            <select name="municipality_id" class="form-control" required>
                <option value="">Select Municipality</option>
                @foreach($municipalities as $municipality)
                    <option value="{{ $municipality->id }}">{{ $municipality->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Address:</label>
            <input type="text" name="address" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Contact Number:</label>
            <input type="text" name="contact_number" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Vulnerability Sectors:</label>
            @foreach($vulnerabilitySectors as $sector)
                <div class="form-check">
                    <input type="checkbox" name="vulnerability_sectors[]" value="{{ $sector->id }}" class="form-check-input">
                    <label class="form-check-label">{{ $sector->name }}</label>
                </div>
            @endforeach
        </div>

        <hr>

        <!-- Assistance Type -->
        <div class="form-group">
            <label>Assistance Type:</label>
            <select name="assistance_type_id" id="assistance_type" class="form-control" required>
                <option value="">Select Assistance Type</option>
                @foreach($assistanceTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Categories Placeholder -->
        <div class="form-group">
            <h5>Assistance Categories:</h5>
            <div id="categories_section">
                <p>Please select an assistance type to view categories.</p>
            </div>
        </div>

        <!-- Requirements Placeholder -->
        <div class="form-group">
            <h5>Requirements:</h5>
            <div id="requirements_section">
                <p>Please select an assistance type to view requirements.</p>
            </div>
        </div>


        <button type="submit" class="btn btn-primary">Save</button>

    </form>
</div>

<script>
document.getElementById('assistance_type').addEventListener('change', function() {
    const typeId = this.value;

    // Load requirements as checkboxes
    fetch(`/get-requirements/${typeId}`)
        .then(res => res.json())
        .then(data => {
            let html = '';
            if (data.length === 0) {
                html = '<p>No requirements found.</p>';
            } else {
                data.forEach(r => {
                    html += `
                        <div class="form-check">
                            <input type="checkbox" name="requirements[]" value="${r.id}" class="form-check-input" id="requirement_${r.id}">
                            <label class="form-check-label" for="requirement_${r.id}">${r.requirement_name}</label>
                        </div>
                    `;
                });
            }
            document.getElementById('requirements_section').innerHTML = html;
        });

    // Load categories as checkboxes
    fetch(`/get-categories/${typeId}`)
        .then(res => res.json())
        .then(data => {
            let html = '';
            if (data.length === 0) {
                html = '<p>No categories found.</p>';
            } else {
                data.forEach(c => {
                    html += `
                        <div class="form-check">
                            <input type="checkbox" name="assistance_categories[]" value="${c.id}" class="form-check-input" id="category_${c.id}">
                            <label class="form-check-label" for="category_${c.id}">${c.category_name}</label>
                        </div>
                    `;
                });
            }
            document.getElementById('categories_section').innerHTML = html;
        });
});
</script>
@endsection
