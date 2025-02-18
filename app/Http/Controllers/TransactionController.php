<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\Transactions;
use Carbon\Carbon;
use App\Models\Contact;

class TransactionController extends Controller
{
    public function insert()
    {
        $contacts = Contact::all();
        $transactions = new Transactions();

        foreach($contacts as $contact){
            $response = $transactions->get(0,$contact->lead_id);

            $data = response()->json([
                'data' => $response,
            ]);

            $dataFinal = json_decode(json_encode($data->getData()), true);
            $transactionTotal = $dataFinal['data']['original']['totalCount'];
            $transactionsData = $dataFinal['data']['original']['data'];

            foreach($transactionsData as $data){
                //Check if contact exist
                $contactExist = Contact::where('lead_id', $data['contactId'])->first();
            
                if($contactExist){
                    if($data['entitySourceName'] == 'Únete a Créetelo Mensual'){

                        //dd($data);

                        $transaction = new Transaction();
                        $transaction->currency = $data['currency'];
                        $transaction->amount = $data['amount'];
                        $transaction->status = $data['status'];
                        $transaction->livemode = $data['liveMode'];
                        $transaction->entity_type = $data['entityType'];
                        $transaction->entity_source_type = $data['entitySourceType'];
                        $transaction->entity_id = $data['entityId'];
                        $transaction->subscription_id = $data['subscriptionId'];
                        $transaction->charge_id = $data['chargeId'];
                        $transaction->summary = 'summary';
                        $transaction->entitySourceName = $data['entitySourceName'];
                        $transaction->create_time = Carbon::parse($data['createdAt'])->toDateTimeString();
                        $transaction->contact_id = $contactExist->id;
                        $transaction->save();

                    } elseif($data['entitySourceName'] == 'Únete a Créetelo Anual'){

                        $transaction = new Transaction();
                        $transaction->currency = $data['currency'];
                        $transaction->amount = $data['amount'];
                        $transaction->status = $data['status'];
                        $transaction->livemode = $data['liveMode'];
                        $transaction->entity_type = $data['entityType'];
                        $transaction->entity_source_type = $data['entitySourceType'];
                        $transaction->entity_id = $data['entityId'];
                        $transaction->subscription_id = $data['subscriptionId'];
                        $transaction->charge_id = $data['chargeId'];
                        $transaction->summary = 'summary';
                        $transaction->entitySourceName = $data['entitySourceName'];
                        $transaction->create_time = Carbon::parse($data['createdAt'])->toDateTimeString();
                        $transaction->contact_id = $contactExist->id;
                        $transaction->save();
                        
                    }
                }
            
            }
        }
    }
}
