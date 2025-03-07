<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Resources\Pages\Page;
use App\Models\Subscription;

class json extends Page
{
    protected static string $resource = SubscriptionResource::class;

    protected static string $view = 'filament.resources.subscription-resource.pages.json';

    protected function getViewData(): array 
    {
        return [
            'subscriptions' => Subscription::query()
                ->select('amount', 'created_at')
                ->orderBy('created_at')
                ->get(),
            'total' => Subscription::sum('amount'),
            'count' => Subscription::count(),
        ];
    }
}
