<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Resources\Pages\Page;

class SubscriptionProjection extends Page
{
    protected static string $resource = SubscriptionResource::class;

    protected static string $view = 'filament.resources.subscription-resource.pages.subscription-projection';

    protected static ?string $title = 'Proyección de suscripciones';
}
