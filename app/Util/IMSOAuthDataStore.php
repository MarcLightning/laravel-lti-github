<?php
/**
 * Created by PhpStorm.
 * User: mmestre
 * Date: 4/17/17
 * Time: 4:14 PM
 *
 * from IMSGlobal
 *
 */

namespace App\Util\IMSOAuthDataStore;

use IMSGlobal\LTI\OAuth\OAuthDataStore;
use IMSGlobal\LTI\OAuth\OAuthConsumer;
use IMSGlobal\LTI\OAuth\OAuthToken;

class IMSOAuthDataStore extends OAuthDataStore {
    private $consumer_key = NULL;
    private $consumer_secret = NULL;

    public function __construct($consumer_key, $consumer_secret) {

        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;

    }

    function lookup_consumer($consumer_key) {

        return new OAuthConsumer($this->consumer_key, $this->consumer_secret);

    }

    function lookup_token($consumer, $token_type, $token) {

        return new OAuthToken($consumer, '');

    }

    function lookup_nonce($consumer, $token, $nonce, $timestamp) {

        return FALSE;  // If a persistent store is available nonce values should be retained for a period and checked here

    }

    function new_request_token($consumer, $callback = null) {

        return NULL;

    }

    function new_access_token($token, $consumer, $verifier = null) {

        return NULL;

    }

}