<?php

return [
    'mfa_challenge_ttl_seconds' => (int) env('AUTH_MFA_CHALLENGE_TTL', 300),
    'step_up_ttl_seconds' => (int) env('AUTH_STEP_UP_TTL', 900),
];
