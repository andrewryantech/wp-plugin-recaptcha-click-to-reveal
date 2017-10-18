<?php
/**
 * Author: andy@modernwebservices.com.au
 * Created: 1/10/17 5:12 PM
 */
declare(strict_types=1);

namespace ModernWebServices\Plugins\ClickToReveal\Admin;

class ProtectedItem
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;


    /**
     * @param string $name
     * @param string $value
     */
    public function __construct(string $name, string $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
