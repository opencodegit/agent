<?php

/* Christmas lights kata
Kata aimed at learning how Test Driven Development (TDD) works
@see Kata https://kata-log.rocks/christmas-lights-kata

Credits
Inspired by Advent of Code
@see Advent of Code https://adventofcode.com/2015/day/6

**************

TEXT

Part 1
Probably a Fire Hazard

Because your neighbors keep defeating you in the holiday house decorating contest year after year, you’ve decided to deploy one million lights in a 1000x1000 grid. Furthermore, because you’ve been especially nice this year, Santa has mailed you instructions on how to display the ideal lighting configuration.Lights in your grid are numbered from 0 to 999 in each direction; the lights at each corner are at 0,0, 0,999, 999,999, and 999,0. The instructions include whether to turn on, turn off, or toggle various inclusive ranges given as coordinate pairs. Each coordinate pair represents opposite corners of a rectangle, inclusive; a coordinate pair like 0,0 through 2,2 therefore refers to 9 lights in a 3x3 square. The lights all start turned off. To defeat your neighbors this year, all you have to do is set up your lights by doing the instructions Santa sent you in order.

Examples
turn on 0,0 through 999,999 would turn on (or leave on) every light.
toggle 0,0 through 999,0 would toggle the first line of 1000 lights, turning off the ones that were on, and turning on the ones that were off.
turn off 499,499 through 500,500 would turn off (or leave off) the middle four lights.
Instructions

turn on 887,9 through 959,629
turn on 454,398 through 844,448
turn off 539,243 through 559,965
turn off 370,819 through 676,868
turn off 145,40 through 370,997
turn off 301,3 through 808,453
turn on 351,678 through 951,908
toggle 720,196 through 897,994
toggle 831,394 through 904,860

After following the instructions, how many lights are lit?

****************

Part 2

You just finish implementing your winning light pattern when you realize you mistranslated Santa’s message from Ancient Nordic Elvish. The light grid you bought actually has individual brightness controls; each light can have a brightness of zero or more. The lights all start at zero.

The phrase turn on actually means that you should increase the brightness of those lights by 1.
The phrase turn off actually means that you should decrease the brightness of those lights by 1, to a minimum of zero.
The phrase toggle actually means that you should increase the brightness of those lights by 2.
What is the total brightness of all lights combined after following Santa’s instructions?

For example:

turn on 0,0 through 0,0 would increase the total brightness by 1.
toggle 0,0 through 999,999 would increase the total brightness by 2000000.

END OF TEXT
*/

declare(strict_types=1);

namespace Kata;

final class ChristmasLightGrid
{
    // CAMPI

    // Array che rappresenta una griglia di luci
    private $lightGrid = array();
    private $width;
    private $height;

    // COSTRUTTORI

    /* Costruisce una griglia monodimensionale oppure bidimensionale in base al tipo della dimensione passata come argomento
       Se $dimension e' un intero, costruisce una griglia monodimensionale di lunghezza pari a (int $dimension)
       Se $dimension e' un array, comunque costruisce una griglia monodimensionale, ma di una lunghezza pari al prodotto di ($dimension[0] * $dimension[1])
    @param $dimension       Rappresenta la dimensione della griglia monodimensionale (int $dimension) oppure la dimensione della griglia bidiemensionale (array $dimension)
    */
    function __construct(int | array $dimension) {
        // griglia monodimensionale con argomento monodimensionale
        if (is_int($dimension)) {
            $this->width = $dimension;
            for ($i = 0 ; $i < $dimension ; $i++) {
                $this->lightGrid[] = false;
            }
        }
        // griglia monodimensionale con argomenti bidimensionali
        else {
            $this->width = $dimension[0]; // ad esempio, $this->width = 4
            $this->height = $dimension[1]; // ad esempio, $this->height = 5
            for ($i = 0 ; $i < ($this->width * $this->height) ; $i++) { // ad esempio, $this->width * $this->height - 1 = 19
                $this->lightGrid[] = false;
            }
        }
    }

    // METODI

    /* Accende la lampadina che si trovano tra le due coordinate monodimensionali passate come argomento
       Se il secondo parametro e' null, accende la sola lampadina che si trova nella prima coordinata monodimensionale
    @param $coordinate1     Rappresenta la coordinata monodimensionale inferiore nel rango di lampadina che si vogliono accendere
    @param $coordinate2     Rappresenta la coordinata monodimensionale superiore nel rango di lampadine che si vogliono accendere
    */
    public function turnOn (int $coordinate1 = 0, ?int $coordinate2 = null): void
    {
        if ($coordinate2 == null) {
            $this->lightGrid[$coordinate1] = true;
        }
        else {
            for ($i = $coordinate1 ; $i <= $coordinate2 ; $i++) {
                $this->lightGrid[$i] = true;
            }
        }
    }

