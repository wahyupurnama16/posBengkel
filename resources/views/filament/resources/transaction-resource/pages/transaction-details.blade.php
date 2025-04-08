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


  <div class="mt-6">
    <div class="mb-4 flex justify-between items-center">
      <x-filament::button tag="a"
        href="{{ route('filament.admin.resources.work-services.create', ['invoice' => $record->invoice]) }}"
        color="primary">
        Add Jasa
      </x-filament::button>
    </div>

    <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
      <thead class="divide-y divide-gray-200 dark:divide-white/5">
        <tr class="bg-gray-50 dark:bg-white/5">
          <th
            class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-sparepart.name"
            style=";">
            <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
              <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                Jasa
              </span>
            </span>
          </th>
          <th
            class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-quantity"
            style=";">
            <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
              <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                Price
              </span>
            </span>
          </th>

          <th aria-label="Actions" class="fi-ta-actions-header-cell w-1"></th>
        </tr>
      </thead>


      <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
        @foreach($workServices as $service)
        <tr class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75">
          <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 ">
            <div class="fi-ta-col-wrp">
              <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                  <div class="flex ">
                    <div class="flex max-w-max" style="">
                      <div class="fi-ta-text-item inline-flex items-center gap-1.5  ">
                        <span class="fi-ta-text-item-label text-sm leading-6 text-gray-950 dark:text-white  " style="">
                          {{ $service ? $service->name :'' }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </td>
          <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 ">
            <div class="fi-ta-col-wrp">
              <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                  <div class="flex ">
                    <div class="flex max-w-max" style="">
                      <div class="fi-ta-text-item inline-flex items-center gap-1.5  ">
                        <span class="fi-ta-text-item-label text-sm leading-6 text-gray-950 dark:text-white  " style="">
                          {{ $service ? number_format($service->price, 2) : '' }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </td>
        </tr>
        @endforeach


        @if(count($workServices) === 0)
        <tr>
          <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
            No services available
          </td>
        </tr>
        @endif
      </tbody>
    </table>
  </div>
</x-filament::page>