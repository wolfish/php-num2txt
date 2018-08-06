<?php
namespace Wolfish\Translate;

use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Wolfish\Enum\Translate\BasicTranslateSegmentEnum;
use Wolfish\Enum\Translate\BasicTranslateTypeEnum;
use Wolfish\Exception\TranslateException;

abstract class BasicTranslate
{
    /**
     * @var string
     */
    protected $lang;

    /**
     * @var array
     */
    protected $numberSegmentArray;

    /**
     * @var array
     */
    protected $numberStringArray;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param $lang string
     */
    public function __construct($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @return string
     */
    public function getDecimalSeparator()
    {
        return $this->translator->trans('and', [], $this->lang);
    }

    /**
     * @param $lang string
     * @throws TranslateException
     */
    public function loadTranslation($lang)
    {
        $resourcePath = 'lang/' . $lang . '.xlf';
        if (!file_exists($resourcePath)) {
            throw new TranslateException(
                'Language XLF file not found',
                TranslateException::ERROR_FILE_NOT_FOUND
            );
        }

        $translator = new Translator($lang);
        $translator->addLoader('xlf', new XliffFileLoader());
        $translator->addResource('xlf', $resourcePath, $lang, $lang);
        $this->translator = $translator;

        $this->numberSegmentArray = [
            BasicTranslateSegmentEnum::MILLION => $translator->trans('million', [], $lang),
            BasicTranslateSegmentEnum::THOUSAND => $translator->trans('thousand', [], $lang),
            BasicTranslateSegmentEnum::TENTHS => $translator->trans('tenths', [], $lang),
            BasicTranslateSegmentEnum::HUNDREDTHS => $translator->trans('hundredths', [], $lang)
        ];

        $this->numberStringArray = [
            1 => [
                BasicTranslateTypeEnum::SINGLE => $translator->trans('one', [], $lang),
                BasicTranslateTypeEnum::TENTH => $translator->trans('eleven', [], $lang),
                BasicTranslateTypeEnum::PLURAL => $translator->trans('eleven', [], $lang),
                BasicTranslateTypeEnum::HUNDREDTH => $translator->trans('one hundred', [], $lang)
            ],
            2 => [
                BasicTranslateTypeEnum::SINGLE => $translator->trans('two', [], $lang),
                BasicTranslateTypeEnum::TENTH => $translator->trans('twelve', [], $lang),
                BasicTranslateTypeEnum::PLURAL => $translator->trans('twenty', [], $lang),
                BasicTranslateTypeEnum::HUNDREDTH => $translator->trans('two hundred', [], $lang)
            ],
            3 => [
                BasicTranslateTypeEnum::SINGLE => $translator->trans('three', [], $lang),
                BasicTranslateTypeEnum::TENTH => $translator->trans('thirteen', [], $lang),
                BasicTranslateTypeEnum::PLURAL => $translator->trans('thirty', [], $lang),
                BasicTranslateTypeEnum::HUNDREDTH => $translator->trans('three hundred', [], $lang)
            ],
            4 => [
                BasicTranslateTypeEnum::SINGLE => $translator->trans('four', [], $lang),
                BasicTranslateTypeEnum::TENTH => $translator->trans('fourteen', [], $lang),
                BasicTranslateTypeEnum::PLURAL => $translator->trans('forty', [], $lang),
                BasicTranslateTypeEnum::HUNDREDTH => $translator->trans('four hundred', [], $lang)
            ],
            5 => [
                BasicTranslateTypeEnum::SINGLE => $translator->trans('five', [], $lang),
                BasicTranslateTypeEnum::TENTH => $translator->trans('fifteen', [], $lang),
                BasicTranslateTypeEnum::PLURAL => $translator->trans('fifty', [], $lang),
                BasicTranslateTypeEnum::HUNDREDTH => $translator->trans('five hundred', [], $lang)
            ],
            6 => [
                BasicTranslateTypeEnum::SINGLE => $translator->trans('six', [], $lang),
                BasicTranslateTypeEnum::TENTH => $translator->trans('sixteen', [], $lang),
                BasicTranslateTypeEnum::PLURAL => $translator->trans('sixty', [], $lang),
                BasicTranslateTypeEnum::HUNDREDTH => $translator->trans('six hundred', [], $lang)
            ],
            7 => [
                BasicTranslateTypeEnum::SINGLE => $translator->trans('seven', [], $lang),
                BasicTranslateTypeEnum::TENTH => $translator->trans('seventeen', [], $lang),
                BasicTranslateTypeEnum::PLURAL => $translator->trans('seventy', [], $lang),
                BasicTranslateTypeEnum::HUNDREDTH => $translator->trans('seven hundred', [], $lang)
            ],
            8 => [
                BasicTranslateTypeEnum::SINGLE => $translator->trans('eight', [], $lang),
                BasicTranslateTypeEnum::TENTH => $translator->trans('eighteen', [], $lang),
                BasicTranslateTypeEnum::PLURAL => $translator->trans('eighty', [], $lang),
                BasicTranslateTypeEnum::HUNDREDTH => $translator->trans('eight hundred', [], $lang)
            ],
            9 => [
                BasicTranslateTypeEnum::SINGLE => $translator->trans('nine', [], $lang),
                BasicTranslateTypeEnum::TENTH => $translator->trans('nineteen', [], $lang),
                BasicTranslateTypeEnum::PLURAL => $translator->trans('ninety', [], $lang),
                BasicTranslateTypeEnum::HUNDREDTH => $translator->trans('nine hundred', [], $lang)
            ],
            10 => [
                BasicTranslateTypeEnum::SINGLE => $translator->trans('ten', [], $lang)
            ],
            0 => [
                BasicTranslateTypeEnum::SINGLE => $translator->trans('zero', [], $lang)
            ]
        ];
    }

    /**
     * @param $number int
     * @param $type int
     * @return string
     * @throws TranslateException
     */
    public function getNumberString($number, $type)
    {
        if (!isset($this->numberStringArray[$number])
            && !isset($this->numberStringArray[$number][$type])
        ) {
            throw new TranslateException(
                'Number string not defined',
                TranslateException::ERROR_NUMBER_STRING
            );
        }

        return $this->numberStringArray[$number][$type] . ' ';
    }

    /**
     * @param $digitsArray array
     * @param $numberSegment int
     * @param $digitSegmentsCount int
     * @return string
     */
    abstract public function getSegmentName($digitsArray, $numberSegment, $digitSegmentsCount);

    /**
     * @param $digitsArray array
     * @param $digitsCount int
     * @return string
     */
    abstract public function getDecimalSegmentName($digitsArray, $digitsCount);
}
