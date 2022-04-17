<?php namespace Jasn\Geohome;

class Geohome {
        private $access_token = false;
    private $base_url     = 'https://api.geotogether.com/';
    private $devices      = [];
    private $password     = '';
    private $username     = '';

    public function __construct($username, $password)
    {
        $this->password = $password;
        $this->username = $username;
    }
    public function live()
    {
        if (!$this->init_api()) {
            return false;
        }
        $data = [];
        if ($this->devices) {
            foreach ($this->devices as $device) {
                $data[] = $this->api('api/userapi/system/smets2-live-data/'.$device);
            }
        }
        if (count($data) === 1) {
            return $data[0];
        }
        return $data;
    }
    public function periodic()
    {
        return $this->api('api/userapi/system/smets2-periodic-data/');
    }

    private function api($uri)
    {
        $response = curl(
            $this->base_url.$uri,
            'GET',
            null,
            array(
                "Accept: application/json",
                "Authorization: Bearer ".$this->access_token,
                "Content-Type: application/json",
            )
        );
        if ($response !== false) {
            return json_decode($response);
        } else {
            return false;
        }
    }
    private function init_api()
    {
        if (!$this->access_token) {
            $response = curl(
                $this->base_url.'usersservice/v2/login',
                'POST',
                [
                    'identity' => $this->username,
                    'password' => $this->password,
                ],
                array(
                    "Accept: application/json",
                    "Content-Type: application/json",
                )
            );
            if ($response !== false) {
                $response = json_decode($response);
                if (!$response->validated) {
                    return false;
                }
                $this->access_token = $response->accessToken;
            } else {
                return false;
            }
        }
        if (!$this->devices) {
            $response = curl(
                $this->base_url.'api/userapi/v2/user/detail-systems?systemDetails=true',
                'GET',
                null,
                array(
                    "Accept: application/json",
                    "Authorization: Bearer ".$this->access_token,
                    "Content-Type: application/json",
                )
            );
            if ($response !== false) {
                $response = json_decode($response);
                if ($response->systemRoles) {
                    foreach ($response->systemRoles as $role) {
                        $this->devices[] = $role->systemId;
                    }
                }
            } else {
                return false;
            }
        }
        return true;
    }
}
