<?php

declare(strict_types=1);

namespace CloudLoyalty\EasySmsClient\Model;

use JMS\Serializer\Annotation as JMS;

class SendSmsResponse
{
    /**
     * SMS identifier from request.
     *
     * @var string
     * @JMS\Type("string")
     */
    public $id;

    /**
     * Number of sent SMSs (in case of large text).
     *
     * @var int
     * @JMS\Type("int")
     */
    public $cnt;

    /**
     * Price of SMS (if requested by "cost" parameter).
     *
     * @var float
     * @JMS\Type("float")
     */
    public $cost;

    /**
     * @var string
     * @JMS\Type("string")
     */
    public $balance;

    /**
     * @var ResponsePhone[]
     * @JMS\Type("array<CloudLoyalty\EasySmsClient\Model\ResponsePhone>")
     */
    public $phones;

    /**
     * @var string
     * @JMS\Type("string")
     */
    public $error;

    /**
     * @var int
     * @JMS\Type("int")
     */
    public $error_code;
}
