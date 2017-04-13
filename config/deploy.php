<?php

return array(
    /*
     * Where the projects should be deployed (/var/www/<project> ??).
     */
    'root' => env('DEPLOY_ROOT', sys_get_temp_dir()),
);
