<?php

// If this file is called directly, abort.
if ( ! defined('WPINC')) {
    die;
}

?>

<br>

<?php settings_errors(); ?>

<h2>Is something not working quite right?</h2>

<p>If you're having difficulty getting your copy of WP Pusher working, you're in the right place. Here you can check on how all the various pieces of the plugin are working. In addition to this screen, you can enable logging in the <a href="<?php echo admin_url('admin.php?page=wppusher&tab=log'); ?>">"Logging" tab</a> and see if there are any unexpected messages in the logs there.</p>

<p>If one of the below tests fails, copy/paste the below output and send it to <a href="mailto:hi@wppusher.com">hi@wppusher.com</a> and we'll be able to start debugging your problem more quickly.</p>

<h2>Active License Key</h2>
<p>This test determines whether you have registered a license key with WP Pusher and it is currently activated in your installation.</p>

<?php $key = get_option('wppusher_license_key', false); ?>

<?php if( $key ) : ?>
    <strong style="color: green;">Status: Passed.</strong> You're all good!
<?php else : ?>
    <strong style="color: red;">Status: Failed.</strong> It doesn't look like you have a license key registered with this installation of WordPress. Do you need to <a href="https://dashboard.wppusher.com/checkout?plan=price_1JO1NpEJPTdj0nOdAMsphyOy" target="_blank">buy one</a> or <a href="https://dashboard.wppusher.com/dashboard" target="_blank">look up your existing license key</a>?
<?php endif; ?>

<h2>cURL Information</h2>
<p>This test determines what version of cURL your server is running. To check the age of your current cURL version as well as to check what the latest version is, <a href="https://curl.se/docs/releases.html" target="_blank">take a look here.</a> If you are running an older version of cURL, you may have issues connecting to the WP Pusher server and getting your license key activated. If this is the case, you may want to ask your web host if they can upgrade the version of cURL that your server is running.</p>

<?php $version = curl_version(); ?>

<strong><?php if( $version['age'] >= 2 ) : ?><strong style="color: red;">WARNING</strong><?php endif; ?> Your version of cURL is about <?php echo $version['age']; ?> years old.</strong>
<br>
<strong>cuRL Version:</strong> <?php echo $version['version']; ?>
<br>
<strong>SSL Version:</strong> <?php echo $version['ssl_version']; ?>


<h2>License Server Connection Test</h2>
<p>This test determines whether your installation of WordPress can connect to our license server.</p>

<?php
    $baseUrl = 'https://dashboard.wppusher.com/ping/';

    $result = wp_remote_get($baseUrl, array('headers' => 'Accept: application/json'));

    $code = wp_remote_retrieve_response_code( $result );
    $response = wp_remote_retrieve_body( $result );
    $headers = (array)wp_remote_retrieve_headers( $result );

    if( 'Pong!' === $response ) : ?>
        <strong style="color: green;">Status: Passed.</strong> You're all good!
    <?php else : ?>
        <strong style="color: red;">Status: Failed.</strong> It looks like something is wrong. Below is some more debugging information that may be helpful:

        <p><strong>Status Code</strong>: <?php echo $code; ?></p>
        <p><strong>Response</strong>: <pre><?php echo $response; ?></pre>
        <p><strong>Headers</strong>: <pre><?php print_r( $headers ); ?></pre>
    <?php endif; ?>

<h2>GitHub Connection Test</h2>
<p>This test checks whether you have connected your GitHub account and can deploy themes/plugins from there.</p>

<?php 
    $token = get_option('gh_token');

    $response = wp_remote_get("https://api.github.com/user/emails", array(
        'headers' => array(
            'Accept' => 'application/vnd.github.v3+json',
            'Content-Type' => 'application/json',
            'Authorization' => "token {$token}",
        ),
    ));

    $response = json_decode( wp_remote_retrieve_body( $response ) );

    if( ! $token ) : ?>
        <strong style="color: red;">Status: Failed.</strong> It looks like you don't have a GitHub token stored. If you are trying to use GitHub, <a href="<?php echo admin_url('admin.php?page=wppusher&tab=github'); ?>">generate a GitHub token here</a>.
    <?php elseif( is_object( $response ) && 'Bad credentials' === $response->message ) : ?>
        <strong style="color: red;">Status: Failed.</strong> It looks like you GitHub token isn't valid. Try and <a href="<?php echo admin_url('admin.php?page=wppusher&tab=github'); ?>">regenerate one here</a> and make sure you have pasted it into the GitHub token field.
    <?php else : ?>
        <strong style="color: green;">Status: Passed.</strong> You're all good!
    <?php endif; ?>

