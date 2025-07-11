@extends('app')
@section('content')
@include('sweetalert::alert')

<body class="bg-gray-100 ">

    <a href="/users" class=" w-10 h-10" title="Edit">
        <i class="fa-solid fa-arrow-left fa-2xl icon-dark-taupe mt-6"></i>
    </a>
    
    <h1 style="font-size: 38px;" class="text-[38px] font-semibold mb-4 text-center mt-10 title-text-dark-taupe">Create User</h1>
    <div class="container mx-auto mt-7 mb-16">     
        <div class="w-1/2 mx-auto bg-light-beige border-light-taupe custom-border-width rounded-lg shadow-md p-6">
            <form id="userCreateForm" method="POST" action="{{ route('users.store') }}">
                @csrf
                @method('POST')     
                
                <div class="mb-4 flex items-center space-x-5">
                    <label for="employee_id" class="font-medium text-gray-700">Select Employee:</label>
                    <!-- Dropdown Button -->
                    <div class="relative">
                        <button type="button" id="dropdownButton" class="font-medium border border-gray-300 bg-gray-300 p-2 rounded-md shadow-sm w-64 text-left relative">
                            <span>Select Employee</span>
                            <i class="fas fa-caret-down text-gray-500 absolute right-2 top-1/2 transform -translate-y-1/2"></i>
                        </button>
                        <!-- Dropdown Menu -->
                        <div id="dropdownMenu" class="absolute z-10 hidden bg-white border border-gray-300 rounded-md shadow-lg mt-1 w-full">
                            <!-- Search Bar -->
                            <div class="p-2 border-b border-gray-300">
                                <input type="text" id="employeeSearch" placeholder="Search employees..." class="w-full px-2 py-1 border border-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                            <!-- Employee List -->
                            <div class="p-2 max-h-48 overflow-y-auto">
                                @foreach($employees as $employee)
                                    <label class="flex items-center employee-item">
                                        <input type="radio" name="employee_id" value="{{ $employee->id }}" class="mr-2" 
                                            data-position="{{ $employee->position }}" 
                                            data-idnumber="{{ $employee->id_number }}" 
                                            data-office="{{ $employee->office ? $employee->office->office_name : '' }}" 
                                            onchange="updateSelectedEmployee()">
                                        <span class="employee-name">{{ $employee->f_name }} {{ $employee->m_initial }}. {{ $employee->l_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4 flex items-center space-x-2">
                    <label for="username" class=" font-medium text-gray-700">Username :</label>
                    <input type="text" id="username" name="username" class="rounded-md mt-1 border border-gray-300 shadow-sm p-1" required maxlength="20">
                </div>

                <div class="mb-4 flex items-center space-x-2">
                    <label for="position" class=" font-medium text-gray-700">Position :</label>
                    <input class="border-none bg-transparent" type="text" id="position" name="position" readonly>
                </div>

                <div class="mb-4 flex items-center space-x-6">
                    <label for="id_number" class=" font-medium text-gray-700">ID Number :</label>
                    <input type="text" id="id_number" name="id_number" class="border-none bg-transparent" readonly>
                </div>

                <div class="mb-4 flex items-center space-x-5">
                    <label for="office_name" class=" font-medium text-gray-700">Office :</label>
                    <input type="text" id="office_name" name="office_name" class="border-none bg-transparent" readonly>
                </div>

                @if(auth()->user()->user_type === 'System Admin')
                    <div class="mb-4 flex items-center space-x-5">
                        <label for="user_type" class="font-medium text-gray-700">User Type:</label>
                        <select style="width: 200px;" name="user_type" id="user_type" class="w-[200px] border p-1 rounded-sm shadow-sm">
                            <option disabled selected>Select User Type</option>
                            <option value="System Admin">System Admin</option>
                            <option value="Office Admin">Office Admin</option>
                            <option value="Office User">Office User</option>
                            <option value="Department Head">Department Head</option>
                            <option value="Record Officer">Record Officer</option>
                        </select>
                    </div>
                @else
                    <input type="hidden" name="user_type" value="Office User">
                @endif

                <div class="mb-4 flex items-center space-x-5">
                    <label for="password" class="font-medium text-gray-700">Password :</label>
                    <input type="text" style="width: 250px;" class="mt-1 w-[250px] border border-gray-300 rounded-md shadow-sm p-1" id="password" name="password" maxlength="15">
                </div>

                <div class="mb-4 flex items-center space-x-5">
                    <label for="password_confirmation" class="font-medium text-gray-700">Confirm Password :</label>
                    <input type="text" style="width: 250px;" class="mt-1 block w-[250px] border border-gray-300 rounded-md shadow-sm p-1" 
                        id="password_confirmation" name="password_confirmation" maxlength="15">
                </div>
                <div class="flex space-x-2">
                    <button type="button" onclick="openModal('submitModal')" class="bg-taupe shadow-lg text-white px-4 py-1 rounded-md hover:bg-taupe-dark">Submit</button>
                    <button type="button" onclick="openModal('cancelModal')" class="border-2 px-4 shadow-lg border-taupe text-taupe py-1 rounded-md hover:bg-soft-white">Cancel</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Modal for cancel -->
    <div id="cancelModal" class="fixed inset-0 flex items-center justify-center z-50 hidden bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm mx-auto">
            <h2 class="text-lg font-bold mb-4 text-center">Confirm Cancel</h2>
            <p class="text-center">Are you sure you want to cancel?</p>
            <div class="flex justify-center mt-4">
                <a class="w-[60px] bg-blue-500 text-white px-4 py-2 rounded-md" type="button" href="{{ route('users.index') }}">Yes</a>
                <button class="w-[60px] bg-gray-300 text-gray-700 px-4 py-2 rounded-md ml-2" onclick="closeModal('cancelModal')">No</button>
            </div>
        </div>
    </div>
    <!-- Modal for submit new employee -->
    <div id="submitModal" class="fixed inset-0 flex items-center justify-center z-50 hidden bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm mx-auto">
            <h2 class="text-lg font-bold mb-4 text-center">Confirm Submit</h2>
            <p class="text-center">Are you sure you want to create this user?</p>
            <div class="flex justify-center mt-4">
                <button class="w-[60px] bg-blue-500 text-white px-4 py-2 rounded-md" onclick="submitForm()">Yes</button>
                <button class="w-[60px] bg-gray-300 text-gray-700 px-4 py-2 rounded-md ml-2" onclick="closeModal('submitModal')">No</button>
            
            </div>
        </div>
    </div>
</body>
<script>
    const dropdownButton = document.getElementById('dropdownButton');
    const dropdownMenu = document.getElementById('dropdownMenu');

    // Toggle dropdown visibility
    dropdownButton.addEventListener('click', () => {
        dropdownMenu.classList.toggle('hidden');
        if (dropdownMenu.classList.contains('hidden')) {
            document.getElementById('employeeSearch').value = ''; // Clear search input
            filterEmployees(); // Reset the employee list
        }
    });

    // Close dropdown if clicked outside
    window.addEventListener('click', (event) => {
        if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.add('hidden');
        }
    });

    // Update the selected employee in the dropdown button and populate fields
    function updateSelectedEmployee() {
        const selectedEmployee = document.querySelector('input[name="employee_id"]:checked');
        if (selectedEmployee) {
            // Update dropdown button text
            const employeeName = selectedEmployee.parentElement.querySelector('.employee-name').textContent.trim();
            dropdownButton.textContent = employeeName;

            // Update Position, ID Number, and Office fields
            const position = selectedEmployee.getAttribute('data-position');
            const idNumber = selectedEmployee.getAttribute('data-idnumber');
            const office = selectedEmployee.getAttribute('data-office');

            document.getElementById('position').value = position || '';
            document.getElementById('id_number').value = idNumber || '';
            document.getElementById('office_name').value = office || '';
        }
    }

    // Function to filter employees in the dropdown
    function filterEmployees() {
        const searchInput = document.getElementById('employeeSearch');
        const employeeItems = document.querySelectorAll('.employee-item');

        const searchTerm = searchInput.value.toLowerCase();

        employeeItems.forEach(item => {
            const employeeName = item.querySelector('.employee-name').textContent.toLowerCase();
            if (employeeName.includes(searchTerm)) {
                item.style.display = 'flex'; // Show the employee
            } else {
                item.style.display = 'none'; // Hide the employee
            }
        });
    }

    // Add event listener to the search input
    document.getElementById('employeeSearch').addEventListener('input', filterEmployees);
</script>
<script>
    document.getElementById('employee_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        document.getElementById('position').value = selectedOption.getAttribute('data-position') || '';
        document.getElementById('id_number').value = selectedOption.getAttribute('data-idnumber') || '';
        document.getElementById('office_name').value = selectedOption.getAttribute('data-office') || '';
    });

    // Submit modal
    function submitForm() {
        document.getElementById('userCreateForm').submit();
    }

    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    window.onclick = function (event) {
        const modals = document.querySelectorAll('.fixed.inset-0'); // Select all modals
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    };
</script>

@endsection