    /* Accende le luci che si trova nella coordinata passata come parametro
    @param $coordinate      Rappresenta la coordinata della luce che si vuole accendere
    */
    public function turnOnBidimensional ($leftCornerArray, $rightCornerArray): void
    {
        \Psy\debug(get_defined_vars(), $this);
        for ($i = $leftCornerArray[0] ; $i <= $rightCornerArray[0]  ; $i++) {
            for ($j = $leftCornerArray[1] ; $j <= $rightCornerArray[1] ; $j++) {
                $this->lightGrid[$this->convertBiToMonoIndex([$i,$j])] = true;
            }
        }
    }

    /* Verifica se la luce passata come argomento è accesa o spenta.
    @param $coordinate      Rappresenta la coordinata della luce che si vuole verificare
    @return                 Restituisce "true" se la luce che si trova nella coordinata passata come parametro è accesa
    */
    /*
    TODO Capire se il parametro è di tipo array o integer
    TODO Convertire la coordinata da bidimensionale a monodimensionale. Esempio
    [
        [1,2,3]
        [4,5,6]         ==       [1,2,3,4,5,6,7,8,9]
        [7,8,9]    (zero index)   0 1 2 3 4 5 6 7 8
    ]
    coordinata bidimensionale
    [1,1] diventa 3x+y     = 4 (zero index)
    [0,1] diventa 3x+y     = 1 (zero index)
    [x,y] diventa w*x + y
    dove
    w = width
    x = row
    y = column

    w*x + y =
    [1,1] -> 3*1 + 1 = 4
    [0,1] -> 3*0 + 1 = 1
    [2,0] -> 3*2 + 0 = 6

    */

    /* Verifica quali luci della griglia sono accese nel rango specificato dai parametri: da $coordinate1 fino a $coordinate2
    @param $coordinate1     Rappresenta la coordinata iniziale del rango da verificare.
                            - Se $coordinate1 non e' specificata, assegna per difetto il valore di 0 (zero)
    @param $coordinate2     Rappresenta la coordinata finale del rango da verificare
                            - $coordinate2 e' opzionale
    @return                 Restituisce un booleano, oppure un array di booleani, che entrambi indicano lo stato della lampadina oppure delle lampadine
                            - Se $coordinate2 non e' specificata, restituisce un unico booleano (true/false)
                            - Se $coordinate2 e' specificata, restituisce un array di booleani dove ogni booleano rappresenta lo stato di una lampadina
    */
    public function lightStatus (int $coordinate1 = 0, ?int $coordinate2 = null): bool | array
    {
        if ($coordinate2 == null) {
            return $this->lightGrid[$coordinate1];
        }
        $temp = array();
        for ($i = $coordinate1 ; $i <= $coordinate2 ; $i++) {
            $temp[] = $this->lightGrid[$i];
        }
        return $temp;
    }

    /* Conta il numero di luci accese nella griglia
    @return         Restituisce un numero intero che rappresenta la quantita' di luci accese nella griglia
    */
    public function countTurnedOnLights (): int
        {
            $cont = 0;
            for ($i = 0 ; $i < count($this->lightGrid) ; $i++) {
                if ($this->lightGrid[$i] == true) {
                    $cont++;
                }
            }
            return $cont;
        }

    /* Converte un indice bidimensionale [x,y] in un indice monodimensionale [z], in base a la lunghezza orizontale della griglia
    @return         Restituisce un intero che rappresenta l'indice monodimensionale [z] corrispondente a un indice bidimensionale [x,y] della griglia
    */
    public function convertBiToMonoIndex ($array): int
        {
            // dimensione orizontale della griglia
            $maxHorizontalLength = $this->width;

            // controllo che i parametro passato come argomento non ecceda la dimensione orizontale della griglia
            if ($array[1] > $maxHorizontalLength) {
                return -1;
            }

            // formula per convertire l'indice bidimensionale [x,y] a un indice monodimensionale [z]
            // z = ()$maxHorizontalLength * x) + y
            return $maxHorizontalLength * $array[0] + $array[1];
        }

    /* Metodo senza uso. Creato inizialmente da PHPUnit test
    @return         Restituisce la stringa previamente passata come argomento
    */
    public function run(string $input): string
    {
        return $input;
    }
}
