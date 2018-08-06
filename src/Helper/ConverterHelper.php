<?php
namespace Wolfish\Helper;

use Wolfish\Enum\Translate\BasicTranslateTypeEnum;
use Wolfish\Exception\TranslateException;
use Wolfish\Translate\BasicTranslate;
use Wolfish\Translate\PolishTranslate;

class ConverterHelper
{
    /**
     * @var BasicTranslate
     */
    private $translate;

    /**
     * @param BasicTranslate $translate
     * @throws TranslateException
     */
    public function __construct(BasicTranslate $translate)
    {
        $this->translate = $translate;
        $translate->loadTranslation($translate->getLang());
    }

    /**
     * @param $number int
     * @param $decimals string|int
     * @throws TranslateException
     * @return string
     */
    public function convert($number, $decimals = 0)
    {
        $result = '';
        $digitSegments = explode('|', number_format($number, 0, '', '|'));
        $digitSegmentsCount = count($digitSegments);
        foreach ($digitSegments as $key => $digitSegment) {
            $segmentLength = strlen($digitSegment);
            if (!(int)$digitSegment && $segmentLength > 1) {
                continue;
            }

            $segmentSplit = $this->splitSegment($digitSegment);
            $digitsCount = $segmentSplit['digitsCount'];
            $digits = $segmentSplit['digits'];

            switch ($segmentLength) {
                case 1:
                    $result .= $this->convertOnes($digits[0]);
                    break;

                case 2:
                    $result .= $this->convertTens($digits, $digitsCount);
                    break;

                case 3:
                    $result .= $this->convertHundreds($digits, $digitsCount);
                    break;
            }

            $result .= $this->translate->getSegmentName($digits, $key, $digitSegmentsCount);
        }

        if ((int)$decimals) {
            if (!empty($result)) {
                $result .= $this->translate->getDecimalSeparator() . ' ';
            }

            $segmentSplit = $this->splitSegment($decimals);
            $result .= $this->convertDecimals($segmentSplit['digits'], $segmentSplit['digitsCount']);
            $result .= $this->translate->getDecimalSegmentName(
                $segmentSplit['digits'],
                $segmentSplit['digitsCount']
            );

        }

        return trim($result);
    }

    /**
     * @param $digitSegment string|int
     * @return array
     */
    private function splitSegment($digitSegment)
    {
        $digits = str_split($digitSegment);
        array_walk($digits, function(&$v) {
            $v = (int)$v;
        });

        return [
            'digits' => $digits,
            'digitsCount' => count($digits)
        ];
    }

    /**
     * @param $num int
     * @throws TranslateException
     * @return string
     */
    private function convertOnes($num)
    {
        return $this->translate->getNumberString($num, BasicTranslateTypeEnum::SINGLE);
    }

    /**
     * @param $digits array
     * @param $digitsCount int
     * @throws TranslateException
     * @return string
     */
    private function convertTens($digits, $digitsCount)
    {
        $result = '';
        foreach ($digits as $key => $digit) {

            // Exception for 0 and 11
            if (
                $digit === 0 ||
                ($digit === 1 && $key === 0 && $digitsCount < 3)
            ) {
                // Exception for 10
                if (isset($digits[$key+1]) && !$digits[$key+1]) {
                    $result .= $this->translate->getNumberString(10, BasicTranslateTypeEnum::SINGLE);
                    continue;
                }

                continue;
            }

            switch ($key) {
                case 0:
                    $result .= $this->translate->getNumberString($digit, BasicTranslateTypeEnum::PLURAL);
                    break;

                case 1:
                    if (empty($result)) {
                        $result = $this->translate->getNumberString($digit, BasicTranslateTypeEnum::TENTH);
                    } else {
                        $result .= $this->translate->getNumberString($digit, BasicTranslateTypeEnum::SINGLE);
                    }
                    break;
            }
        }

        return $result;
    }

    /**
     * @param $digits array
     * @param $digitsCount int
     * @throws TranslateException
     * @return string
     */
    private function convertHundreds($digits, $digitsCount)
    {
        $result = '';
        foreach ($digits as $key => $digit) {

            // Exception for 0
            if ($digit === 0) {
                continue;
            }

            if ($digit === 1 && $key === 1) {
                // Exception for 10
                if (isset($digits[$key+1]) && !$digits[$key+1]) {
                    $result .= $this->translate->getNumberString(10, BasicTranslateTypeEnum::SINGLE);
                    continue;
                }

                // Exception for 11
                if ($digitsCount === 3 && $digits[2] != 0) {
                    continue;
                }
            }

            switch ($key) {
                case 0:
                    $result .= $this->translate->getNumberString($digit, BasicTranslateTypeEnum::HUNDREDTH);
                    break;

                case 1:
                    $result .= $this->translate->getNumberString($digit, BasicTranslateTypeEnum::PLURAL);
                    break;

                case 2:
                    if ($digitsCount === 3 && $digits[1] === 1) {
                        $result .= $this->translate->getNumberString($digit, BasicTranslateTypeEnum::TENTH);
                    } else {
                        $result .= $this->translate->getNumberString($digit, BasicTranslateTypeEnum::SINGLE);
                    }
                    break;
            }
        }

        return $result;
    }

    /**
     * @param $num array
     * @param $digitsCount int
     * @throws TranslateException
     * @return string
     */
    private function convertDecimals($num, $digitsCount)
    {
        // Exceptions for decimals in pl
        if ($this->translate instanceof PolishTranslate) {
            $sum = (int)join($num);
            if ($sum === 1 || $sum === 2) {
                return $this->translate->getDecimalString($sum);
            }
        }

        $result = $digitsCount > 1 && (int)$num[0] ?
            $this->convertTens($num, $digitsCount)
            :
            $this->convertOnes($digitsCount === 1 ? $num[0] : $num[1]);

        return $result;
    }
}
