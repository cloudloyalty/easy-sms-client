<?php

declare(strict_types=1);

namespace CloudLoyalty\EasySmsClient\Model;

class SendSmsRequest extends BaseOptions
{
    /**
     * @var string
     */
    private $phones;

    /**
     * @var string
     */
    private $mes;

    /**
     * @var string
     */
    private $id;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        if (isset($options['phone'])) {
            $this->setPhone($options['phone']);
        }
        if (isset($options['message'])) {
            $this->setMessage($options['message']);
        }
        if (isset($options['id'])) {
            $this->setId($options['id']);
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

        return array_merge(
            parent::asHttpQueryParams(),
            $params
        );
    }

    public function setPhone(string $phone): self
    {
        $this->phones = $phone;

        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->mes = $message;

        return $this;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }
}
