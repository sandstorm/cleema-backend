<?php

namespace App\Http\Controllers;

use App\Http\Resources\OffersCollection;
use App\Http\Resources\OffersResource;
use App\Models\NewsEntries;
use App\Models\Offers;
use App\Models\Regions;
use App\Models\VoucherRedemptions;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OffersController extends Controller
{
    public function fetch (Request $request)
    {
        // new filters
        $regionUUID = request('region');
        $isRegional = request('isRegional');

        if(!$regionUUID && !$isRegional) {
            $filters = request('filters');
            if(isset($filters['$or']['0']['region']['uuid']['$eq'])) {
                $regionUUID = $filters['$or']['0']['region']['uuid']['$eq'];
            }
            if(isset($filters['$or']['1']['isRegional']['$eq'])) {
                $isRegional = $filters['$or']['1']['isRegional']['$eq'];
            }
        }

        if($regionUUID && $isRegional){
            $regions = Regions::where('is_supraregional', true)->pluck('uuid', 'id');
            $regions[] = $regionUUID;
            $offers = Offers::whereDate('valid_from', '<', Carbon::now())
                ->whereDate('valid_until','>=', Carbon::now())
                ->where(function ($query) use ($isRegional, $regions) {
                    $query->whereRelation('region',
                        fn($query) => $query->whereIn('uuid', $regions))
                        ->orWhere('is_regional', $isRegional);
                })
                ->get();
        }
        else{
            $offers = Offers::whereDate('valid_from', '<', Carbon::now())
                        ->whereDate('valid_until','>=', Carbon::now())
                        ->get();
        }

        return new OffersCollection($offers);
    }

    public function fetchOne(String $uuid)
    {
        $offer = Offers::where('uuid', '=', $uuid)->first();
        return new OffersResource($offer);
    }

    /**
     * @param String $uuid
     * @return OffersResource|array|\Illuminate\Http\JsonResponse
     */
    public function redeem (String $uuid) {
        // get logged in user
        $user = Auth::guard('localAuth')->user();
        if(!$user){
            return Controller::getApiErrorMessage("Authentication failed");
        }
        // get offer from the request
        $offer = Offers::where('uuid', '=', $uuid)->first();
        if(!$offer){
            return Controller::getApiErrorMessage('No offer with given UUID found');
        }
        // get redeemStatus to handle the request
        $redeemStatus = OffersController::getVoucherRedeemStatus($user, $offer);

        // only redeem when voucher is not exhausted and a redeem is available --> from status
        if ($redeemStatus && !$redeemStatus['vouchersExhausted'] && $redeemStatus['redeemAvailable']) {
            // get first not redeemed voucher redemption
            $notRedeemed = VoucherRedemptions::where('offer_id', '=', $offer->id)
                ->whereNull('redeemed_at')
                ->first();
            // update it and set redeemed at to current date
            $notRedeemed->update([
                'redeemed_at' => Carbon::now() // Assuming redeemedAt is a date field
            ]);
            // associate user with the voucher redemption
            $notRedeemed->redeemer()->associate($user);
        }
        else {
            return response()->json(['reason' => 'Already redeemed or vouchers exhausted.'], 400);
        }

        $notRedeemed->save();
        // reload voucher redemption and user
        $notRedeemed->load('redeemer');
        $user->load('voucherRedemptions');
        // return offer as resource for the api
        return new OffersResource($offer);
    }

    /**
     * Get current Status from a Voucher (last redeemedCode, exhausted, redeemAvailable, availableDate)
     * @param $user
     * @param $offer
     * @return array|mixed|null
     */
    static function getVoucherRedeemStatus($user, $offer) {
        if(!$user || !$offer){
            return null;
        }
        // get voucherRedemptions from offer
        $vouchers = $offer->voucherRedemptions;
        // check if there are not redeemd voucher left
        $vouchersExhausted = $vouchers->whereNull('redeemed_at')->count() == 0;
        $redemptionsOfUser = $user->voucherRedemptions->where('offer_id', '=', $offer->id);

        $redeemAvailable = true;
        $redeemAvailableDate = null;
        $redeemedCode = null;
        $validUntil = $offer->valid_until;
        $redeemInterval = OffersController::getCurrentVoucherRedeemInterval($offer, Carbon::now());

        foreach ($redemptionsOfUser as $r) {
            $redeemedAt = $r->redeemed_at;
            $redeemedCode = $r->code; // Assuming 'code' is a field in the redemption data
            if ($redeemedAt >= $redeemInterval['intervalStart'] && $redeemedAt <= $redeemInterval['intervalEnd']) {
                $redeemAvailable = false;
                $startOfNextInterval = $redeemInterval['intervalEnd']->addDays(1); // Adding 1 day to interval end
                $redeemAvailableDate = $validUntil > $redeemInterval['intervalEnd'] ? $startOfNextInterval : null;
            }
        }

        return [
            'vouchersExhausted' => $vouchersExhausted,
            'redeemAvailable' => $redeemAvailable && !$vouchersExhausted,
            'redeemAvailableDate' => $redeemAvailableDate,
            'redeemedCode' => $redeemedCode,
        ];
    }

    static function getCurrentVoucherRedeemInterval($offer, $now) {
        if (!$offer->valid_from || !$offer->valid_until || !$offer->redeem_interval) {
            return null;
        }

        $validFrom = $offer->valid_from;
        $validUntil = $offer->valid_until;
        $intervalStart = $validFrom;

        assert($intervalStart instanceof Carbon);

        while ($validUntil >= $intervalStart) {
            $intervalEnd = $intervalStart->copy()->addDays($offer->redeem_interval - 1);

            if ($intervalEnd > $validUntil) {
                $intervalEnd = $validUntil;
            }

            if ($now->startOfDay() <= $intervalEnd) {
                return [
                    'intervalStart' => $intervalStart,
                    'intervalEnd' => $intervalEnd,
                ];
            }

            $intervalStart = $intervalEnd->copy()->addDays(1);
        }

        return null;
    }

}
