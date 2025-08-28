@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 800px; margin: 0 auto; background: #f8f9fa; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 2rem;">
    <h2 class="mb-4" style="font-size: 2rem; font-weight: 600; color: #2d3a3a;">Add Client</h2>
    <form action="{{ route('clients.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">First Name:</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Middle Name:</label>
                <input type="text" name="middle_name" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Last Name:</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sex:</label>
                <select name="sex" class="form-control" required>
                    <option value="">Select Sex</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Birthday:</label>
                <input type="date" name="birthday" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Municipality:</label>
                <select name="municipality_id" class="form-control" required>
                    <option value="">Select Municipality</option>
                    @foreach($municipalities as $municipality)
                        <option value="{{ $municipality->id }}">{{ $municipality->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label">Address:</label>
                <input type="text" name="address" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Contact Number:</label>
                <input type="text" name="contact_number" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Client Valid ID Presented?:</label><br>
                <input type="hidden" name="valid_id" value="0">
                <input type="checkbox" name="valid_id" value="1" class="form-check-input" id="valid_id_checkbox">
                <label class="form-check-label" for="valid_id_checkbox">Yes</label>
            </div>
        </div>
        <div class="row g-3 mt-3" style="background: #eef6f3; border-radius: 8px; padding: 1rem;">
            <div class="col-12 mb-2">
                <div class="form-check mb-2">
                    <input type="checkbox" name="has_representative" id="has_representative" class="form-check-input" value="1" onchange="toggleRepresentativeFields()">
                    <label class="form-check-label" for="has_representative">Client has a Representative</label>
                </div>
            </div>
            <div id="representativeFields" style="display: none;">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Representative First Name:</label>
                        <input type="text" name="representative_first_name" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Representative Middle Name:</label>
                        <input type="text" name="representative_middle_name" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Representative Last Name:</label>
                        <input type="text" name="representative_last_name" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Representative Contact Number:</label>
                        <input type="text" name="representative_contact_number" class="form-control">
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="proof_of_relationship" id="proof_of_relationship" class="form-check-input" value="1">
                            <label for="proof_of_relationship" class="form-check-label">Proof of Relationship Provided</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group mt-4">
            <label class="form-label">Vulnerability Sectors:</label>
            <div class="row">
                @foreach($vulnerabilitySectors as $sector)
                    <div class="form-check col-md-4">
                        <input type="checkbox" name="vulnerability_sectors[]" value="{{ $sector->id }}" class="form-check-input" id="sector_{{ $sector->id }}">
                        <label class="form-check-label" for="sector_{{ $sector->id }}">{{ $sector->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <hr>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Assistance Type:</label>
                <select name="assistance_type_id" id="assistance_type" class="form-control" required>
                    <option value="">Select Assistance Type</option>
                    @foreach($assistanceTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <h5>Assistance Categories:</h5>
                <div id="categories_section">
                    <p>Please select an assistance type to view categories.</p>
                </div>
            </div>
            <div class="col-md-12">
                <h5>Requirements:</h5>
                <div id="requirements_section">
                    <p>Please select an assistance type to view requirements.</p>
                </div>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn" style="background: #3edbb3; color: #fff; border: none;">Save</button>
            <a href="{{ route('clients.index') }}" class="btn" style="background: #3edbb3; color: #fff; border: none;">Cancel</a>
        </div>
    </form>
</div>

<script>
document.getElementById('assistance_type').addEventListener('change', function() {
    const typeId = this.value;

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
                            <input type="checkbox" name="requirements[]" value="${r.id}" class="form-check-input" id="requirement_${r.id}">
                            <label class="form-check-label" for="requirement_${r.id}">${r.requirement_name}</label>
                        </div>
                    `;
                });
            }
            document.getElementById('requirements_section').innerHTML = html;
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
                            <input type="radio" name="assistance_category_id" value="${c.id}" class="form-check-input" id="category_${c.id}">
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