<h2>BitBucket Connection Test</h2>
<p>This test checks whether you have connected your BitBucket account and can deploy themes/plugins from there.</p>

<?php  
    $token = get_option('bb_token');

    $response = wp_remote_get("https://cloud.wppusher.com/auth/bitbucket/refresh-token?refresh_token={$token}");

    if (is_wp_error($response) or empty($response)) {
        // Something went wrong
        die("Could not get access token from BB");
    }

    $json = json_decode($response['body'], true);

    if ( ! isset($json['access_token'])) {
        // Something went wrong
        die("Could not get JSON access token from BB");
    }

    $access_token = $json['access_token'];

    $url = "https://api.bitbucket.org/2.0/user";

    $response = wp_remote_get($url, array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$access_token}",
        ),
    ));

    $code = wp_remote_retrieve_response_code( $response );
    $response_body = wp_remote_retrieve_body( $response );
    $headers = (array)wp_remote_retrieve_headers( $response );

    if( ! $token ) : ?>
        <strong style="color: red;">Status: Failed.</strong> It looks like you don't have a Bitbucket token stored. If you are trying to use Bitbucket, <a href="<?php echo admin_url('admin.php?page=wppusher&tab=bitbucket'); ?>">generate a Bitbucket token here</a>.
    <?php elseif( 200 !== $code ) : ?>
        <strong style="color: red;">Status: Failed.</strong> Something is wrong with your connection to BitBucket. Details of your authentication attempt are below:

        <p><strong>Status Code</strong>: <?php echo $code; ?></p>
        <p><strong>Response</strong>: <pre><?php echo $response_body; ?></pre>
        <p><strong>Headers</strong>: <pre><?php print_r( $headers ); ?></pre>
    <?php else: ?>
        <strong style="color: green;">Status: Passed.</strong> You're all good!
    <?php endif; ?>

<h2>GitLab Connection Test</h2>
<p>This test checks whether you have connected your GitLab account and can deploy themes/plugins from there.</p>

<?php
    $baseUrl = get_option('gl_base_url');

    if( ! $baseUrl ) : ?>
        <strong style="color: red;">Status: Failed.</strong> No GitLab URL stored. If you're trying to use GitLab, <a href="<?php echo admin_url('admin.php?page=wppusher&tab=gitlab'); ?>">add your Gitlab information here.</a>
    <?php endif; ?>

    <?php $token = get_option('gl_private_token');

    $apiURL = "{$baseUrl}/api/v4/users?private_token={$token}";

    $response = wp_remote_get($apiURL, array(
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
    ));

    $code = wp_remote_retrieve_response_code( $response );
    $response_body = wp_remote_retrieve_body( $response );
    $headers = (array)wp_remote_retrieve_headers( $response );

    if ( ! $token ) : ?>
        <strong style="color: red;">Status: Failed.</strong> No GitLab token stored. If you're trying to use a private GitLab repository, <a href="<?php echo admin_url('admin.php?page=wppusher&tab=gitlab'); ?>">add your Gitlab information here.</a>
    <?php elseif( 200 !== $code ) : ?>
        <strong style="color: red;">Status: Failed.</strong> Something is wrong with your connection to Gitlab. Details of your authentication attempt are below:

        <p><strong>Status Code</strong>: <?php echo $code; ?></p>
        <p><strong>Response</strong>: <pre><?php echo $response_body; ?></pre>
        <p><strong>Headers</strong>: <pre><?php print_r( $headers ); ?></pre>
    <?php else: ?>
        <strong style="color: green;">Status: Passed.</strong> You're all good!
    <?php endif; ?>
