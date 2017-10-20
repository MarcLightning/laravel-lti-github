<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Util\IMSOAuthDataStore\IMSOAuthDataStore;
use IMSGlobal\LTI\OAuth\OAuthServer;
use IMSGlobal\LTI\OAuth\OAuthSignatureMethod_HMAC_SHA1;
use IMSGlobal\LTI\OAuth\OAuthRequest;
use IMSGlobal\LTI\OAuth\OAuthException;

function pre($stuff) {
    echo '<pre>';
    print_r($stuff);
    echo '</pre>';
}

class Lti_launch extends Controller
{
    public $message = false;
    public $ok = true;

    // constructor including secret validation via OAuth
    function __construct()
    {
        $tool_consumer_secrets['key'] = 'secret';

        // Check it is a POST request
        $this->ok = $this->ok && $_SERVER['REQUEST_METHOD'] === 'POST';

        // Check the LTI message type
        $this->ok = $this->ok && isset($_POST['lti_message_type']) && ($_POST['lti_message_type'] === 'basic-lti-launch-request');

        // Check the LTI version
        $this->ok = $this->ok && isset($_POST['lti_version']) && ($_POST['lti_version'] === 'LTI-1p0');

        // Check a consumer key exists
        $this->ok = $this->ok && !empty($_POST['oauth_consumer_key']);

        // Check a resource link ID exists
        $this->ok = $this->ok && !empty($_POST['resource_link_id']);

        // Check the consumer key is recognised
        $this->ok = $this->ok && array_key_exists($_POST['oauth_consumer_key'], $tool_consumer_secrets);

        // If all checks have passed, validate signature with OAuth
        if ($this->ok) {
            try {
                $store = new IMSOAuthDataStore($_POST['oauth_consumer_key'], $tool_consumer_secrets['key']);
                $server = new OAuthServer($store);
                $method = new OAuthSignatureMethod_HMAC_SHA1();
                $server->add_signature_method($method);
                $request = OAuthRequest::from_request();
                $server->verify_request($request);
                $this->ok = true;
            } catch (OAuthException $e) {
                $this->ok = false;
                $this->message = $e->getMessage();
            }
        }
    }

    function launch()
    {
        // if lti is valid, launch
        if ($this->ok) {
            echo 'LTI launched successfully';
            echo '<br />';
            pre($_POST);
        } else {
            die ('LTI launch failed. Contact itg@brown.edu for assistance');
        }
    }
}