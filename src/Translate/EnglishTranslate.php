<?php
namespace Wolfish\Translate;

use Wolfish\Enum\Translate\BasicTranslateSegmentEnum;
use Wolfish\Enum\NumberSegmentEnum;

class EnglishTranslate extends BasicTranslate
{
    public function __construct($lang = 'en')
    {
        parent::__construct($lang);
    }

    public function getDecimalSegmentName($digitsArray, $digitsCount)
    {
        $result = '';
        if ($digitsCount > 1) {
            $result .= $this->numberSegmentArray[BasicTranslateSegmentEnum::HUNDREDTHS];
        } else {
            $result .= $this->numberSegmentArray[BasicTranslateSegmentEnum::TENTHS];
        }

        return $result . ' ';
    }

    public function getSegmentName($digitsArray, $numberSegment, $digitSegmentsCount)
    {
        $result = '';
        switch ($numberSegment) {
            case NumberSegmentEnum::MILLION:
                if ($digitSegmentsCount === 3) {
                    $result .= $this->numberSegmentArray[BasicTranslateSegmentEnum::MILLION];
                } elseif ($digitSegmentsCount === 2) {
                    $result .= $this->numberSegmentArray[BasicTranslateSegmentEnum::THOUSAND];
                }
                break;

            case NumberSegmentEnum::THOUSAND:
                if ($digitSegmentsCount === 3) {
                    $result .= $this->numberSegmentArray[BasicTranslateSegmentEnum::THOUSAND];
                }
                break;
        }

        return empty($result) ? $result : $result . ' ';
    }
}
