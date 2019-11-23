<?php

use Phalcon\Http\Request;
use \Firebase\JWT\JWT;

class Auth
{
    public static function validateRequest (Request $request, string $jwtKey): bool
    {
        try {
            $headers = getallheaders();

            if (!isset($headers['Authorization']) || empty($headers['Authorization'])) {
                return false;
            }
            $secretKey = base64_decode($jwtKey);
            $jwt = explode(" ", $headers['Authorization']);
            $decodedToken = JWT::decode($jwt[1], $secretKey, array('HS512'));

            if (isset($decodedToken)) {
                if ($decodedToken->data->id > -1 && $decodedToken->data->id !== null) {
                    return true;
                }
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    public static function getUserId (string $email, string $password): int
    {
        try {
            $user = Users::findFirst(
                [
                    'email = :email:',
                    'bind' => [
                        'email' => $email
                    ]
                ]
            );

            if ($user !== false) {

                if (password_verify($password, $user->password) || $user->password_token === $password) {
                    $user->password_token = null;

                    if ($user->update() !== false) {
                        return intval($user->id);
                    } else {
                        var_dump(Helpers::getErrors($user));
                    }
                }
            }

        } catch (Exception $e) {
             var_dump(Message::exception($e));
        }

        return -1;
    }

    public static function createToken (string $serverName, array $data, string $jwtKey): string
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

    public static function getUserData (Phalcon\Config $config)
    {
        $headers = getallheaders();
        if(!isset($headers["Authorization"]) || empty($headers["Authorization"])) {
            return null;
        }
        $secretKey = base64_decode($config->jwtkey);
        $jwt = explode(" ", $headers["Authorization"]);
        $decoded_token = JWT::decode($jwt[1], $secretKey, array('HS512'));
        return $decoded_token->data;
    }

    // public static function getUserRol (int $userId): string
    // {
    //     $userRol = '';
    //     if ($userId > 0) {
    //         $grants = SysGrants::find(['user_id = ' . $userId]);
    //         if ($grants->count()) {
    //             foreach ($grants as $g) {
    //                 $rol = SysRoles::findFirst($g->role_id);
    //                 if ($rol !== false) {
    //                     $userRol = $rol->name;
    //                 }
    //             }
    //         }
    //     }
    //     return $userRol;
    // }

    //aux obtener token descifrado
    // public static function getTokenData (Phalcon\Config $config)
    // {
    //     $headers = getallheaders();
    //     if(!isset($headers["Authorization"]) || empty($headers["Authorization"])) {
    //         return null;
    //     }
    //     $secretKey = base64_decode($config->jwtkey);
    //     $jwt = explode(" ", $headers["Authorization"]);
    //     $decoded_token = JWT::decode($jwt[1], $secretKey, array('HS512'));
    //     return $decoded_token;
    // }
}
