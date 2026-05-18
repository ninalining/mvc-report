<?php

namespace App\Tests\Card;

use App\Card\CardGraphic;
use PHPUnit\Framework\TestCase;

class CardGraphicTest extends TestCase
{
    public function testGetColorRed(): void
    {
        $card = new CardGraphic('♥', 'K');
        $this->assertSame('red', $card->getColor());

        $card2 = new CardGraphic('♦', '5');
        $this->assertSame('red', $card2->getColor());
    }

    public function testGetColorBlack(): void
    {
        $card = new CardGraphic('♠', 'A');
        $this->assertSame('black', $card->getColor());

        $card2 = new CardGraphic('♣', '9');
        $this->assertSame('black', $card2->getColor());
    }

    public function testGetUnicodeSymbol(): void
    {
        $card = new CardGraphic('♠', 'A');
        $this->assertSame('A♠', $card->getUnicodeSymbol());
    }

    public function testGetCssClassRed(): void
    {
        $card = new CardGraphic('♥', '3');
        $this->assertSame('playing-card red', $card->getCssClass());
    }

    public function testGetCssClassBlack(): void
    {
        $card = new CardGraphic('♠', '3');
        $this->assertSame('playing-card', $card->getCssClass());
    }

    public function testGetLabel(): void
    {
        $card = new CardGraphic('♦', 'Q');
        $this->assertSame('Q♦', $card->getLabel());
    }

    public function testToArray(): void
    {
        $card = new CardGraphic('♣', '10');
        $result = $card->toArray();

        $this->assertSame('10♣', $result['label']);
        $this->assertSame('playing-card', $result['cssClass']);
        $this->assertSame('♣', $result['suit']);
        $this->assertSame('10', $result['value']);
    }
}
