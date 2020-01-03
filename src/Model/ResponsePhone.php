<?php

declare(strict_types=1);

namespace CloudLoyalty\EasySmsClient\Model;

use JMS\Serializer\Annotation as JMS;

class ResponsePhone
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $phone;

    /**
     * @var float
     * @JMS\Type("float")
     */
    public $cost;

    /**
     * @var int
     * @JMS\Type("int")
     */
    public $status;
}
