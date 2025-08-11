<?php
$request = \iwago\Utilities::server_domain('serror.html');
\iwago\Utilities::delete_form_data();
\Response::redirect($request);
