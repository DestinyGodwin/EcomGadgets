<?php

namespace App\Enums\V1;

enum TransactionType: string
{
    case SUBSCRIPTION = 'SUB';
    case FEATURE = 'FEAT';
    case ADVERTISEMENT = 'ADV';

    public function label(): string
    {
        return match ($this) {
            self::SUBSCRIPTION => 'Subscription',
            self::FEATURE => 'Feature',
            self::ADVERTISEMENT => 'Advertisement',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->toArray();
    }
}
