<?php

declare(strict_types=1);

namespace CloudLoyalty\EasySmsClient\Model;

class BaseOptions
{
    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $psw;

    /**
     * @var string
     */
    private $fmt;

    /**
     * @var string
     */
    private $charset;

    /**
     * @var string
     */
    private $cost;

    /**
     * @var string
     */
    private $err;

    /**
     * @var string
     */
    private $all;

    /**
     * @var string
     */
    private $sender;

    /**
     * @var string
     */
    private $connectId;

    public function __construct(array $options = [])
    {
        if (isset($options['login'])) {
            $this->setLogin($options['login']);
        }
        if (isset($options['password'])) {
            $this->setPassword($options['password']);
        }
        if (isset($options['fmt'])) {
            $this->setFmt($options['fmt']);
        }
        if (isset($options['charset'])) {
            $this->setCharset($options['charset']);
        }
        if (isset($options['returnCost'])) {
            $this->setReturnCost($options['returnCost']);
        }
        if (isset($options['err'])) {
            $this->setErr($options['err']);
        }
        if (isset($options['all'])) {
            $this->setAll($options['all']);
        }
        if (isset($options['sender'])) {
            $this->setSender($options['sender']);
        }
        if (isset($options['connectId'])) {
            $this->setConnectId($options['connectId']);
        }
    }

    public function asHttpQueryParams(): array
    {
        $params = array_filter(
            get_object_vars($this),
            function ($value) {
                return $value !== null;
            }
        );

        unset($params['connectId']);

        return $params;
    }

    /**
     * @return $this
     */
    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->psw = $password;

        return $this;
    }

    /**
     * @return $this
     */
    public function setFmt(string $fmt): self
    {
        $this->fmt = $fmt;

        return $this;
    }

    /**
     * @return $this
     */
    public function setCharset(string $charset): self
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @return $this
     */
    public function setReturnCost(string $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * @return $this
     */
    public function setErr(string $err): self
    {
        $this->err = $err;

        return $this;
    }

    /**
     * @return $this
     */
    public function setAll(string $all): self
    {
        $this->all = $all;

        return $this;
    }

    /**
     * @return $this
     */
    public function setSender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return $this
     */
    public function setConnectId(string $connectId): self
    {
        $this->connectId = $connectId;

        return $this;
    }

    public function getConnectId(): ?string
    {
        return $this->connectId;
    }
}
