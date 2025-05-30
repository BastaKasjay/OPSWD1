<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="">
        <button>
            <a href="{{ route('home') }}" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
            ← Back to Home
        </a>
        </button>
    </form>
    <h1>Patient Information</h1>
    <form method="post" action="{{ route('client.docs') }}">
        @csrf
        @method('POST')
        <div>
            <Label>First Name</Label>
            <input type="text" name="name" placeholder="Name">
        </div>
        <div>
            <Label>Middle Name</Label>
            <input type="text" name="middle_name" placeholder="Middle name">
        </div>
        <div>
            <Label>Last Name</Label>
            <input type="text" name="last_name" placeholder="Last_name">
        </div>
        <div>
            <Label>Sex</Label>
            <input type="text" name="sex" placeholder="Sex">
        </div>
        <div>
            <Label>Age</Label>
            <input type="text" name="age" placeholder="Age">
        </div>
        <div>
            <Label>PWD</Label>
            <select name="pwd" id="">
                <option value="">Select</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>
        <div>

        <div>
            <Label>4Ps</Label>
            <select name="4ps" id="">
                <option value="">Select</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>
        <div></div>

        <div>    
            <Label>Address</Label>
            <input type="text" name="address" placeholder="Address">
        </div>
        <div>
            <Label>Contact Number</Label>
            <input type="text" name="contact_number" placeholder="Contact Number">
        </div>
        <div>
            <Label>Valid Id</Label>
            <input type="text" name="valid id" placeholder="Valid Id">
        </div>

        <div>
            <Label>Municipality</Label>
            <select name="municipality_id">
                <option value="">Select Municipality</option>
                @foreach($municipalities as $municipality)
                    <option value="{{ $municipality->id }}">{{ $municipality->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div>
            <Label>Assistance Type</Label>
            <select name="assistance_type" id="assistance_type">
                <option value="None">None</option>
                <option value="Medical">Medical</option>
                <option value="Burial">Burial</option>
                <option value="ESA (Emergency Shelter Assistance)">ESA (Emergency Shelter Assistance)</option>
                <option value="Transportation">Transportation</option>
            </select>
        </div>
        <div>
            <Label>Assistance Category</Label>
            <select name="assistance_category" id="assistance_category">
                <option value="">None</option>
            </select>
        </div>

        <h2>Client Information:</h2>

        <div>
            <Label>First Name</Label>
            <input type="text" name="client_name" placeholder="Client First Name">
        </div>

        <div>
            <Label>Middle Name</Label>
            <input type="text" name="client_middle_name" placeholder="Client Middle Name">
        </div>
    
        <div>
            <Label>Last Name</Label>
            <input type="text" name="client_last_name" placeholder="Client Last Name">
        </div>
        <div>
            <Label>Relationship</Label>
            <input type="text" name="client_relationship" placeholder="Relationship to Patient">
        </div>


        <input type="Submit" value="Add" class="btn btn-primary">
    
    </form>


    <script>
        const categories = {
            Medical: [
                { value: 'Hospital', text: 'Hospital' },
                { value: 'Purchasing Medicine', text: 'Purchasing Medicine' }
            ],
            Burial: [
                { value: 'Burial disaster/injured', text: 'Burial disaster/Injured' },
                { value: 'Burial illness/natural death', text: 'Burial illness/Natural Death' }
            ],
            "ESA (Emergency Shelter Assistance)": [
                { value:'House totally damaged', text: 'House totally damaged' },
                { value:'House partially damaged', text: 'House partially damaged' }
            ],
            Transportation: [
                { value: 'Abused', text: 'Abused' }
            ]
        };

        document.getElementById('assistance_type').addEventListener('change', function() {
            const type = this.value;
            const categorySelect = document.getElementById('assistance_category');
            categorySelect.innerHTML = '<option value="">None</option>';
            if (categories[type]) {
                categories[type].forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.value;
                    option.text = cat.text;
                    categorySelect.appendChild(option);
                });
            }
        });
    </script>
</body>
</html>