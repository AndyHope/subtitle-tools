<?php

namespace App\Subtitles\Transformers;

use App\Subtitles\ContainsGenericCues;
use App\Subtitles\PlainText\GenericSubtitleCue;
use SjorsO\Pinyin\Pinyin;

class PinyinUnderChineseTransformer extends CueTransformer
{
    protected $pinyin;

    public function __construct(Pinyin $pinyin)
    {
        $this->pinyin = $pinyin;
    }

    /**
     * Adds pinyin underneath every line that has Chinese
     *
     * @param ContainsGenericCues $subtitle
     *
     * @return bool False if no valid transformations have happened, true otherwise
     */
    public function transformCues(ContainsGenericCues $subtitle): bool
    {
        $hasChangedSomething = false;

        foreach ($subtitle->getCues(false) as $cue) {
            $cue->alterLines(function ($line, $index) use (&$hasChangedSomething) {
                $converted = $this->pinyin->convert($line);

                if ($converted !== $line) {
                    $hasChangedSomething = true;

                    return $line."\n".$converted;
                }

                return $line;
            });
        }

        return $hasChangedSomething;
    }

    public function transformCue(GenericSubtitleCue $cue): bool
    {
        throw new \RuntimeException('Not implemented');
    }

    public function transformLines(array $lines): array
    {
        throw new \RuntimeException('Not implemented');
    }
}
