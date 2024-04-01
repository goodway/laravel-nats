<?php

namespace Goodway\LaravelNats\DTO;

use Goodway\LaravelNats\Helpers\ArrayHelper;
use Illuminate\Support\Str;

class NatsClientConfiguration
{
    use ArrayHelper;

    public function __construct(
        public string           $host = 'localhost',
        public int              $port = 4222,
        protected ?string       $user = null,
        protected ?string       $password = null,
        protected ?string       $token = null,
        protected ?string       $nkey = null,
        protected ?string       $jwt = null,
        protected ?string       $sslKey = null,
        protected ?string       $sslCert = null,
        protected ?string       $sslCa = null,
        protected bool          $reconnect = true,
        protected float         $connectionTimeout = 1,
        protected bool          $verboseMode = true,
        protected string        $inboxPrefix = '_INBOX',
        protected int           $pingInterval = 2,
    ) {}


    /**
     * Set tls cert files to provide TLS Authentication
     * @param string|null $tlsKeyFile
     * @param string|null $tlsCertFile
     * @param string|null $tlsCAFile
     * @return $this
     */
    public function withTls(
        ?string $tlsKeyFile = null,
        ?string $tlsCertFile = null,
        ?string $tlsCAFile = null
    ): static
    {
        $this->sslKey = $tlsKeyFile;
        $this->sslCert = $tlsCertFile;
        $this->sslCa = $tlsCAFile;
        return $this;
    }

    /**
     * Sets NKey to provide NKey Authentication
     * @param string|null $nKey
     * @return $this
     */
    public function withNKey(?string $nKey = null): static
    {
        $this->nkey = $nKey;
        return $this;
    }

    /**
     * Sets a connection timeout in seconds
     * @param float $connectionTimeout
     * @return $this
     */
    public function timeout(float $connectionTimeout = 1): static
    {
        $this->connectionTimeout = $connectionTimeout;
        return $this;
    }

    /**
     * Converts current configuration to nats configuration attributes
     * @return array
     */
    public function provideNats(): array
    {
        return [
            'host'          => $this->host,
            'port'          => $this->port,
            'user'          => $this->user,
            'pass'          => $this->password,
            'token'         => $this->token,
            'nkey'          => $this->nkey,
            'jwt'           => $this->jwt,
            'tlsKeyFile'    => $this->sslKey,
            'tlsCertFile'   => $this->sslCert,
            'tlsCaFile'     => $this->sslCa,
            'reconnect'     => $this->reconnect,
            'timeout'       => $this->connectionTimeout,
            'pingInterval'  => $this->pingInterval,
            'verbose'       => $this->verboseMode,
            'inboxPrefix'   => $this->inboxPrefix,
        ];
    }

    /**
     * Make new configuration from array
     * @param array $configuration
     * @return NatsClientConfiguration
     */
    public static function fromArray(array $configuration): NatsClientConfiguration
    {
        $attributes = self::transformKeys($configuration, Str::camel(...));
        return new self(...$attributes);
    }

    /**
     * Converts configuration object to array
     * @return array
     */
    public function toArray(): array
    {
        return self::transformKeys($this, Str::snake(...));
    }

}
