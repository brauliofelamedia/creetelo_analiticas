<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Subscription;
use App\Services\Subscriptions;
use App\Services\Transactions;
use App\Services\Contacts;

class Json extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.json';

    protected function getViewData(): array 
    {
        $transactions = new Subscriptions();
        $transactionService = new Transactions();
        $contactService = new Contacts();
        
        $responseSub = $transactions->get(0);
        $responseTra = $transactionService->get(0,'w6Vosgin754itUOAj6Mb');
        $responseCon = $contactService->contacts();
        
        $dataSub = response()->json(['data' => $responseSub]);
        $dataTra = response()->json(['data' => $responseTra]);
        $dataCon = response()->json(['data' => $responseCon]);
        
        $dataSubCasi = json_decode(json_encode($dataSub->getData()), true);
        $dataTraCasi = json_decode(json_encode($dataTra->getData()), true);
        $dataConCasi = json_decode(json_encode($dataCon->getData()), true);
        
        $dataSubFinal = $dataSubCasi['data']['original'];
        $dataTraFinal = $dataTraCasi['data']['original'];
        $dataConFinal = $dataConCasi['data']['contacts'];

        return [
            'subscription' => json_encode($dataSubFinal['data'][0], JSON_PRETTY_PRINT),
            'transaction' => json_encode($dataTraFinal['data'][0], JSON_PRETTY_PRINT),
            'contact' => json_encode($dataConFinal[0], JSON_PRETTY_PRINT),
        ];
    }
}
