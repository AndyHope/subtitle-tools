<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\ContainsGenericCues;
use App\Subtitles\LoadsGenericSubtitles;
use App\Subtitles\WithGenericCues;
use LogicException;
use SjorsO\TextFile\Facades\TextFileReader;
use App\Subtitles\PartialShiftsCues;
use App\Subtitles\ShiftsCues;
use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Subtitles\WithFileLines;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class WebVtt extends TextFile implements ShiftsCues, PartialShiftsCues, TransformsToGenericSubtitle, LoadsGenericSubtitles, ContainsGenericCues
{
    use WithFileLines, WithGenericCues;

    protected $extension = 'vtt';

    protected $loadingFromWebVttFile = false;

    protected $styleLines = [];

    public function __construct($source = null)
    {
        if ($source === null) {
            return;
        } elseif ($source instanceof TransformsToGenericSubtitle) {
            $this->loadGenericSubtitle($source->toGenericSubtitle());
        } else {
            $this->loadFile($source);
        }
    }

    public function loadFileFromFormat($file, $sourceFormat)
    {
        if ($sourceFormat === WebVtt::class) {
            $this->loadingFromWebVttFile = true;
        }

        return $this->loadFile($file);
    }

    public function loadFile($file)
    {
        $name = $file instanceof UploadedFile
            ? $file->getClientOriginalName()
            : $file;

        $this->originalFileNameWithoutExtension = pathinfo($name, PATHINFO_FILENAME);

        $this->filePath = $file instanceof UploadedFile
            ? $file->getRealPath()
            : $file;

        $lines = TextFileReader::getLines($this->filePath);
        // ensure parsing works properly on files missing the required trailing empty line
        $lines[] = '';

        $this->cues = [];

        $timingIndexes = [];

        for ($i = 0; $i < count($lines); $i++) {
            if (WebVttCue::isTimingString($lines[$i])) {
                $timingIndexes[] = $i;
            }
        }

        // If we are loading a WebVtt file, save all lines between the header and the first cue.
        if ($this->loadingFromWebVttFile && count($timingIndexes) > 0) {
            $this->styleLines = array_slice($lines, 1, $timingIndexes[0] - 1);
        }

        $timingIndexes[] = count($lines);

        for ($timingIndex = 0; $timingIndex < count($timingIndexes) - 1; $timingIndex++) {
            $newCue = new WebVttCue();

            if ($this->loadingFromWebVttFile) {
                $newCue->setIndex($lines[$timingIndexes[$timingIndex] - 1]);
            }

            $newCue->setTimingFromString($lines[$timingIndexes[$timingIndex]]);

            for ($lineIndex = $timingIndexes[$timingIndex] + 1; $lineIndex < $timingIndexes[$timingIndex + 1] - 1; $lineIndex++) {
                $line = $lines[$lineIndex];

                // Skip to the next timing index if the line is empty, or if the
                // line is a WebVtt comment.
                //
                // see: https://developer.mozilla.org/en-US/docs/Web/API/WebVTT_API#WebVTT_comments
                if (trim($line) === '' || starts_with('NOTE ', $line)) {
                    $lineIndex = $timingIndexes[$timingIndex + 1] - 1;

                    continue;
                }

                $newCue->addLine($line);
            }

            $this->AddCue($newCue);
        }

        return $this->removeEmptyCues()->removeDuplicateCues();
    }

    public static function isThisFormat($file)
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $lines = TextFileReader::getLines($filePath);

        if (count($lines) === 0) {
            return false;
        }

        // First line starting with WEBVTT is always a WebVtt file
        if (strpos(trim($lines[0]), 'WEBVTT') === 0) {
            return true;
        }

        return false;
    }

    public function shift($ms)
    {
        foreach ($this->cues as $cue) {
            $cue->shift($ms);
        }

        return $this;
    }

    public function shiftPartial($fromMs, $toMs, $ms)
    {
        if ($fromMs > $toMs || $ms == 0) {
            return $this;
        }

        foreach ($this->cues as $cue) {
            if ($cue->getStartMs() >= $fromMs && $cue->getStartMs() <= $toMs) {
                $cue->shift($ms);
            }
        }

        return $this;
    }

    /**
     * @return GenericSubtitle
     */
    public function toGenericSubtitle()
    {
        $generic = new GenericSubtitle();

        $generic->setFilePath($this->filePath);

        $generic->setFileNameWithoutExtension($this->originalFileNameWithoutExtension);

        foreach ($this->getCues(false) as $cue) {
            $newGenericCue = new GenericSubtitleCue();

            $newGenericCue->setTiming(
                $cue->getStartMs(),
                $cue->getEndMs()
            );

            $newGenericCue->setLines($cue->getLines());

            $generic->addCue($newGenericCue);
        }

        return $generic;
    }

    public function loadGenericSubtitle(GenericSubtitle $genericSubtitle)
    {
        $this->setFilePath($genericSubtitle->getFilePath());

        $this->setFileNameWithoutExtension($genericSubtitle->getFileNameWithoutExtension());

        foreach ($genericSubtitle->getCues() as $genericCue) {
            $this->addCue($genericCue);
        }

        return $this;
    }

    public function addCue($cue)
    {
        if ($cue instanceof WebVttCue) {
            $this->cues[] = $cue;
        } elseif ($cue instanceof GenericSubtitleCue) {
            $this->cues[] = new WebVttCue($cue);
        } else {
            throw new LogicException('Invalid cue');
        }

        return $this;
    }

    public function getContentLines()
    {
        $lines = [
            'WEBVTT - https://subtitletools.com',
        ];

        if (head($this->styleLines) !== '') {
            $lines[] = '';
        }

        $lines = array_merge($lines, $this->styleLines);

        if (last($lines) !== '') {
            $lines[] = '';
        }

        foreach ($this->getCues() as $cue) {
            $lines = array_merge($lines, $cue->toArray());
        }

        return $lines;
    }
}
