<?php

/**
 * This is an example root index file. YOURLS_SITE / index.php.
 * You will need to include the ?unghoster_tracking_confirmation_code below to verify your Unchoster pixel.
 */

// Start YOURLS engine
require_once dirname(__FILE__) . '/includes/load-yourls.php';

if (isset($_GET['unghoster_tracking_confirmation_code'])) {
    $account_id = yourls_get_option('unghoster', '');
    $notice = <<<HTML
<p>Verifying tracking code.</p>
HTML;
    $script = <<<SCRIPT
<script>
    !function(i,o,r){i[r]&&i[r].isLoaded||(i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].init=function(n,e){var t;i[r].loading||(i[r].loading=!0,(script=o.createElement("script")).type="text/javascript",script.src="https://cdn.unghoster.com/unghoster.js",script.async=!0,script.onload=function()
    {for(i[r].loaded=!0,i[r].loading=!1,i[r].load&&i[r].load(n,e);i[r].q&&i[r].q.length;){var t=i[r].q.shift();i[r].apply(null,t)}},(t=o.getElementsByTagName("script")[0]).parentNode.insertBefore(script,t))})}
    (window,document,"unghoster"),unghoster.init("$account_id");
</script>
SCRIPT;

    if (empty($account_id)) {
        $script = '';
        $notice = <<<HTML
<p>Error: Missing Account ID</p>
HTML;
    }
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
$script
<title>Unghoster Tracking Confirmation</title>
</head>
<body>
$notice
</body>
</html> 
HTML;
    exit;
}
