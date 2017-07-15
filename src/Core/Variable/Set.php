<?php


namespace LastCall\Mannequin\Core\Variable;


class Set
{

    private $name;

    private $description = '';

    private $values;


    public function __construct(
        string $name,
        array $values = [],
        $description = ''
    ) {
        $this->name = $name;
        $this->values = $values;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function has(string $name)
    {
        return isset($this->values[$name]);
    }

    public function get(string $name)
    {
        return $this->values[$name];
    }
}