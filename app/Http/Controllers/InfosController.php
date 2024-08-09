<?php

namespace App\Http\Controllers;

use App\Http\Resources\AboutsCollection;
use App\Http\Resources\AboutsResource;
use App\Http\Resources\LegalNoticesCollection;
use App\Http\Resources\LegalNoticesResource;
use App\Http\Resources\PartnershipResource;
use App\Http\Resources\PrivacyPoliciesCollection;
use App\Http\Resources\PrivacyPoliciesResource;
use App\Models\Abouts;
use App\Models\LegalNotices;
use App\Models\Partnerships;
use App\Models\PrivacyPolicies;

class InfosController extends Controller
{
    /**
     * controller function for route api/privacy-policy
     * @return PrivacyPoliciesResource[]
     */
    public function privacyPolicy ()
    {
        return [
            "data" => new PrivacyPoliciesResource(PrivacyPolicies::first())
        ];
    }

    /**
     * controller function for route api/legal-notice
     * @return LegalNoticesResource[]
     */
    public function legalNotice ()
    {
        return [
            "data" => new LegalNoticesResource(LegalNotices::first())
        ];
    }

    /**
     * controller function for route api/about
     * @return AboutsResource[]
     */
    public function about ()
    {
        return [
            "data" => new AboutsResource(Abouts::first())
        ];
    }

    /**
     * controller function for route api/about
     * @return PartnershipResource[]
     */
    public function partnership ()
    {
        return [
            "data" => new PartnershipResource(Partnerships::first())
        ];
    }
}
