<?php

namespace Tests\Feature;

use App\Models\Files;
use App\Models\Locations;
use App\Models\Offers;
use App\Models\RedeemedVoucher;
use App\Models\Regions;
use App\Models\VoucherRedemptions;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class);

describe('Offers API Tests', function () {
    beforeEach(function () {
    });


    it('fetches offers', function () {
        $region = Regions::factory()->create();
        $location = Locations::factory()->create();
        $offers = Offers::factory(7)->create();
        foreach ($offers as $offer) {
            if ($offer->is_regional) {
                $offer->region()->associate($region)->save();
                $offer->location()->associate($location)->save();
            }
        }

        $regionalOffers = Offers::where('is_regional', true)->get();
        $nonRegionalOffers = Offers::where('is_regional', false)->get();

        //TODO: Apps currently make wrong fetch offers requests
        // -> they send the regionUuid, but also send "is_regional = wrong", so the api understandably ignores the region
        $response = ApiTestHelper::makeGetRequest(
            '/api/offers?filters%5Bregion%5D%5Buuid%5D%5B$eq%5D=' . $region->uuid . '&filters%5B$or%5D%5B1%5D%5BisRegional%5D%5B$eq%5D=false'
        );
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertCount($offers->count(), $responseContent);

        foreach ($responseContent as $offer) {
            $arraySearchResult = array_search($offer['uuid'], $offers->pluck('uuid')->toArray());
            assertTrue($arraySearchResult >= 0 && $arraySearchResult !== false);
        }
    });


    it('fetches one offer', function () {
        $image = Files::factory()->create();
        $region = Regions::factory()->create();
        $location = Locations::factory()->create();
        $offer = Offers::factory()->create();
        $offer->region()->associate($region)->save();
        $offer->location()->associate($location)->save();
        $offer->image()->associate($image)->save();

        $response = ApiTestHelper::makeGetRequest('api/offers/' . $offer->uuid);
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals($responseContent['title'], $offer->title);
        assertEquals($responseContent['summary'], $offer->summary);
        assertEquals($responseContent['description'], $offer->description);
        assertEquals($responseContent['websiteUrl'], $offer->url);
        assertEquals($responseContent['redeemInterval'], $offer->redeem_interval);
        assertEquals($responseContent['genericVoucher'], $offer->generic_voucher);
        assertEquals($responseContent['storeType'], $offer->store_type);
        assertEquals($responseContent['uuid'], $offer->uuid);
        assertEquals(Carbon::make($responseContent['validFrom']), Carbon::make($offer->valid_from));
        assertEquals(Carbon::make($responseContent['validUntil']), Carbon::make($offer->valid_until));
        assertEquals($responseContent['region']['name'], $offer->region()->first()->name);
        assertEquals($responseContent['region']['uuid'], $offer->region()->first()->uuid);
        assertEquals($responseContent['location']['title'], $offer->location()->first()->title);
        assertEquals($responseContent['location']['coordinates']['latitude'], $offer->location()->first()->latitude);
        assertEquals($responseContent['location']['coordinates']['longitude'], $offer->location()->first()->longitude);
        assertEquals($responseContent['image']['url'], 'storage/' . $offer->image()->first()->url);
        assertEquals(true, $responseContent['voucherRedeem']['vouchersExhausted']);
        assertEquals(false, $responseContent['voucherRedeem']['redeemAvailable']);
        assertEquals(null, $responseContent['voucherRedeem']['redeemAvailableDate']);
        assertEquals(null, $responseContent['voucherRedeem']['redeemedCode']);

    });


    it('redeems offer', function () {
        $offer = Offers::factory()->create();
        $voucher = VoucherRedemptions::factory()->create();
        $voucher->offer()->associate($offer)->save();

        $response = ApiTestHelper::makeGetRequest('api/offers/' . $offer->uuid);
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals($offer->uuid, $responseContent['uuid']);
        assertEquals(false, $responseContent['voucherRedeem']['vouchersExhausted']);
        assertEquals(true, $responseContent['voucherRedeem']['redeemAvailable']);
        assertEquals(null, $responseContent['voucherRedeem']['redeemAvailableDate']);
        assertEquals(null, $responseContent['voucherRedeem']['redeemedCode']);

        $response = ApiTestHelper::makePatchRequest('api/offers/' . $offer->uuid . '/redeem');
        $response->assertStatus(200);

        $responseContent = json_decode($response->content(), true)['data'];

        assertEquals($offer->uuid, $responseContent['uuid']);
        assertEquals(false, $responseContent['voucherRedeem']['vouchersExhausted']);
        assertEquals(false, $responseContent['voucherRedeem']['redeemAvailable']);
        assertEquals(
            Carbon::now()->addDays($offer->redeem_interval) > Carbon::make($offer->valid_until) ?
                null : Carbon::now()->addDays($offer->redeem_interval)->format('Y-m-d\T00:00:00.000000\Z'),
            $responseContent['voucherRedeem']['redeemAvailableDate']
        );
        assertEquals($offer->voucherRedemptions()->first()->code, $responseContent['voucherRedeem']['redeemedCode']);
    });

});
