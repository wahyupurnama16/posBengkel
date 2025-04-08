<?php

namespace App\Filament\Resources\WorkServiceResource\Pages;

use App\Filament\Resources\WorkServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkServices extends ListRecords
{
    protected static string $resource = WorkServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
