<?php
namespace Wolfish\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wolfish\Exception\TranslateException;
use Wolfish\Helper\ConverterHelper;
use Wolfish\Translate\EnglishTranslate;
use Wolfish\Translate\PolishTranslate;

class NumToTextCommand extends Command
{
    const MAX_NUMBER = 999999999;

    const MAX_DECIMALS = 99;

    /**
     * @var int
     */
    private $number;

    /**
     * @var string
     */
    private $decimals;

    /**
     * @var string
     */
    private $lang;


    protected function configure()
    {
        $this->setName('num2txt')
            ->setDescription('Changes given number to a text string')
            ->setHelp(
                "This command allows you to change any number you input to a text string\n" .
                "The maximum allowed number is <info>" . self::MAX_NUMBER . '.' . self::MAX_DECIMALS . "</info> ( < one billion)\n" .
                "The allowed precision is <info>" . strlen(self::MAX_DECIMALS) . "</info> decimal points\n\n" .
                "Usage: <comment>php app.php num2txt 100</comment>"
            );

        $this->addArgument('number', InputArgument::REQUIRED, 'number to convert');
        $this->addArgument('lang', InputArgument::OPTIONAL, 'language of result', 'en');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!preg_match('/^-?\d+(\.?|,?)\d{0,2}$/i', $input->getArgument('number'))) {
            return $this->outputError($output, 'Given input is not a valid number!');
        }

        $this->number = str_replace(',', '.', $input->getArgument('number'));
        $this->lang = $input->getArgument('lang');

        switch ($this->lang) {
            case 'pl':
                $translate = new PolishTranslate();
                break;

            case 'en':
            default:
                $translate = new EnglishTranslate();
                break;
        }

        $numberArray = explode('.', $this->number);
        $this->number = (int)$numberArray[0];
        $this->decimals = isset($numberArray[1]) ? $numberArray[1] : 0;

        if ($this->number > self::MAX_NUMBER) {
            return $this->outputError($output, 'Maximum number exceeded');
        }

        if ($this->number < 0) {
            return $this->outputError($output, 'Negative numbers not allowed');
        }

        try {
            $converter = new ConverterHelper($translate);
            return $this->outputResult($output, $converter->convert($this->number, $this->decimals));
        } catch (TranslateException $e) {
            return $this->outputError($output, $e->getMessage());
        }
    }

    /**
     * @param $output OutputInterface
     * @param $result string
     * @return bool
     */
    private function outputResult(OutputInterface $output, $result)
    {
        $output->writeln('<comment>' . $result . '</comment>');
        return true;
    }

    /**
     * @param OutputInterface $output
     * @param $error string
     * @return bool
     */
    private function outputError(OutputInterface $output, $error)
    {
        $output->writeln('<error>' . $error . '</error>');
        $output->writeln('See <comment>--help</comment> for more info');
        return false;
    }
}
