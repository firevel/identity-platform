<?php

return [
	'api' => 'https://identitytoolkit.googleapis.com/v1/',
	'algorithm' => 'BCRYPT',
	'users' => [
		'model' => config('auth.providers.users.model'),
	],
	'project_id' => env('GOOGLE_CLOUD_PROJECT'),
];