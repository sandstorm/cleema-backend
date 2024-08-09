<?php

namespace Tests\Feature;

use App\Mail\ConfirmRegistrationMail;
use App\Models\UpUsers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

describe('Email Tests', function () {
    beforeEach(function () {
    });

    it('sends a confirmation email', function () {
        $user = UpUsers::factory()->create(['uuid' => ApiTestHelper::$userUuid]);

        $confirmation_token = hash('sha1', Str::random(20));
        Mail::fake();
        Mail::to($user->email)->send(new ConfirmRegistrationMail(env('APP_URL') . '/api/email-confirmation?confirmation=' . $confirmation_token));
        Mail::assertSent(ConfirmRegistrationMail::class);
        Mail::assertSent(ConfirmRegistrationMail::class, function ($mail) use ($user) {
            $this->assertTrue($mail->hasFrom(env('MAIL_FROM_ADDRESS')));
            $this->assertTrue($mail->hasTo($user->email));
            return true;
        });
    });

});
