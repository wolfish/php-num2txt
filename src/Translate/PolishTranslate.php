<?php
namespace Wolfish\Translate;

use Wolfish\Enum\NumberSegmentEnum;
use Wolfish\Enum\Translate\PolishTranslateSegmentEnum;
use Wolfish\Enum\Translate\PolishTranslateTypeEnum;

class PolishTranslate extends BasicTranslate
{
    public function __construct($lang = 'pl')
    {
        parent::__construct($lang);
    }

    /**
     * @param $lang string
     * @throws \Wolfish\Exception\TranslateException
     */
    public function loadTranslation($lang)
    {
        parent::loadTranslation($lang);

        $this->numberSegmentArray = array_merge($this->numberSegmentArray, [
            PolishTranslateSegmentEnum::MILLIONS => $this->translator->trans('millions', [], $lang),
            PolishTranslateSegmentEnum::MILLIONS_MULTI => $this->translator->trans('millions multi', [], $lang),
            PolishTranslateSegmentEnum::THOUSANDS => $this->translator->trans('thousands', [], $lang),
            PolishTranslateSegmentEnum::THOUSAND_SINGLE => $this->translator->trans('thousand single', [], $lang),
            PolishTranslateSegmentEnum::TENTH => $this->translator->trans('tenth', [], $lang),
            PolishTranslateSegmentEnum::HUNDREDTH => $this->translator->trans('hundredth', [], $lang),
            PolishTranslateSegmentEnum::TENTH_MULTI => $this->translator->trans('tenth multi', [], $lang),
            PolishTranslateSegmentEnum::HUNDREDTH_MULTI => $this->translator->trans('hundredth multi', [], $lang)
        ]);

        $this->numberStringArray[1] = array_merge($this->numberStringArray[1], [
            PolishTranslateTypeEnum::DECIMAL => $this->translator->trans('one decimal', [], $lang)
        ]);

        $this->numberStringArray[2] = array_merge($this->numberStringArray[2], [
            PolishTranslateTypeEnum::DECIMAL => $this->translator->trans('two decimal', [], $lang)
        ]);
    }

    public function getDecimalSegmentName($digitsArray, $digitsCount)
    {
        $result = '';
        $num = (int)join($digitsArray);

        $pluralNumbers = [2,3,4];
        $isPlural = in_array(end($digitsArray), $pluralNumbers);

        if ($digitsCount === 1) {
            if ($num === 1) {
                $result .= $this->numberSegmentArray[PolishTranslateSegmentEnum::TENTH];
            } elseif ($isPlural) {
                $result .= $this->numberSegmentArray[PolishTranslateSegmentEnum::TENTH_MULTI];
            } else {
                $result .= $this->numberSegmentArray[PolishTranslateSegmentEnum::TENTHS];
            }
        } else {
            if ($num === 1) {
                $result .= $this->numberSegmentArray[PolishTranslateSegmentEnum::HUNDREDTH];
            } elseif (!(int)$digitsArray[0] && $isPlural) {
                $result .= $this->numberSegmentArray[PolishTranslateSegmentEnum::HUNDREDTH_MULTI];
            } else {
                $result .= $this->numberSegmentArray[PolishTranslateSegmentEnum::HUNDREDTHS];
            }
        }

        return $result;
    }

    public function getSegmentName($digitsArray, $numberSegment, $digitSegmentsCount)
    {
        $result = '';
        $num = (int)join($digitsArray);
        $isPlural = $this->isPlural($digitsArray);

        switch ($numberSegment) {
            case NumberSegmentEnum::MILLION:
                if ($digitSegmentsCount === 3) {
                    if ($num === 1) {
                        $resultString = $this->numberSegmentArray[PolishTranslateSegmentEnum::MILLION];
                    } elseif ($isPlural) {
                        $resultString = $this->numberSegmentArray[PolishTranslateSegmentEnum::MILLIONS];
                    } else {
                        $resultString = $this->numberSegmentArray[PolishTranslateSegmentEnum::MILLIONS_MULTI];
                    }
                    $result .= $resultString;
                } elseif ($digitSegmentsCount === 2) {
                    if ($num === 1) {
                        $resultString = $this->numberSegmentArray[PolishTranslateSegmentEnum::THOUSAND_SINGLE];
                    } elseif ($isPlural) {
                        $resultString = $this->numberSegmentArray[PolishTranslateSegmentEnum::THOUSAND];
                    } else {
                        $resultString = $this->numberSegmentArray[PolishTranslateSegmentEnum::THOUSANDS];
                    }
                    $result .= $resultString;
                }
                break;

            case NumberSegmentEnum::THOUSAND:
                if ($digitSegmentsCount === 3) {
                    $result .= $this->numberSegmentArray[PolishTranslateSegmentEnum::THOUSANDS];
                }
                break;
        }

        return empty($result) ? $result : $result . ' ';
    }

    /**
     * @param $number int
     * @return string
     * @throws \Wolfish\Exception\TranslateException
     */
    public function getDecimalString($number)
    {
        return $this->getNumberString($number, PolishTranslateTypeEnum::DECIMAL);
    }

    /**
     * @param $digits array
     * @return bool
     */
    private function isPlural($digits)
    {
        $pluralNumbers = [2,3,4];
        return in_array(end($digits), $pluralNumbers);
    }
}
