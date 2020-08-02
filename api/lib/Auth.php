<?php

use Phalcon\Http\Request;
use \Firebase\JWT\JWT;

class Auth
{
    public static function getTokenData(Phalcon\Config $config)
    {
        try {
            $headers = getallheaders();

            $auth = '';
            
            if (isset ($headers['Authorization'])) {
                $auth = $headers['Authorization'];
            } else if (isset($headers['authorization'])) {
                $auth = $headers['authorization'];
            }

            if (empty($auth)) {
                return null;
            }

            $secretKey = base64_decode($config->jwtkey);
            $jwt = explode(' ', $auth);

            $decodedToken = JWT::decode($jwt[1], $secretKey, ['HS512']);

            return $decodedToken->data ?? null;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function getUserId(string $email, string $password): int
    {
        $user = Users::findFirst(
            [
                'email = :email:',
                'bind' => [
                    'email' => $email
                ]
            ]
        );

        if ($user) {

            if (password_verify($password, $user->password) || $user->password_token === $password) {
                $user->password_token = null;

                if ($user->update()) {
                    return intval($user->id);
                }
            }
        }

        return -1;
    }

    public static function createToken(string $serverName, array $data, string $jwtKey): string
    {
        $tokenId = base64_encode(random_bytes(32));
        $issuedAt = time();
        $notBefore = $issuedAt;         // Adding 10 seconds
        $expire = $notBefore + (60 * 60 * 5); // + 5 horas

        /*
         * Create the token as an array
         */
        $rawData = [
            'iat' => $issuedAt,         // Issued at: time when the token was generated
            'jti' => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss' => $serverName,       // Issuer
            'nbf' => $notBefore,        // Not before
            'exp' => $expire,           // Expire
            'data' => $data             // Data related to the signer user
        ];

        $secretKey = base64_decode($jwtKey);

        $jwt = JWT::encode(
            $rawData,      // Data to be encoded in the JWT
            $secretKey,     // The signing key
            'HS512'         // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );

        return $jwt;
    }
}
