<?php

namespace Tymon\JWTAuth\Providers\JWT;

use Tymon\JWTAuth\Contracts\Providers\JWT as TymonJWT;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\Key;

class Firebase extends Provider implements TymonJWT
{
    private $key;

    public function __construct(
        $secret,
        $algo,
        array $keys
    ) {
        parent::__construct($secret, $algo, $keys);

        $this->key = new Key($this->getVerificationKey(), $this->getAlgo());
    }

    public function encode(array $payload)
    {
        try {
            return JWT::encode($payload, $this->getSigningKey(), $this->getAlgo());//public static function encode($payload, $key, $alg, $keyId = null, $head = null)
        }
        catch (\Throwable $e) {
            throw new JWTException('Could not encode token: '. $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function decode($token)
    {
        try {
            return (array)JWT::decode($token, $this->key);//public static function decode($jwt, $keyOrKeyArray)
        }
        catch (ExpiredException $e) {
            throw new TokenExpiredException(ExpiredException::class . ': ' . $e->getMessage());
        } catch (BeforeValidException $e) {
            throw new TokenInvalidException(BeforeValidException::class . ': ' . $e->getMessage());
        } catch (SignatureInvalidException $e) {
            throw new TokenInvalidException(SignatureInvalidException::class . ': ' . $e->getMessage());
        } catch (\Throwable $e) {
            throw new JWTException('Could not decode token: '. $e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function isAsymmetric()
    {
        try {
            list($function, $algorithm) = JWT::$supported_algs[$this->getAlgo()];
            return $function === 'openssl';
        } catch (\Throwable $e) {
            throw new JWTException('The given algorithm could not be found', $e->getCode(), $e);
        }
    }
}
