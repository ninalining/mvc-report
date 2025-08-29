<?php

namespace App\Card;

class CardGraphic extends Card
{
    public function getColor(): string
    {
        
        if ($this->getSuit() === '♥' || $this->getSuit() === '♦') {
            return 'red';
        }
        return 'black';
    }

    public function getUnicodeSymbol(): string
    {
        return $this->getValue() . $this->getSuit();
    }
}
