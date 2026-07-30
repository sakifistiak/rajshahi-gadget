<x-app-layout>
    <div x-data="{ addModalOpen: false, editModalOpen: false, editCustomer: { id: null, name: '', email: '' } }" class="p-6 space-y-6">
        
        <!-- Header Card (Card: rounded-lg = 8px, Button: rounded-md = 6px) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-lg border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Customer Management</h1>
                <p class="text-xs text-gray-500 mt-1">View, add, edit, or remove registered customers and users.</p>
            </div>
            <button @click="addModalOpen = true" 
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md shadow-sm transition-all cursor-pointer">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                Add New Customer
            </button>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-lg flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                    {{ session('success') }}
                </span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">Close</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-lg flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                    {{ session('error') }}
                </span>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900">Close</button>
            </div>
        @endif

        <!-- Customer Table Card (Card: rounded-lg = 8px) -->
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3.5">Customer</th>
                            <th class="px-5 py-3.5">Email Address</th>
                            <th class="px-5 py-3.5 text-center">Registered Date</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs uppercase shadow-sm">
                                            {{ substr($customer->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $customer->name }}</div>
                                            <div class="text-[10px] text-gray-400">ID: #{{ $customer->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-gray-700 font-mono">
                                    {{ $customer->email }}
                                </td>
                                <td class="px-5 py-4 text-center text-gray-500">
                                    {{ $customer->created_at ? $customer->created_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="editCustomer = { id: {{ $customer->id }}, name: '{{ addslashes($customer->name) }}', email: '{{ addslashes($customer->email) }}' }; editModalOpen = true" 
                                                class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors cursor-pointer"
                                                title="Edit Customer">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>
                                        @if($customer->id !== auth()->id())
                                            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer account?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors cursor-pointer"
                                                        title="Delete Customer">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-gray-400">
                                    No customers found. Click "Add New Customer" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($customers->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>

        <!-- Add Customer Modal (Modal: rounded-lg = 8px, Button: rounded-md = 6px) -->
        <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="addModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="addModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="addModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <form action="{{ route('admin.customers.store') }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-5 pb-4 sm:p-6">
                            <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                    <i data-lucide="user-plus" class="w-5 h-5 text-blue-600"></i>
                                    Add New Customer
                                </h3>
                                <button type="button" @click="addModalOpen = false" class="text-gray-400 hover:text-gray-600">
                                    <i data-lucide="x" class="w-5 h-5"></i>
                                </button>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Full Name</label>
                                    <input type="text" name="name" required class="w-full text-xs rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-2 px-3" placeholder="e.g. Tanvir Hasan" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email Address</label>
                                    <input type="email" name="email" required class="w-full text-xs rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-2 px-3" placeholder="customer@example.com" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Password</label>
                                    <input type="password" name="password" required class="w-full text-xs rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-2 px-3" placeholder="Minimum 8 characters" />
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-3 flex justify-end gap-2 border-t border-gray-100">
                            <button type="button" @click="addModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-800 rounded-md">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 rounded-md shadow-sm">Save Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Customer Modal -->
        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="editModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <form :action="'/admin/customers/' + editCustomer.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white px-6 pt-5 pb-4 sm:p-6">
                            <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                    <i data-lucide="edit-3" class="w-5 h-5 text-blue-600"></i>
                                    Edit Customer Profile
                                </h3>
                                <button type="button" @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600">
                                    <i data-lucide="x" class="w-5 h-5"></i>
                                </button>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Full Name</label>
                                    <input type="text" name="name" x-model="editCustomer.name" required class="w-full text-xs rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-2 px-3" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email Address</label>
                                    <input type="email" name="email" x-model="editCustomer.email" required class="w-full text-xs rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-2 px-3" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">New Password (Optional)</label>
                                    <input type="password" name="password" class="w-full text-xs rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-2 px-3" placeholder="Leave blank to keep current" />
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-3 flex justify-end gap-2 border-t border-gray-100">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-800 rounded-md">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 rounded-md shadow-sm">Update Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
