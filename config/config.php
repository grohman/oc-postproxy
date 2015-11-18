<?php

return [
	'services' => env('POSTPROXY_SERVICES', [
		'sendgrid' => [
            'label' => 'SendGrid',
            'class' => 'IDesigning\\PostProxy\\Services\\SendgridService'
            ]
	])
];
