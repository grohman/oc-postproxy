<?php

    // copy to /config/idesigning/postproxy/config.php

return [
    'services' => [
        'IDesigning\\PostProxy\\Services\\SendgridService'
    ],
    'collectors' => [
        'IDesigning\\PostProxy\\Collectors\\RainlabUser',
        'IDesigning\\PostProxy\\Collectors\\PostProxyRecipients',
    ]
];
