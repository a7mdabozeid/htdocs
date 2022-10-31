<?php

class NextendSocialLoginPROProviderExtensionTwitter extends NextendSocialLoginPROProviderExtensionWithSyncData {

    /** @var NextendSocialProviderTwitter */
    protected $provider;

    protected function synchronizeNodeFields($user_id, $fields, $data) {

        if (isset($data['screen_name'])) {
            $data['profile_url'] = 'https://twitter.com/' . $data['screen_name'];
        }

        parent::synchronizeNodeFields($user_id, $fields, $data);
    }

    protected function getRemoteData($node = 'me') {
        if ($node === 'me') {
            return $this->provider->getMe();
        }

        return array();
    }
}