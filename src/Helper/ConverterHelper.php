<?php
namespace Wolfish\Helper;

use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Wolfish\Enum\NumberStringEnum;

class ConverterHelper
{
    /**
     * @var string
     */
    private $lang;

    /**
     * @var array
     */
    private $numberStringArray;

    /**
     * @var array
     */
    private $numberSegmentArray;

    /**
     * @var string
     */
    private $decimalSeparator;

    public function __construct($lang)
    {
        $this->lang = $lang;
        $this->loadTranslation();
    }

    /**
     * @param $number int
     * @param $decimals string|int
     * @return string
     */
    public function convert($number, $decimals = 0)
    {
        $result = '';
        $digitSegments = explode('|', number_format($number, 0, '', '|'));
        $digitSegmentsCount = count($digitSegments);
        foreach ($digitSegments as $key => $digitSegment) {
            if (!(int)$digitSegment) {
                continue;
            }

            $segmentSplit = $this->splitSegment($digitSegment);
            $digitsCount = $segmentSplit['digitsCount'];
            $digits = $segmentSplit['digits'];

            switch (strlen($digitSegment)) {
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

            $result .= $this->getSegmentName($digitSegmentsCount, $digits, $key);
        }

        if ((int)$decimals) {
            if (!empty($result)) {
                $result .= $this->decimalSeparator . ' ';
            }

            $segmentSplit = $this->splitSegment($decimals);
            $result .= $this->convertDecimals($segmentSplit['digits'], $segmentSplit['digitsCount']);
            $result .= $this->getDecimalSegmentName($segmentSplit['digits'], $segmentSplit['digitsCount']);

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
     * @return string
     */
    private function convertOnes($num)
    {
        $result = '';
        if (isset($this->numberStringArray[$num])) {
            $result = $this->numberStringArray[$num];
            $result = is_array($result) ? $result[0] : $result;
        }

        return $result . ' ';
    }

    /**
     * @param $digits array
     * @param $digitsCount int
     * @return string
     */
    private function convertTens($digits, $digitsCount)
    {
        $result = '';
        foreach ($digits as $key => $digit) {

            // Exception for 0 and 11
            if (
                !isset($this->numberStringArray[$digit]) ||
                ($digit === 1 && $key === 0 && $digitsCount < 3)
            ) {
                // Exception for 10
                if (isset($digits[$key+1]) && !$digits[$key+1]) {
                    $result .= $this->numberStringArray[10] . ' ';
                    continue;
                }

                continue;
            }

            $digitString = $this->numberStringArray[$digit];

            switch ($key) {
                case 0:
                    $result .= $digitString[2];
                    break;

                case 1:
                    if (empty($result)) {
                        $result = $digitString[1];
                    } else {
                        $result .= $digitString[0];
                    }
                    break;
            }

            $result .= ' ';
        }

        return $result;
    }

    /**
     * @param $digits array
     * @param $digitsCount int
     * @return string
     */
    private function convertHundreds($digits, $digitsCount)
    {
        $result = '';
        foreach ($digits as $key => $digit) {

            // Exception for 0
            if (!isset($this->numberStringArray[$digit])) {
                continue;
            }

            if ($digit === 1 && $key === 1) {
                // Exception for 10
                if (isset($digits[$key+1]) && !$digits[$key+1]) {
                    $result .= $this->numberStringArray[10] . ' ';
                    continue;
                }

                // Exception for 11
                if ($digitsCount === 3 && $digits[2] != 0) {
                    continue;
                }
            }

            $digitString = $this->numberStringArray[$digit];

            switch ($key) {
                case 0:
                    $result .= $digitString[3];
                    break;

                case 1:
                    $result .= $digitString[2];
                    break;

                case 2:
                    if ($digitsCount === 3 && $digits[1] === 1) {
                        $result .= $digitString[1];
                    } else {
                        $result .= $digitString[0];
                    }
                    break;
            }

            $result .= ' ';
        }

        return $result;
    }

    /**
     * @param $num array
     * @param $digitsCount int
     * @return string
     */
    private function convertDecimals($num, $digitsCount)
    {
        // Exceptions for decimals in pl
        if ($this->lang === 'pl') {
            $sum = (int)join($num);
            if ($sum === 1 || $sum === 2) {
                return $this->numberStringArray[$sum][4] . ' ';
            }
        }

        $result = $digitsCount > 1 && (int)$num[0] ?
            $this->convertTens($num, $digitsCount)
            :
            $this->convertOnes($digitsCount === 1 ? $num[0] : $num[1]);

        return $result;
    }

    /**
     * @param $digits array
     * @param $digitsCount int
     * @return string
     */
    private function getDecimalSegmentName($digits, $digitsCount)
    {
        $result = '';
        $num = (int)join($digits);

        switch ($this->lang) {
            case 'en':
                if ($digitsCount > 1) {
                    $result .= $this->numberSegmentArray[NumberStringEnum::HUNDREDTHS];
                } else {
                    $result .= $this->numberSegmentArray[NumberStringEnum::TENTHS];
                }
                break;

            case 'pl':
                $pluralNums = [2,3,4];
                $isPlural = in_array(end($digits), $pluralNums);

                if ($digitsCount === 1) {
                    if ($num === 1) {
                        $result .= $this->numberSegmentArray[NumberStringEnum::TENTH];
                    } elseif ($isPlural) {
                        $result .= $this->numberSegmentArray[NumberStringEnum::TENTH_MULTI];
                    } else {
                        $result .= $this->numberSegmentArray[NumberStringEnum::TENTHS];
                    }
                } else {
                    if ($num === 1) {
                        $result .= $this->numberSegmentArray[NumberStringEnum::HUNDREDTH];
                    } elseif (!(int)$digits[0] && $isPlural) {
                        $result .= $this->numberSegmentArray[NumberStringEnum::HUNDREDTH_MULTI];
                    } else {
                        $result .= $this->numberSegmentArray[NumberStringEnum::HUNDREDTHS];
                    }
                }
                break;
        }

        return $result;
    }

    /**
     * @param $digitSegmentsCount int
     * @param $digits array
     * @param $key int
     * @return string
     */
    private function getSegmentName($digitSegmentsCount, $digits, $key)
    {
        $result = '';
        $num = (int)join($digits);

        switch ($this->lang) {
            case 'en':
                switch ($key) {
                    case 0:
                        if ($digitSegmentsCount === 3) {
                            $result .= $this->numberSegmentArray[NumberStringEnum::MILLION];
                        } elseif ($digitSegmentsCount === 2) {
                            $result .= $this->numberSegmentArray[NumberStringEnum::THOUSAND];
                        }
                        break;

                    case 1:
                        if ($digitSegmentsCount === 3) {
                            $result .= $this->numberSegmentArray[NumberStringEnum::THOUSAND];
                        }
                        break;
                }
                break;

            case 'pl':
                $pluralNums = [2,3,4];
                $isPlural = in_array(end($digits), $pluralNums);

                switch ($key) {
                    case 0:
                        if ($digitSegmentsCount === 3) {
                            if ($num === 1) {
                                $resultString = $this->numberSegmentArray[NumberStringEnum::MILLION];
                            } elseif ($isPlural) {
                                $resultString = $this->numberSegmentArray[NumberStringEnum::MILLIONS];
                            } else {
                                $resultString = $this->numberSegmentArray[NumberStringEnum::MILLIONS_MULTI];
                            }
                            $result .= $resultString;
                        } elseif ($digitSegmentsCount === 2) {
                            if ($num === 1) {
                                $resultString = $this->numberSegmentArray[NumberStringEnum::THOUSAND_SINGLE];
                            } elseif ($isPlural) {
                                $resultString = $this->numberSegmentArray[NumberStringEnum::THOUSAND];
                            } else {
                                $resultString = $this->numberSegmentArray[NumberStringEnum::THOUSANDS];
                            }
                            $result .= $resultString;
                        }
                        break;

                    case 1:
                        if ($digitSegmentsCount === 3) {
                            $result .= $this->numberSegmentArray[NumberStringEnum::THOUSANDS];
                        }
                        break;
                }
                break;
        }

        return empty($result) ? $result : $result . ' ';
    }

    private function loadTranslation()
    {
        $translator = new Translator($this->lang);
        $translator->addLoader('xlf', new XliffFileLoader());
        $translator->addResource('xlf', 'lang/' . $this->lang . '.xlf', $this->lang, $this->lang);

        $this->decimalSeparator = $translator->trans('and', [], $this->lang);

        $this->numberSegmentArray = [
            NumberStringEnum::MILLION => $translator->trans('million', [], $this->lang),
            NumberStringEnum::MILLIONS => $translator->trans('millions', [], $this->lang),
            NumberStringEnum::MILLIONS_MULTI => $translator->trans('millions multi', [], $this->lang),
            NumberStringEnum::THOUSAND => $translator->trans('thousand', [], $this->lang),
            NumberStringEnum::THOUSANDS => $translator->trans('thousands', [], $this->lang),
            NumberStringEnum::THOUSAND_SINGLE => $translator->trans('thousand single', [], $this->lang),
            NumberStringEnum::TENTH => $translator->trans('tenth', [], $this->lang),
            NumberStringEnum::TENTHS => $translator->trans('tenths', [], $this->lang),
            NumberStringEnum::HUNDREDTH => $translator->trans('hundredth', [], $this->lang),
            NumberStringEnum::HUNDREDTHS => $translator->trans('hundredths', [], $this->lang),
            NumberStringEnum::TENTH_MULTI => $translator->trans('tenth multi', [], $this->lang),
            NumberStringEnum::HUNDREDTH_MULTI => $translator->trans('hundredth multi', [], $this->lang)
        ];

        $this->numberStringArray = [
            1 => [
                $translator->trans('one', [], $this->lang),
                $translator->trans('eleven', [], $this->lang),
                $translator->trans('eleven_multi', [], $this->lang),
                $translator->trans('one hundred', [], $this->lang),
                $translator->trans('one decimal', [], $this->lang)
            ],
            2 => [
                $translator->trans('two', [], $this->lang),
                $translator->trans('twelve', [], $this->lang),
                $translator->trans('twenty', [], $this->lang),
                $translator->trans('two hundred', [], $this->lang),
                $translator->trans('two decimal', [], $this->lang)
            ],
            3 => [
                $translator->trans('three', [], $this->lang),
                $translator->trans('thirteen', [], $this->lang),
                $translator->trans('thirty', [], $this->lang),
                $translator->trans('three hundred', [], $this->lang)
            ],
            4 => [
                $translator->trans('four', [], $this->lang),
                $translator->trans('fourteen', [], $this->lang),
                $translator->trans('forty', [], $this->lang),
                $translator->trans('four hundred', [], $this->lang)
            ],
            5 => [
                $translator->trans('five', [], $this->lang),
                $translator->trans('fifteen', [], $this->lang),
                $translator->trans('fifty', [], $this->lang),
                $translator->trans('five hundred', [], $this->lang)
            ],
            6 => [
                $translator->trans('six', [], $this->lang),
                $translator->trans('sixteen', [], $this->lang),
                $translator->trans('sixty', [], $this->lang),
                $translator->trans('six hundred', [], $this->lang)
            ],
            7 => [
                $translator->trans('seven', [], $this->lang),
                $translator->trans('seventeen', [], $this->lang),
                $translator->trans('seventy', [], $this->lang),
                $translator->trans('seven hundred', [], $this->lang)
            ],
            8 => [
                $translator->trans('eight', [], $this->lang),
                $translator->trans('eighteen', [], $this->lang),
                $translator->trans('eighty', [], $this->lang),
                $translator->trans('eight hundred', [], $this->lang)
            ],
            9 => [
                $translator->trans('nine', [], $this->lang),
                $translator->trans('nineteen', [], $this->lang),
                $translator->trans('ninety', [], $this->lang),
                $translator->trans('nine hundred', [], $this->lang)
            ],
            10 => $translator->trans('ten', [], $this->lang)
        ];
    }
}
