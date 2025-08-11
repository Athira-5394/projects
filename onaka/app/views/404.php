<?php
$request = \iwago\Utilities::server_domain('notfound.html');
\iwago\Utilities::delete_form_data();
\Response::redirect($request);