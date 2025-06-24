@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Client</h2>
    <form action="{{ route('clients.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Basic client info -->
        <div class="form-group">
            <label>First Name:</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Middle Name:</label>
            <input type="text" name="middle_name" class="form-control">
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

        <!-- Representative Details -->
        <div class="form-group">
            <label>Representative First Name:</label>
            <input type="text" name="representative_first_name" class="form-control">
        </div>

        <div class="form-group">
            <label>Representative Middle Name:</label>
            <input type="text" name="representative_middle_name" class="form-control">
        </div>

        <div class="form-group">
            <label>Representative Last Name:</label>
            <input type="text" name="representative_last_name" class="form-control">
        </div>

        <div class="form-group">
            <label>Representative Contact Number:</label>
            <input type="text" name="representative_contact_number" class="form-control">
        </div>

        <!-- Vulnerability Sectors -->
        <div class="form-group">
            <label>Vulnerability Sectors:</label>
            @foreach($vulnerabilitySectors as $sector)
                <div class="form-check">
                    <input type="checkbox" name="vulnerability_sectors[]" value="{{ $sector->id }}" class="form-check-input" id="sector_{{ $sector->id }}">
                    <label class="form-check-label" for="sector_{{ $sector->id }}">{{ $sector->name }}</label>
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

        <button type="submit" class="btn btn-primary" id="saveBtn" disabled>Save</button>
        <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const saveBtn = document.getElementById('saveBtn');

    document.getElementById('assistance_type').addEventListener('change', function() {
        const typeId = this.value;
        saveBtn.disabled = true; // Always disable on change

        // Load requirements
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
                                <input type="checkbox" name="requirements[]" value="${r.id}" class="form-check-input requirement-checkbox" id="requirement_${r.id}">
                                <label class="form-check-label" for="requirement_${r.id}">${r.requirement_name}</label>
                            </div>
                        `;
                    });
                }
                document.getElementById('requirements_section').innerHTML = html;
                addValidationListeners();
            });

        // Load categories
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
                                <input type="radio" name="assistance_category_id" value="${c.id}" class="form-check-input category-radio" id="category_${c.id}">
                                <label class="form-check-label" for="category_${c.id}">${c.category_name}</label>
                            </div>
                        `;
                    });
                }
                document.getElementById('categories_section').innerHTML = html;
                addValidationListeners();
            });
    });

    function addValidationListeners() {
        document.querySelectorAll('.requirement-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', validateForm);
        });
        document.querySelectorAll('.category-radio').forEach(radio => {
            radio.addEventListener('change', validateForm);
        });
        validateForm();
    }

    function validateForm() {
        const requirementCheckboxes = document.querySelectorAll('.requirement-checkbox');
        const allChecked = Array.from(requirementCheckboxes).every(cb => cb.checked);
        const categorySelected = document.querySelector('.category-radio:checked') !== null;

        if ((requirementCheckboxes.length === 0 || allChecked) && categorySelected) {
            saveBtn.disabled = false;
        } else {
            saveBtn.disabled = true;
        }
    }
});
document.addEventListener('DOMContentLoaded', function () {
    // Initial validation on page load
    validateForm();
});

</script>
@endsection
