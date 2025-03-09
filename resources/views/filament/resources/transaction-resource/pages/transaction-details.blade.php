<x-filament::page>
  <x-filament::card>

    <div class="flex justify-between items-center">

      <div>
        <h2 class="text-2xl font-bold">Transaction Details</h2>
        <p class="text-gray-600">Transaction ID: {{ $record->id }}</p>
        <p class="text-gray-600">Customer: {{ $record->customer->name ?? 'N/A' }}</p>
        <p class="text-gray-600">Date: {{ $record->transaction_date }}</p>
      </div>
      <div>
        <h3 class="text-xl font-bold">Total: IDR {{ number_format($record->total_amount, 2) }}</h3>
      </div>
    </div>
  </x-filament::card>

  <div class="mt-6">
    <div class="mb-4">
      <x-filament::button tag="a"
        href="{{ route('filament.admin.resources.transaction-details.create', ['invoice' => $record->invoice]) }}"
        color="primary">
        Add Item
      </x-filament::button>
    </div>
    {{ $this->table }}
  </div>


</x-filament::page>