<?php
/**
 * @copyright Copyright (c) 2014-2016 www.whyceeyes.com
 * Example
 * 'ApiStatus'=> 'Active','depricated'
 * SessionDuration in minutes
 */

return [
    'host_name' => 'https://whyceeyes.com',
    'base_folder' => 'fam/api/V1.0',//no trailing or leading slash
    'ApiVersion'=> 'V1.0',//not in use now
    'ApiStatus'=> 'Active',
    'SessionDuration'=> 120,//minutes
    'otpSessionDuration'=>120,//minutes,
    'customer_docs' => 'resource/customer',
    'user_docs' => 'resource/user'
];