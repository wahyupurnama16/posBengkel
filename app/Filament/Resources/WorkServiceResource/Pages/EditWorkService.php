<?php
namespace App\Filament\Resources\WorkServiceResource\Pages;

use App\Filament\Resources\WorkServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkService extends EditRecord
{
    protected static string $resource = WorkServiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
