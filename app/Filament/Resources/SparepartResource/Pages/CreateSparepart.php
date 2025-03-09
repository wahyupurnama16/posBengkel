<?php

namespace App\Filament\Resources\SparepartResource\Pages;

use App\Filament\Resources\SparepartResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSparepart extends CreateRecord
{
    protected static string $resource = SparepartResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
