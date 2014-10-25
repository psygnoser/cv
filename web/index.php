<?php

//phpinfo();

require_once '../CV/app/app.php';

$citae = new CV\app\App;
print $citae->render();