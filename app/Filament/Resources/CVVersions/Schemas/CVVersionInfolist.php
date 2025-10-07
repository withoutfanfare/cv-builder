<?php

namespace App\Filament\Resources\CVVersions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CVVersionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('cv.title')
                    ->label('Original CV')
                    ->icon('heroicon-o-document-text')
                    ->columnSpan(1),

                TextEntry::make('created_at')
                    ->label('Snapshot Created')
                    ->dateTime()
                    ->icon('heroicon-o-clock')
                    ->columnSpan(1),

                TextEntry::make('reason')
                    ->label('Version Reason')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->columnSpanFull(),

                TextEntry::make('snapshot_json')
                    ->label('CV Snapshot Preview')
                    ->formatStateUsing(function ($state) {
                        // Decode JSON if it's a string
                        $snapshot = is_string($state) ? json_decode($state, true) : $state;

                        if (! $snapshot || ! is_array($snapshot)) {
                            return 'Unable to load snapshot data';
                        }

                        $output = [];

                        // CV Title
                        if (isset($snapshot['title'])) {
                            $output[] = "**CV Title:** {$snapshot['title']}";
                        }

                        // Header Info
                        if (isset($snapshot['header_info'])) {
                            $header = $snapshot['header_info'];
                            $output[] = "\n**Contact Information:**";
                            if (isset($header['full_name'])) {
                                $output[] = "• Name: {$header['full_name']}";
                            }
                            if (isset($header['job_title'])) {
                                $output[] = "• Job Title: {$header['job_title']}";
                            }
                            if (isset($header['email'])) {
                                $output[] = "• Email: {$header['email']}";
                            }
                            if (isset($header['phone'])) {
                                $output[] = "• Phone: {$header['phone']}";
                            }
                            if (isset($header['location'])) {
                                $output[] = "• Location: {$header['location']}";
                            }
                        }

                        // Sections count
                        if (isset($snapshot['sections']) && is_array($snapshot['sections'])) {
                            $sectionCount = count($snapshot['sections']);
                            $output[] = "\n**CV Structure:**";
                            $output[] = "• Total Sections: {$sectionCount}";

                            foreach ($snapshot['sections'] as $section) {
                                $type = $section['section_type'] ?? 'unknown';
                                $output[] = '  - '.ucfirst($type);
                            }
                        }

                        return implode("\n", $output);
                    })
                    ->markdown()
                    ->columnSpanFull(),
            ]);
    }
}
