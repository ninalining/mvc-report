<?php

namespace App\Card;

class Card
{
    private string $suit;
    private string $value;

    public function __construct(string $suit, string $value)
    {
        $this->suit = $suit;
        $this->value = $value;
    }

    public function getSuit(): string
    {
        return $this->suit;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return "{$this->value}{$this->suit}";
    }

    public function __get(string $name)
    {
        if ($name === 'suit') {
            return $this->suit;
        }

        if ($name === 'value') {
            return $this->value;
        }

        throw new \Exception("Property {$name} does not exist");
    }
}
