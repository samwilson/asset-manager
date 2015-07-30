<?php

return array(
    'enabled' => env('ADLDAP_ENABLED'),
    'account_suffix' => env('ADLDAP_SUFFIX'),
    'domain_controllers' => array_filter(array(env('ADLDAP_DC1'), env('ADLDAP_DC2'), env('ADLDAP_DC3'))),
    'base_dn' => env('ADLDAP_BASEDN'),
    //'admin_username' => '',
    //'admin_password' => 'password',
    'real_primary_group' => true, // Returns the primary group (an educated guess).
    'use_ssl' => false, // If TLS is true this MUST be false.
    'use_tls' => false, // If SSL is true this MUST be false.
    'recursive_groups' => true,
);
