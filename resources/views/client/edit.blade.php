@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{ route('clients.update', $client->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>First Name:</label>
            <input type="text" name="first_name" class="form-control" value="{{ $client->first_name }}" required>
        </div>

        <div class="form-group">
            <label>Middle Name:</label>
            <input type="text" name="middle_name" class="form-control" value="{{ $client->middle_name }}">
        </div>

        <div class="form-group">
            <label>Last Name:</label>
            <input type="text" name="last_name" class="form-control" value="{{ $client->last_name }}" required>
        </div>

        <div class="form-group">
            <label>Sex:</label>
            <select name="sex" class="form-control" required>
                <option value="Male" {{ $client->sex == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ $client->sex == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

        <div class="form-group">
            <label>Birthday:</label>
            <input type="date" name="birthday" class="form-control" value="{{ $client->birthday ? $client->birthday->format('Y-m-d') : '' }}" required>
        </div>

        <div class="form-group">
            <label>Current Age:</label>
            <input type="text" class="form-control" value="{{ $client->age ?? 'Not calculated' }}" readonly>
            <small class="text-muted">Age is automatically calculated from birthday</small>
        </div>

        <div class="form-group">
            <label>Address:</label>
            <input type="text" name="address" class="form-control" value="{{ $client->address }}" required>
        </div>

        <div class="form-group">
            <label>Contact Number:</label>
            <input type="text" name="contact_number" class="form-control" value="{{ $client->contact_number }}" required>
        </div>

        <div class="form-group">
            <label>Municipality:</label>
            <select name="municipality_id" class="form-control" required>
                <option value="">Select Municipality</option>
                @foreach($municipalities as $municipality)
                    <option value="{{ $municipality->id }}" {{ $client->municipality_id == $municipality->id ? 'selected' : '' }}>
                        {{ $municipality->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Assistance Type:</label>
            <select name="assistance_type_id" id="assistance_type" class="form-control" required>
                <option value="">Select Assistance Type</option>
                @foreach($assistanceTypes as $type)
                    <option value="{{ $type->id }}" {{ $client->assistance_type_id == $type->id ? 'selected' : '' }}>
                        {{ $type->type_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Assistance Category:</label>
            <select name="assistance_category_id" id="assistance_category" class="form-control" required>
                <option value="">Select Assistance Category</option>
                @foreach($assistanceCategories as $category)
                    <option value="{{ $category->id }}" {{ $client->assistance_category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Vulnerability Sectors:</label>
            @foreach($vulnerabilitySectors as $sector)
                <div class="form-check">
                    <input type="checkbox" name="vulnerability_sectors[]" value="{{ $sector->id }}" 
                        class="form-check-input" id="sector_{{ $sector->id }}"
                        {{ $client->vulnerabilitySectors->contains($sector->id) ? 'checked' : '' }}>
                    <label class="form-check-label" for="sector_{{ $sector->id }}">{{ $sector->name }}</label>
                </div>
            @endforeach
        </div>

        <div class="form-group">
            <label>Representative First Name:</label>
            <input type="text" name="representative_first_name" class="form-control"
            value="{{ old('representative_first_name', $client->representative_first_name ?? '') }}">
        </div>

        <div class="form-group">
            <label>Representative Middle Name:</label>
            <input type="text" name="representative_middle_name" class="form-control"
            value="{{ old('representative_middle_name', $client->representative_middle_name ?? '') }}">
        </div>

        <div class="form-group">
            <label>Representative Last Name:</label>
            <input type="text" name="representative_last_name" class="form-control"
            value="{{ old('representative_last_name', $client->representative_last_name ?? '') }}">
        </div>

        <div class="form-group">
            <label>Representative Contact Number:</label>
            <input type="text" name="representative_contact_number" class="form-control"
            value="{{ old('representative_contact_number', $client->representative_contact_number ?? '') }}">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('clients.show', $client->id) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<!-- Ajax part -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#assistance_type').on('change', function () {
        var typeId = $(this).val();
        $('#assistance_category').empty().append('<option>Loading...</option>');

        if (typeId) {
            $.ajax({
                url: '/get-categories/' + typeId,
                type: 'GET',
                success: function (data) {
                    $('#assistance_category').empty().append('<option value="">Select Assistance Category</option>');
                    data.forEach(function (category) {
                        $('#assistance_category').append(
                            '<option value="'+category.id+'">'+category.category_name+'</option>'
                        );
                    });
                }
            });
        }
    });
</script>
@endsection
