<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contacts;
use App\Services\Opportunity;
use App\Services\Payments;
use App\Services\Subscriptions;
use App\Services\Transactions;
use App\Services\GoHighLevel;
use Exception;
use Illuminate\Http\JsonResponse;

class ContactDataController extends Controller
{
    private $contactsService;
    private $opportunityService;
    private $paymentsService;
    private $subscriptionsService;
    private $transactionsService;
    private $goHighLevelService;

    public function __construct()
    {
        $this->contactsService = new Contacts();
        $this->opportunityService = new Opportunity();
        $this->paymentsService = new Payments();
        $this->subscriptionsService = new Subscriptions();
        $this->transactionsService = new Transactions();
        $this->goHighLevelService = new GoHighLevel();
    }

    /**
     * Get a random contact with all their related data
     * 
     * @return JsonResponse
     */
    public function getRandomContactData(): JsonResponse
    {
        try {
            // First check if token is valid
            $tokenCheck = $this->goHighLevelService->checkToken();
            if ($tokenCheck->getStatusCode() !== 200) {
                // Try to renew token
                $renewResult = $this->goHighLevelService->renewToken();
                if ($renewResult && $renewResult->getStatusCode() !== 200) {
                    return response()->json([
                        'error' => 'Unable to authenticate with GoHighLevel API'
                    ], 401);
                }
            }

            // Get contacts
            $contactsResponse = $this->contactsService->contacts(1);
            
            if (!isset($contactsResponse['contacts']) || empty($contactsResponse['contacts'])) {
                return response()->json([
                    'error' => 'No contacts found'
                ], 404);
            }

            // Get a random contact
            $contacts = $contactsResponse['contacts'];
            $randomContact = $contacts[array_rand($contacts)];
            $contactId = $randomContact['id'];

            $result = [
                'contact' => $randomContact,
                'opportunities' => [],
                'payments' => [],
                'subscriptions' => [],
                'transactions' => []
            ];

            // Get opportunities for the contact
            try {
                $opportunitiesResponse = $this->opportunityService->Opportunities($contactId);
                if ($opportunitiesResponse->getStatusCode() === 200) {
                    $opportunitiesData = json_decode($opportunitiesResponse->getContent(), true);
                    if (isset($opportunitiesData['opportunities'])) {
                        $result['opportunities'] = $opportunitiesData['opportunities'];
                    } elseif (isset($opportunitiesData['data'])) {
                        $result['opportunities'] = $opportunitiesData['data'];
                    }
                } else {
                    $result['opportunities_error'] = 'API returned status: ' . $opportunitiesResponse->getStatusCode();
                }
            } catch (Exception $e) {
                $result['opportunities_error'] = 'Error fetching opportunities: ' . $e->getMessage();
            }

            // Get payments/transactions for the contact
            try {
                $paymentsResponse = $this->paymentsService->Transactions(0, $contactId);
                if ($paymentsResponse->getStatusCode() === 200) {
                    $paymentsData = json_decode($paymentsResponse->getContent(), true);
                    if (isset($paymentsData['transactions'])) {
                        $result['payments'] = $paymentsData['transactions'];
                    } elseif (isset($paymentsData['data'])) {
                        $result['payments'] = $paymentsData['data'];
                    }
                }
            } catch (Exception $e) {
                $result['payments_error'] = 'Error fetching payments: ' . $e->getMessage();
            }

            // Get subscriptions for the contact
            try {
                $subscriptionsResponse = $this->subscriptionsService->get(0, $contactId);
                if ($subscriptionsResponse->getStatusCode() === 200) {
                    $subscriptionsData = json_decode($subscriptionsResponse->getContent(), true);
                    if (isset($subscriptionsData['subscriptions'])) {
                        $result['subscriptions'] = $subscriptionsData['subscriptions'];
                    } elseif (isset($subscriptionsData['data'])) {
                        $result['subscriptions'] = $subscriptionsData['data'];
                    }
                }
            } catch (Exception $e) {
                $result['subscriptions_error'] = 'Error fetching subscriptions: ' . $e->getMessage();
            }

            // Get transactions specifically for the contact using the Transactions service
            try {
                $transactionsResponse = $this->transactionsService->get(0, $contactId);
                if ($transactionsResponse->getStatusCode() === 200) {
                    $transactionsData = json_decode($transactionsResponse->getContent(), true);
                    if (isset($transactionsData['transactions'])) {
                        $result['transactions'] = $transactionsData['transactions'];
                    } elseif (isset($transactionsData['data'])) {
                        $result['transactions'] = $transactionsData['data'];
                    }
                }
            } catch (Exception $e) {
                $result['transactions_error'] = 'Error fetching transactions: ' . $e->getMessage();
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Random contact data retrieved successfully'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching contact data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
