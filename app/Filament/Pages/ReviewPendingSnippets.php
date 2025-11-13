<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ExperienceSnippetResource;
use App\Models\ExperienceSnippet;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ReviewPendingSnippets extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static string $view = 'filament.pages.review-pending-snippets';

    protected static ?string $navigationLabel = 'Review Pending Snippets';

    protected static ?int $navigationSort = 99;

    protected static bool $shouldRegisterNavigation = false; // Only show when there are pending snippets

    public static function shouldRegisterNavigation(): bool
    {
        return session()->has('pending_snippets') && !empty(session('pending_snippets'));
    }

    public function mount(): void
    {
        if (!session()->has('pending_snippets') || empty(session('pending_snippets'))) {
            Notification::make()
                ->title('No Pending Snippets')
                ->body('Generate snippets from a CV first.')
                ->info()
                ->send();

            redirect()->to(ExperienceSnippetResource::getUrl('index'));
        }
    }

    public function table(Table $table): Table
    {
        $pendingSnippets = session('pending_snippets', []);

        // Create temporary collection for display
        $collection = collect($pendingSnippets)->map(function ($data, $index) {
            return (object) array_merge($data, ['id' => $index]);
        });

        return $table
            ->query(fn () => new \Illuminate\Database\Eloquent\Builder(new \Illuminate\Database\Query\Builder(app('db')->connection())))
            ->columns([
                IconColumn::make('selected')
                    ->label('')
                    ->boolean()
                    ->default(true)
                    ->toggleable(),

                TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->weight('medium'),

                TextColumn::make('content')
                    ->limit(60)
                    ->wrap()
                    ->tooltip(fn ($record) => $record->content ?? ''),

                TextColumn::make('category')
                    ->badge()
                    ->sortable(),

                TagsColumn::make('tags')
                    ->limit(3),

                TextColumn::make('role_type')
                    ->label('Role')
                    ->badge(),
            ])
            ->bulkActions([
                BulkAction::make('save_selected')
                    ->label('Save Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Collection $records) {
                        $saved = 0;
                        foreach ($records as $record) {
                            ExperienceSnippet::create([
                                'title' => $record->title,
                                'content' => $record->content,
                                'category' => $record->category,
                                'tags' => $record->tags,
                                'role_type' => $record->role_type,
                                'industry' => $record->industry ?? null,
                                'usage_count' => 0,
                            ]);
                            $saved++;
                        }

                        session()->forget('pending_snippets');

                        Notification::make()
                            ->title('Snippets Saved!')
                            ->body("{$saved} snippets added to your library.")
                            ->success()
                            ->send();

                        return redirect()->to(ExperienceSnippetResource::getUrl('index'));
                    }),

                BulkAction::make('delete_selected')
                    ->label('Discard Selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function () {
                        session()->forget('pending_snippets');

                        Notification::make()
                            ->title('Pending Snippets Discarded')
                            ->warning()
                            ->send();

                        return redirect()->to(ExperienceSnippetResource::getUrl('index'));
                    }),
            ])
            ->emptyStateHeading('No Pending Snippets')
            ->emptyStateDescription('Generate snippets from a CV first');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save_all')
                ->label('Save All')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    $pendingSnippets = session('pending_snippets', []);
                    $saved = 0;

                    foreach ($pendingSnippets as $data) {
                        ExperienceSnippet::create([
                            'title' => $data['title'],
                            'content' => $data['content'],
                            'category' => $data['category'],
                            'tags' => $data['tags'],
                            'role_type' => $data['role_type'],
                            'industry' => $data['industry'] ?? null,
                            'usage_count' => 0,
                        ]);
                        $saved++;
                    }

                    session()->forget('pending_snippets');

                    Notification::make()
                        ->title('All Snippets Saved!')
                        ->body("{$saved} snippets added to your library.")
                        ->success()
                        ->send();

                    return redirect()->to(ExperienceSnippetResource::getUrl('index'));
                }),

            Action::make('discard_all')
                ->label('Discard All')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    session()->forget('pending_snippets');

                    Notification::make()
                        ->title('All Pending Snippets Discarded')
                        ->warning()
                        ->send();

                    return redirect()->to(ExperienceSnippetResource::getUrl('index'));
                }),
        ];
    }
}
