<?php

declare(strict_types=1);

namespace Kata\Tests;

use Kata\ChristmasLightGrid;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ChristmasLightGridTest extends TestCase
{
    // test per una sola lampadina, in una griglia monodimensionale
    public function testShouldLightTurnedOn(): void
    {
        $grid = new ChristmasLightGrid(1);

        self::assertSame($grid->lightStatus(), false);

        $grid->turnOn();

        self::assertSame($grid->lightStatus(), true);
    }

    // test per due lampadine, una accanto all'altra, in una griglia monodimensionale
    public function testShouldTwoLightsTurnedOn(): void
    {
        $grid = new ChristmasLightGrid(2);

        self::assertSame($grid->lightStatus(0), false);
        self::assertSame($grid->lightStatus(1), false);

        $grid->turnOn(0);
        $grid->turnOn(1);

        self::assertSame($grid->lightStatus(0), true);
        self::assertSame($grid->lightStatus(1), true);
    }

    // test per tre lampadine, anche se non sono una accanto all'altra, in una griglia monodimensionale
    public function testShouldThreeLightsTurnedOn(): void
    {
        $grid = new ChristmasLightGrid(10);

        self::assertSame($grid->countTurnedOnLights(), 0);

        $grid->turnOn(2);
        $grid->turnOn(3);
        $grid->turnOn(4);

        self::assertSame($grid->countTurnedOnLights(), 3);
    }

    // test per varie aggruppazioni di lampadine, in una griglia monodimensionale
    public function testShouldRangeTurnedOnLights(): void
    {
        $grid = new ChristmasLightGrid(20);

        self::assertSame($grid->countTurnedOnLights(), 0);

        $grid->turnOn(2,6);
        $grid->turnOn(9,15);

        self::assertSame($grid->countTurnedOnLights(), 12);

        self::assertSame(
            $grid->lightStatus(2,15),
            [true,true,true,true,true,false,false,true,true,true,true,true,true,true]
        );
    }

    // test per una aggruppazione rettangolare, in una giglia bidimensionale, mediante trasformazione a una griglia monodimensionale
    public function testShouldGridTurnedOnLights(): void
    {
        $grid = new ChristmasLightGrid([4,5]); // equivale a 20 lampadine

        self::assertSame($grid->countTurnedOnLights(), 0);

        $grid->turnOnBidimensional([2,2],[3,3]);

        self::assertSame(
                    $grid->lightStatus(0,19),
                    [false,false,false,false,false,false,false,false,false,false,true,true,false,false,true,true,false,false,false,false]
                );

        self::assertSame($grid->countTurnedOnLights(), 4);
/*


        // TODO limitare, qua sotto, l'accensione delle lampadine a un rettangolo anzi che tutta la righa
        // array passati come argomenti - notazione …[] -
        // @see https://stackoverflow.com/questions/744145/passing-an-array-as-arguments-not-an-array-in-php
        // $grid->turnOn($grid->convertBiToMonoIndex(...[2,2]), $grid->convertBiToMonoIndex(...[3,3]));

        // qua sotto non passo un array come argomento
        // qua sotto passo l'indice monodimensionale
        $grid->turnOn($grid->convertBiToMonoIndex(2,2),$grid->convertBiToMonoIndex(2,3));
        $grid->turnOn($grid->convertBiToMonoIndex(3,2),$grid->convertBiToMonoIndex(3,3));

        self::assertSame($grid->countTurnedOnLights(), 4);

        // TODO convertire, qua sotto, l'indice bidimensionale [2,2] a monodimiensionale
        // TODO convertire, qua sotto, l'indice bidimensionale [3,3] a monodimiensionale
        // TODO limitare, qua sotto, l'accensione delle lampadine a un rettangolo anzi che tutta la righa
        self::assertSame(
            $grid->lightStatus([2,2],[3,3]),
            [
                [true,true], // TODO convertire, qua accanto, l'array bidimensionale a monodimensionale
                [true,true]
            ]
        );
*/
    }


    // Test parametrici da usare la prossima volta
    #[DataProvider('cial')]
    public function testRun(string $input, string $expected): void
    {
        $grid = new ChristmasLightGrid(10);

        self::assertSame($expected, $input);
    }

    // Parametri del test parametrico
    /**
     * @return array<string, list{string, string}>
     */
    public static function cial(): array
    {
        return [
            'returns same input' => ['hello', 'hello'],
        ];
    }
}
