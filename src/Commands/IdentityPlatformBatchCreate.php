<?php

namespace Firevel\IdentityPlatform\Commands;

use Firevel\IdentityPlatform\Services\IdentityPlatformService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use IdentityPlatform;

class IdentityPlatformBatchCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'identity-platform:batch-create {--tenant=} {--chunk-size=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import firebase users from Laravel using Google Identity Platform API.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tenant = $this->option('tenant');

        app(config('identityplatform.users.model'))
            ->chunk(
                $this->option('chunk-size'),
                function($users) use($tenant) {
                    $users->transform(function ($user) use($tenant) {
                        if (method_exists($user, 'toFirebaseUser')) {
                            $userData = $user->toFirebaseUser();
                        } else {
                            $userData = [
                                'localId' => $user->id,
                                'email' => $user->email,
                                'displayName' => $user->name,
                                'passwordHash' => base64_encode($user->password),
                            ];
                        }

                        if (!empty($tenant)) {
                            $userData['tenantId'] = $tenant;
                        }

                        return $userData;
                    });

                    $options = [];
                    if (!empty($tenant)) {
                        $options['tenantId'] = $tenant;
                    }
                    IdentityPlatform::batchCreate($users, $options);
                }
            );
        return 0;
    }
}
