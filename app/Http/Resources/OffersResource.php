<?php

namespace App\Http\Resources;

use App\Http\Controllers\OffersController;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class OffersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $voucherRedemptionState = "pending";
        $user = Auth::guard('localAuth')->user();

        return [
            //TODO Location, address, discount, type, voucherRedemption
            'id' => $this->id,
            'title' => $this->title ?? '',
            'summary' => $this->summary ?? '',
            'description' => $this->description ?? '',
            'image' => new FilesResource($this->image),
            'date' => $this->valid_from,
            'region' => new RegionsResource($this->region),
            'location' => new LocationResource($this->location),
            'address' => new AddressesResource($this->address),
            'discount' => $this->discount ?? 0,
            'storeType' => $this->store_type,
            // TODO voucher Redemptions
            'voucherRedeem' => OffersController::getVoucherRedeemStatus($user, $this),
            'uuid' => $this->uuid,
            'validFrom' => $this->valid_from ?? '2000-04-30 00:00:00.000000',
            'validUntil' => $this->valid_until ?? '2000-04-30 00:00:00.000000',
            'genericVoucher' => $this->generic_voucher,
            'individualVoucher' => $this->individual_voucher,
            'isRegional' => $this->is_regional ?? false,
            'redeemInterval' => $this->redeem_interval,
            'views' => $this->views,
            'websiteUrl' => $this->url != null ? $this->getURL($this->url) : null,
        ];
    }

    public static function getURL(string $url): string {
        $parsedUrl = parse_url($url);

        if (empty($parsedUrl["scheme"])) {
            return "https://".$url;
        } else if ($parsedUrl["scheme"] == "https" || $parsedUrl["scheme"] == "http") {
            return $url;
        } else {
            return "https://".$url;
        }

    }
}
