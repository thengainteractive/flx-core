<?php

namespace App\Filament\Pages;

use App\Core\ModuleRegistry;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;
use Nwidart\Modules\Facades\Module;

use BackedEnum;
use UnitEnum;

class ManageModules extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSquaresPlus;

    protected static string | UnitEnum | null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Modules';

    protected static ?string $title = 'Module Manager';

    protected string $view = 'filament.pages.manage-modules';

    public static function canAccess(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return $user && $user->hasRole('Super Admin');
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(function () {
                $modules = Module::all();
                $data = [];
                foreach ($modules as $module) {
                    $data[] = [
                        'name' => $module->getName(),
                        'alias' => $module->getLowerName(),
                        'description' => $module->get('description') ?: 'No description provided.',
                        'version' => $module->get('version') ?: '1.0.0',
                        'is_enabled' => $module->isEnabled(),
                    ];
                }
                return collect($data);
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Module Name')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(60),
                TextColumn::make('version')
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_enabled')
                    ->label('Status')
                    ->boolean(),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('toggle')
                    ->label(fn ($record) => $record['is_enabled'] ? 'Disable' : 'Enable')
                    ->color(fn ($record) => $record['is_enabled'] ? 'danger' : 'success')
                    ->icon(fn ($record) => $record['is_enabled'] ? Heroicon::OutlinedXCircle : Heroicon::OutlinedCheckCircle)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $moduleName = $record['name'];
                        $module = Module::find($moduleName);

                        if (!$module) {
                            Notification::make()
                                ->title('Module not found')
                                ->danger()
                                ->send();
                            return;
                        }

                        if ($module->isEnabled()) {
                            $module->disable();
                            Notification::make()
                                ->title("Module '{$moduleName}' disabled")
                                ->warning()
                                ->send();
                        } else {
                            $module->enable();
                            Artisan::call('module:migrate', ['module' => $moduleName, '--force' => true]);
                            Notification::make()
                                ->title("Module '{$moduleName}' enabled and migrated")
                                ->success()
                                ->send();
                        }
                    }),
                \Filament\Actions\Action::make('migrate')
                    ->label('Migrate')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->color('gray')
                    ->visible(fn ($record) => $record['is_enabled'])
                    ->action(function ($record) {
                        Artisan::call('module:migrate', ['module' => $record['name'], '--force' => true]);
                        Notification::make()
                            ->title("Migrations ran for module '{$record['name']}'")
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
