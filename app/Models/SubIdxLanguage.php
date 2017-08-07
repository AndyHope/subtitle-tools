<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubIdxLanguage
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $sub_idx_id
 * @property string $index
 * @property string $language
 * @property string|null $filename
 * @property string|null $finished_at
 * @property string $filePath
 * @property-read \App\Models\SubIdx $subIdx
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereSubIdxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $has_error
 * @property string|null $started_at
 * @property-read mixed $file_path
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereHasError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereStartedAt($value)
 */
class SubIdxLanguage extends Model
{
    protected $fillable = ['index', 'language', 'filename', 'has_error', 'started_at', 'finished_at'];

    public function subIdx()
    {
        return $this->belongsTo('App\Models\SubIdx');
    }

    public function getFilePathAttribute()
    {
        return storage_disk_file_path($this->subIdx->store_directory . $this->filename);
    }

}
