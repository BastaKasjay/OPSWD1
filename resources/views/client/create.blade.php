<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Add Client</h1>
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
            <Label>Relationship</Label>
            <input type="text" name="realtionship" placeholder="Relationship">
        </div><div>
            <Label>Sex</Label>
            <input type="text" name="sex" placeholder="Sex">
        </div>
        <div>
            <Label>Age</Label>
            <input type="text" name="age" placeholder="Age">
        </div>
        <div>
            <Label>4ps</Label>
            <input type="text" name="4ps" placeholder="4ps?">
        </div>
        <div>
            <Label>Pwd</Label>
            <input type="text" name="pwd" placeholder="Pwd?">
        </div>
        <div>
            <Label>Address</Label>
            <input type="text" name="address" placeholder="Address">
        </div>
        <div>
            <Label>Contact Number</Label>
            <input type="text" name="contact number" placeholder="Contact Number">
        </div>
        <div>
            <Label>Valid Id</Label>
            <input type="text" name="valid id" placeholder="Valid Id">
        </div>

        <div>
            <Label>Municipality</Label>
            <select name="municipality">
                <option value="None">None</option>
                <option value="Atok">Atok</option>
                <option value="Bakun">Bakun</option>
                <option value="Bokod">Bokod</option>
                <option value="Bugias">Bugias</option>
                <option value="Itogon">Itogon</option>
                <option value="Trinidad">La Trinidad</option>
                <option value="Kabayan">Kabayan</option>
                <option value="Kapangan">Kapangan</option>
                <option value="Mankayan">Mankayan</option>
                <option value="Sablan">Sablan</option>
                <option value="Tuba">Tuba</option>
                <option value="Tublay">Tublay</option>
                <option value="Kibungan">Kibungan</option>
            </select>
        </div>
        
        <div>
            <Label>Assistance Type</Label>
            <select name="Assistance_Type">
                <option value="None">None</option>
                <option value="Medical">Medical</option>
                <option value="Burial">Burial</option>
                <option value="ESA (Emergency Shelter Assistance)">ESA (Emergency Shelter Assistance)</option>
                <option value="Transportation">Transportation</option>
            </select>
        </div>
        <div>
            <Label>Assistance Category</Label>
            <select name="Assistance_Category">
                <option value="None">None</option>
                <option value="hospital">hospital</option>
                <option value="Burial">Burial</option>
                <option value="ESA">ESA (Emergency Shelter Assistance)</option>
                <option value="Transportation">Transportation</option>
            </select>
        </div>


        <div>
            
            <input type="Submit" value="Add Client" class="btn btn-primary">
        </div>
    </form>
</body>
</html>