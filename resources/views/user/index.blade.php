@extends('layouts.app')

@section('title', 'Users')


@section('content')
<div class="container"> 
    <h1 class="text-2xl font-bold mb-4">User</h1>

    <div class="flex justify-center">
        <div class="w-full mb-16">
            <!-- Top Controls -->
            <div class="flex justify-between mb-4">
                <div class="relative w-1/4">
                    <a href="{{ route('users.create') }}"
                       class="cursor-pointer bg-taupe text-black font-bold py-2 px-4 rounded shadow-sm hover:bg-taupe-dark flex items-center justify-center">
                        Create
                    </a>
                </div>

                <div class="relative w-sm mt-1 mr-1">
                    <form action="{{ route('users.index') }}" method="GET" class="flex items-center space-x-2">
                        <input
                            type="text"
                            name="search"
                            placeholder="Search users"
                            value="{{ request('search') }}"
                            id="search-bar"
                            class="border border-gray-300 rounded-md shadow-sm p-2 pl-10 pr-10 w-full">
                        <button
                            type="submit"
                            class="bg-blue-500 text-black px-4 py-2 rounded-md hover:bg-blue-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            @if(request('search'))
            <div id="search-count" class="flex justify-end text-gray-600 mb-2">
                Showing {{ $users->total() }} result(s) for "{{ request('search') }}"
            </div>
            @endif

            <!-- User Table -->
            <div class="w-full overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg text-sm">
                    @php
                        function sortLink($field, $label) {
                            $currentSort = request('sort');
                            $currentDirection = request('direction');
                            $direction = 'asc';

                            if ($currentSort === $field && $currentDirection === 'asc') {
                                $direction = 'desc';
                            } elseif ($currentSort === $field && $currentDirection === 'desc') {
                                $direction = null;
                            }

                            $params = array_filter(array_merge(request()->all(), [
                                'sort' => $direction ? $field : null,
                                'direction' => $direction
                            ]));

                            $url = route('users.index', $params);
                            $icon = 'fa-sort';
                            if ($currentSort === $field) {
                                $icon = $currentDirection === 'asc' ? 'fa-sort-up' : ($currentDirection === 'desc' ? 'fa-sort-down' : 'fa-sort');
                            }

                            return '<a href="'.$url.'" class="flex items-center space-x-1">'.
                                '<span>'.$label.'</span>'.
                                '<i class="fa-solid '.$icon.' text-grey-600"></i>'.
                                '</a>';
                        }
                    @endphp

                    <thead class="bg-custom-gray">
                        <tr>
                            <th class="py-3 px-4 text-left text-gray-600 font-bold">ID</th>
                            <th class="py-3 px-4 text-left text-gray-600 font-bold">Username</th>
                            <th class="py-3 px-4 text-left text-gray-600 font-bold"> Name</th>
                            <th class="py-3 px-4 text-left text-gray-600 font-bold">{!! sortLink('role', 'Role') !!}</th>
                            <th class="py-3 px-4 text-right text-gray-600 font-bold">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-200 even:bg-gray-100 odd:bg-soft-white">
                            <td class="py-2 px-4 border-b">{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-2 px-4 border-b">{{ $user->username }}</td>
                            <td class="py-2 px-4 border-b">
                                {{ $user->employee->first_name }}
                                @if($user->employee->middle_name)
                                    {{ strtoupper(substr($user->employee->middle_name, 0, 1)) }}.
                                @endif
                                {{ $user->employee->last_name }}
                            </td>
                            <td class="py-2 px-4 border-b">{{ $user->role }}</td>
                            <td class="py-2 px-4 border-b text-right">
                                <a href="{{ route('users.edit', $user) }}" class="text-black hover:text-yellow-700 mx-2" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button onclick="openDeleteModal('{{ $user->id }}')" class="text-black hover:text-red-700" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-end">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal"
        class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50"
        style="z-index: 1050;">
        
        <div class="bg-white rounded shadow-lg p-4" style="max-width: 400px; width: 100%;">
            <h2 class="h5 text-center mb-3">Confirm Delete</h2>
            <p class="text-center">Are you sure you want to delete this user?</p>
            <div class="d-flex justify-content-center mt-3">
                <button type="button" class="btn btn-secondary me-2" onclick="closeModal()">Cancel</button>
                <form id="deleteForm" action="" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
