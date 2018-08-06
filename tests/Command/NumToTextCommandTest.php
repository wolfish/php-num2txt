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

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $application = new Application();
        $application->add(new NumToTextCommand());

        $command = $application->find('num2txt');
        $commandTester = new CommandTester($command);

        $this->cmd = $commandTester;

        parent::__construct($name, $data, $dataName);
    }

    private function getOutput()
    {
        return trim(preg_replace("/(\n|\r)*/",'', $this->cmd->getDisplay()));
    }

    public function testZeroEn()
    {
        $this->cmd->execute(array('number' => 0, 'lang' => 'en'));
        $this->assertEquals('zero', $this->getOutput());
    }

    public function testTenEn()
    {
        $this->cmd->execute(array('number' => 10, 'lang' => 'en'));
        $this->assertEquals('ten', $this->getOutput());
    }

    public function testTenPl()
    {
        $this->cmd->execute(array('number' => 10, 'lang' => 'pl'));
        $this->assertEquals('dziesięć', $this->getOutput());
    }

    public function testTensEn()
    {
        $this->cmd->execute(array('number' => 11, 'lang' => 'en'));
        $this->assertEquals('eleven', $this->getOutput());
    }

    public function testTensPl()
    {
        $this->cmd->execute(array('number' => 11, 'lang' => 'pl'));
        $this->assertEquals('jedenaście', $this->getOutput());
    }

    public function testHundredsEn()
    {
        $this->cmd->execute(array('number' => 196, 'lang' => 'en'));
        $this->assertEquals('one hundred ninety six', $this->getOutput());
    }

    public function testHundredsPl()
    {
        $this->cmd->execute(array('number' => 196, 'lang' => 'pl'));
        $this->assertEquals('sto dziewięćdziesiąt sześć', $this->getOutput());
    }

    public function testThousandsEn()
    {
        $this->cmd->execute(array('number' => 6032, 'lang' => 'en'));
        $this->assertEquals('six thousand thirty two', $this->getOutput());
    }

    public function testThousandsPl()
    {
        $this->cmd->execute(array('number' => 6032, 'lang' => 'pl'));
        $this->assertEquals('sześć tysięcy trzydzieści dwa', $this->getOutput());
    }
    public function testTenThousandsEn()
    {
        $this->cmd->execute(array('number' => 74450, 'lang' => 'en'));
        $this->assertEquals('seventy four thousand four hundred fifty', $this->getOutput());
    }

    public function testTenThousandsPl()
    {
        $this->cmd->execute(array('number' => 74450, 'lang' => 'pl'));
        $this->assertEquals('siedemdziesiąt cztery tysiące czterysta pięćdziesiąt', $this->getOutput());
    }

    public function testHundredThousandsEn()
    {
        $this->cmd->execute(array('number' => 104992, 'lang' => 'en'));
        $this->assertEquals('one hundred four thousand nine hundred ninety two', $this->getOutput());
    }

    public function testHundredThousandsPl()
    {
        $this->cmd->execute(array('number' => 104992, 'lang' => 'pl'));
        $this->assertEquals('sto cztery tysiące dziewięćset dziewięćdziesiąt dwa', $this->getOutput());
    }

    public function testMillionEn()
    {
        $this->cmd->execute(array('number' => 4008111, 'lang' => 'en'));
        $this->assertEquals('four million eight thousand one hundred eleven', $this->getOutput());
    }

    public function testMillionPl()
    {
        $this->cmd->execute(array('number' => 4008111, 'lang' => 'pl'));
        $this->assertEquals('cztery miliony osiem tysięcy sto jedenaście', $this->getOutput());
    }

    public function testTenMillionEn()
    {
        $this->cmd->execute(array('number' => 11875352, 'lang' => 'en'));
        $this->assertEquals('eleven million eight hundred seventy five thousand three hundred fifty two', $this->getOutput());
    }

    public function testTenMillionPl()
    {
        $this->cmd->execute(array('number' => 11875352, 'lang' => 'pl'));
        $this->assertEquals('jedenaście milionów osiemset siedemdziesiąt pięć tysięcy trzysta pięćdziesiąt dwa', $this->getOutput());
    }

    public function testHundredMillionEn()
    {
        $this->cmd->execute(array('number' => 999999999, 'lang' => 'en'));
        $this->assertEquals('nine hundred ninety nine million nine hundred ninety nine thousand nine hundred ninety nine', $this->getOutput());
    }

    public function testHundredMillionPl()
    {
        $this->cmd->execute(array('number' => 999999999, 'lang' => 'pl'));
        $this->assertEquals('dziewięćset dziewięćdziesiąt dziewięć milionów dziewięćset dziewięćdziesiąt dziewięć tysięcy dziewięćset dziewięćdziesiąt dziewięć', $this->getOutput());
    }

    public function testZeroDecimalEn()
    {
        $this->cmd->execute(array('number' => 162.0, 'lang' => 'en'));
        $this->assertEquals('one hundred sixty two', $this->getOutput());
    }

    public function testZeroDecimalPl()
    {
        $this->cmd->execute(array('number' => 162.00, 'lang' => 'pl'));
        $this->assertEquals('sto sześćdziesiąt dwa', $this->getOutput());
    }

    public function testTenthDecimalEn()
    {
        $this->cmd->execute(array('number' => 8930.4, 'lang' => 'en'));
        $this->assertEquals('eight thousand nine hundred thirty and four tenths', $this->getOutput());
    }

    public function testTenthDecimalPl()
    {
        $this->cmd->execute(array('number' => 8930.4, 'lang' => 'pl'));
        $this->assertEquals('osiem tysięcy dziewięćset trzydzieści i cztery dziesiąte', $this->getOutput());
    }

    public function testHundredthDecimalEn()
    {
        $this->cmd->execute(array('number' => 452133.41, 'lang' => 'en'));
        $this->assertEquals('four hundred fifty two thousand one hundred thirty three and forty one hundredths', $this->getOutput());
    }

    public function testHundredthDecimalPl()
    {
        $this->cmd->execute(array('number' => 452133.41, 'lang' => 'pl'));
        $this->assertEquals('czterysta pięćdziesiąt dwa tysiące sto trzydzieści trzy i czterdzieści jeden setnych', $this->getOutput());
    }

    public function testZeroPreifxEn()
    {
        $this->cmd->execute(array('number' => '0032,1', 'lang' => 'en'));
        $this->assertEquals('thirty two and one tenths', $this->getOutput());
    }

    public function testZeroPrefixPl()
    {
        $this->cmd->execute(array('number' => '0032,1', 'lang' => 'pl'));
        $this->assertEquals('trzydzieści dwa i jedna dziesiąta', $this->getOutput());
    }

    public function testErrorMaxNumber()
    {
        $this->cmd->execute(array('number' => 1000000000));
        $this->assertEquals('Maximum number exceededSee --help for more info', $this->getOutput());
    }

    public function testErrorPrecision()
    {
        $this->cmd->execute(array('number' => 1.001));
        $this->assertEquals('Given input is not a valid number!See --help for more info', $this->getOutput());
    }

    public function testErrorMalformedNumber()
    {
        $this->cmd->execute(array('number' => '13a7'));
        $this->assertEquals('Given input is not a valid number!See --help for more info', $this->getOutput());
    }

}
