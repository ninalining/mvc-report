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
    public function getCssClass(): string
    {
        return 'playing-card' . ($this->getColor() === 'red' ? ' red' : '');
    }

    public function getLabel(): string
    {
        return $this->getUnicodeSymbol();
    }

    public function toArray(): array
    {
        return [
            'label' => $this->getLabel(),
            'cssClass' => $this->getCssClass(),
            'suit' => $this->getSuit(),
            'value' => $this->getValue(),
        ];
    }
}
