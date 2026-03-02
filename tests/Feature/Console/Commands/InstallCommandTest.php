<?php

declare(strict_types=1);

use function Pest\Laravel\artisan;

it('can run the install command', function () {
    artisan('seokit:install')
        ->expectsConfirmation('Would you like to run the migrations now?', 'no')
        ->expectsConfirmation('Would you like to star this repo on GitHub?', 'no')
        ->assertSuccessful();
});
