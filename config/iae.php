<?php

return [
    'cloud_base_url' => env('IAE_CLOUD_BASE_URL', 'https://iae-sso.virtualfri.id'),
    'cloud_api_key' => env('IAE_CLOUD_API_KEY'),
    'team_id' => env('IAE_TEAM_ID', 'TEAM-01'),
    'group_code' => env('IAE_GROUP_CODE', 'BBK2HAB3'),
    'student_name' => env('IAE_STUDENT_NAME', 'M Zacky Dhaffary'),
    'student_nim' => env('IAE_STUDENT_NIM', '102022430021'),
    'event_routing_key' => env('IAE_EVENT_ROUTING_KEY', 'cart.item.added'),
];