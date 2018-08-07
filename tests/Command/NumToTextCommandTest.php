<?php
namespace Wolfish\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Wolfish\Command\NumToTextCommand;
use PHPUnit\Framework\TestCase;

class NumToTextCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    private $cmd;

    public function setUp()
    {
        $application = new Application();
        $application->add(new NumToTextCommand());

        $command = $application->find('num2txt');
        $commandTester = new CommandTester($command);

        $this->cmd = $commandTester;
    }

    private function getOutput()
    {
        return trim(preg_replace("/(\n|\r)*/",'', $this->cmd->getDisplay()));
    }

    /**
     * @param $number float
     * @param $lang string
     * @param $expected string
     * @dataProvider testDataProvider
     */
    public function testCommandOutput($number, $lang, $expected)
    {
        $this->cmd->execute(array('number' => $number, 'lang' => $lang));
        $this->assertEquals($this->getOutput(), $expected);
    }

    /**
     * @doesNotPerformAssertions
     * @return array
     */
    public function testDataProvider()
    {
        return [
            [0, 'en', 'zero'],
            [10, 'en', 'ten'],
            [11, 'en', 'eleven'],
            [196, 'en', 'one hundred ninety six'],
            [6032, 'en', 'six thousand thirty two'],
            [74450, 'en', 'seventy four thousand four hundred fifty'],
            [104992, 'en', 'one hundred four thousand nine hundred ninety two'],
            [4008111, 'en', 'four million eight thousand one hundred eleven'],
            [11875352, 'en', 'eleven million eight hundred seventy five thousand three hundred fifty two'],
            [999999999, 'en',
                'nine hundred ninety nine million nine hundred ninety nine thousand nine hundred ninety nine'
            ],
            [162.0, 'en', 'one hundred sixty two'],
            [8930.4, 'en', 'eight thousand nine hundred thirty and four tenths'],
            [452133.41, 'en', 'four hundred fifty two thousand one hundred thirty three and forty one hundredths'],
            ['0032,1', 'en', 'thirty two and one tenths'],
            [10, 'pl', 'dziesięć'],
            [11, 'pl', 'jedenaście'],
            [196, 'pl', 'sto dziewięćdziesiąt sześć'],
            [6032, 'pl', 'sześć tysięcy trzydzieści dwa'],
            [74450, 'pl', 'siedemdziesiąt cztery tysiące czterysta pięćdziesiąt'],
            [104992, 'pl', 'sto cztery tysiące dziewięćset dziewięćdziesiąt dwa'],
            [4008111, 'pl', 'cztery miliony osiem tysięcy sto jedenaście'],
            [11875352, 'pl', 'jedenaście milionów osiemset siedemdziesiąt pięć tysięcy trzysta pięćdziesiąt dwa'],
            [999999999, 'pl',
                'dziewięćset dziewięćdziesiąt dziewięć milionów dziewięćset dziewięćdziesiąt ' .
                'dziewięć tysięcy dziewięćset dziewięćdziesiąt dziewięć'
            ],
            [162.0, 'pl', 'sto sześćdziesiąt dwa'],
            [8930.4, 'pl', 'osiem tysięcy dziewięćset trzydzieści i cztery dziesiąte'],
            [452133.41, 'pl', 'czterysta pięćdziesiąt dwa tysiące sto trzydzieści trzy i czterdzieści jeden setnych'],
            ['0032,1', 'pl', 'trzydzieści dwa i jedna dziesiąta'],
            [1000000000, null, 'Maximum number exceededSee --help for more info'],
            [1.001, null, 'Given input is not a valid number!See --help for more info'],
            ['13a7', null, 'Given input is not a valid number!See --help for more info']
        ];
    }
}
