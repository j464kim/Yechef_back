<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\StripeService;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\Payout;
use App\Models\PayoutAccount;
use Illuminate\Contracts\Filesystem\Factory;

class PayoutController extends Controller
{
	private $storage;
	protected $stripe, $customer, $secretKey, $stripeService, $payment;

	public function __construct(
		Application $app,
		Customer $customer,
		StripeService $stripeService,
		Payout $payout,
		Factory $factory
	) {
		parent::__construct($app);

		$this->customer = $customer;
		$this->stripeService = $stripeService;
		$this->payout = $payout;
		$this->storage = $factory;
	}

	public function index(Request $request)
	{
		$connect = $this->stripeService->getOrCreateConnect($request);
		if (!$connect) {
			return response()->success($connect);
		}

		$balance = $this->stripeService->getBalance($connect->id);
		$payoutAccount = PayoutAccount::firstOrCreate(
			['connect_id' => $connect->id]
		);
		Log::info($connect);
		Log::info($balance);
		$payoutInfo = (object)array(
			'id'                => $payoutAccount->id,
			'country'           => $connect->country,
			'default_currency'  => strtoupper($connect->default_currency),
			'email'             => $connect->email,
			'total_count'       => $connect->external_accounts->total_count,
			'available_balance' => stripe_to_db($balance->available[0]->amount),
			'pending_balance'   => stripe_to_db($balance->pending[0]->amount)

		);

		return response()->success($payoutInfo);
	}

	public function getExternalAccounts(Request $request)
	{
		$connect = $this->stripeService->getConnect($request);
		Log::info($connect);

		return response()->success($connect->external_accounts);
	}

	public function store(Request $request)
	{
		$user = $this->getUser($request);
		$connect = $this->stripeService->getOrCreateConnect($request);

		$connect->tos_acceptance->date = time();
		// Assumes you're not using a proxy
		$connect->tos_acceptance->ip = $_SERVER['REMOTE_ADDR'];
		$connect->save();

		// Profit from kitchen generally goes to the owner who created the kitchen at first
		// Store the connect account into DB
		$payoutAccount = PayoutAccount::firstOrCreate(
			[
				'user_id'    => $user->id,
				'connect_id' => $connect->id,
				'country'    => $connect->country,
			]
		);

		return response()->success($payoutAccount);
	}

	public function update(Request $request, $id)
	{
		$validationRule = PayoutAccount::getValidationRule();
		$this->validate($request, $validationRule);

		$this->stripeService->updatePayoutAddress($request);
		return response()->success();
	}

	public function updatePersonalInfo(Request $request, $id)
	{
		$validationRule = PayoutAccount::getValidationRule(true);
		$this->validate($request, $validationRule);

		$this->stripeService->updatePayoutUserInfo($request);
		return response()->success();
	}

	public function createExternalAccount(Request $request)
	{
		$this->stripeService->addExternalAccount($request);

		return response()->success();
	}

	public function destroyExternalAccount(Request $request, $id)
	{
		$this->stripeService->deleteExternalAccount($request, $id);

		return response()->success();
	}

	public function switchDefaultAccount(Request $request)
	{
		$this->stripeService->switchDefaultExternalAccount($request);

		return response()->success();
	}

	public function uploadID(Request $request)
	{
		$file = $request->file('file');
		$fileName = 'yechef_' . date('d-m-Y_H-i-s') . '.' . uniqid() . '.' . $file->getClientOriginalExtension();
		$filePath = storage_path('app') . '/' . $fileName;
		Log::info($filePath);

		$localDisk = $this->storage->disk('local');
		$localDisk->put($fileName, file_get_contents($file), 'public');

		$this->stripeService->uploadFile($request, $filePath);

		$localDisk->delete($fileName);

		return response()->success();
	}

}
