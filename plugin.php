<?php
/**
 * Plugin Name: Unghoster
 * Description: Retrieve person-level contact information including emails and Linkedin for your website visitors. <a href="https://www.unghoster.com/signup?ref=thefrosty" target="_blank">Signup for free</a>.
 * Author: Austin Passy
 * Author URI: https://github.com/thefrosty
 * Version: 0.1.0
 * Requires PHP: 8.1
 * Plugin URI: https://github.com/thefrosty/yourls-unghoster
 */

namespace TheFrosty\YourlsUnghoster;

defined('YOURLS_ABSPATH') || exit;

use function defined;
use function filter_var;
use function strval;
use function yourls_add_action;
use function yourls_create_nonce;
use function yourls_get_option;
use function yourls_register_plugin_page;
use function yourls_update_option;
use function yourls_verify_nonce;
use const FILTER_SANITIZE_FULL_SPECIAL_CHARS;

// Plugin settings page etc.
yourls_add_action('plugins_loaded', static function (): void {
    yourls_register_plugin_page('unghoster', 'Unghoster', static function () {
        // Check if form was submitted
        if (isset($_POST['unghoster'])) {
            yourls_verify_nonce('unghoster');
            yourls_update_option('unghoster', filter_var($_POST['unghoster'],FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        }

        $nonce = yourls_create_nonce('unghoster');
        $value = getUnghosterId();

        echo <<<HTML
        <main>
            <h2>Unghoster Settings</h2>
            <form method="post">
            <p>
                <label>Unghoster ID</label>
                <input type="text" name="unghoster" value="$value">
            </p>
            <p>
                <input type="submit" value="Save" class="button">
                <input type="hidden" name="nonce" value="$nonce">
            </p>
            </form>
        </main>
HTML;
    });
});

function getUnghosterId(): string
{
    return strval(yourls_get_option('unghoster', ''));
}

// Hook our custom function into the 'pre_redirect' event
yourls_add_action('pre_redirect', static function (array $args): void {
    $account_id = getUnghosterId();
    if (empty($account_id)) {
        return;
    }

    $url = $args[0] ?? '';

    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="5; url=$url" />
<style>
.slider {
    position: absolute;
    width: 400px;
    height: 2px;
    margin-top: -30px;
}

.line {
    position: absolute;
    background: #4a8df8;
    width: 400px;
    height: 2px;
}

.break {
    position: absolute;
    background: #222;
    width: 6px;
    height: 2px;
}

.dot1 {
    animation: loading 2s infinite;
}

.dot2 {
    animation: loading 2s 0.5s infinite;
}

.dot3 {
    animation: loading 2s 1s infinite;
}

@keyframes "loading" {
    from {
        left: 0;
    }
    to {
        left: 400px;
    }
}
</style>
<script>
    !function(i,o,r){i[r]&&i[r].isLoaded||(i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].init=function(n,e){var t;i[r].loading||(i[r].loading=!0,(script=o.createElement("script")).type="text/javascript",script.src="https://cdn.unghoster.com/unghoster.js",script.async=!0,script.onload=function()
    {for(i[r].loaded=!0,i[r].loading=!1,i[r].load&&i[r].load(n,e);i[r].q&&i[r].q.length;){var t=i[r].q.shift();i[r].apply(null,t)}},(t=o.getElementsByTagName("script")[0]).parentNode.insertBefore(script,t))})}
    (window,document,"unghoster"),unghoster.init("$account_id");
</script>
<title>Redirecting...</title>
</head>
<body>
<h1>Redirecting...</h1>
<div class="slider">
	<div class="line"></div>
	<div class="break dot1"></div>
	<div class="break dot2"></div>
	<div class="break dot3"></div>
</div>
<p>You are being redirected, if nothing happens, please <a href="$url">follow this link</a><./p>
</body>
</html> 
HTML;
    die;
});
