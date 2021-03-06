<?php

namespace Scool\EbreEscoolModel;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Scool\EbreEscoolModel\Contracts\HasPeriods;
use Scool\EbreEscoolModel\Traits\Periodable;

/**
 * Class Study
 * @package Scool\EbreEscoolModel
 */
class Study extends EloquentModel implements HasPeriods
{
    use Periodable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'studies';

    /**
     * The primary key.
     *
     * @var string
     */
    protected $primaryKey = 'studies_id';

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key) ?: $this->getAttribute('studies_' .$key) ;
    }

    /**
     * Get the study periods.
     */
    public function periods()
    {
        return $this->belongsToMany(AcademicPeriod::class, 'studies_academic_periods',
            'studies_academic_periods_study_id', 'studies_academic_periods_academic_period_id');
    }

    /**
     * Get the study courses.
     */
    public function allCourses()
    {
        return $this->multiple() ? $this->allCoursesForMultiple() : $this->allCoursesForSingle();
    }

    /**
     * Get all courses for studies of type multiple.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    protected function allCoursesForMultiple() {
        return $this->belongsToMany(Course::class, 'course_studies',
            'course_studies_study_id','course_studies_course_id');
    }

    /**
     * Get all courses for studies of type single.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    protected function allCoursesForSingle() {
        return $this->hasMany(Course::class,'course_study_id','studies_id');
    }

    /**
     * Get only active studies related to department.
     */
    public function courses()
    {
        return $this->allCourses()->active();
    }


    /**
     * Get only active studies related to department.
     *
     * @param $period
     * @return mixed
     */
    public function coursesActiveOn($period)
    {
        return $this->allCourses()->activeOn($period);
    }

    /**
     * Get the study study modules.
     */
    public function modules()
    {
       // TODO though courses
    }

    /**
     * Check if study is multiple (like ASIX-DAM)
     * @return boolean
     */
    public function multiple() {
        return (boolean) $this->studies_multiple;
    }
}
