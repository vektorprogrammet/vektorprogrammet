<?php

namespace App\Repository\Eloquent;

use App\Models\Semester;
use App\Repository\Contract\SemesterRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Eloquent implementation of SemesterRepositoryInterface.
 */
class SemesterRepository implements SemesterRepositoryInterface
{
    /**
     * Create query builder for all semesters ordered by age.
     * Note: For Laravel, this returns an Eloquent query builder instance.
     *
     * @return Builder
     */
    public function queryForAllSemestersOrderedByAge()
    {
        return Semester::query()
            ->orderBy('year', 'desc')
            ->orderByRaw("CASE WHEN semester_time = 'Vår' THEN 1 WHEN semester_time = 'Høst' THEN 2 END");
    }

    /**
     * Find all semesters ordered by age.
     *
     * @return Semester[]
     */
    public function findAllOrderedByAge(): array
    {
        return $this->queryForAllSemestersOrderedByAge()->get()->toArray();
    }

    /**
     * Find current semester.
     *
     * @return Semester|null
     */
    public function findCurrentSemester(): ?Semester
    {
        $now = Carbon::now();
        $year = (string) $now->year;

        // Determine semester time based on current month
        // Vår (Spring): January - July (01-07)
        // Høst (Fall): August - December (08-12)
        $month = $now->month;
        $semesterTime = ($month >= 1 && $month <= 7) ? 'Vår' : 'Høst';

        return Semester::where('year', $year)
            ->where('semester_time', $semesterTime)
            ->first();
    }

    /**
     * Find or create current semester.
     *
     * @return Semester
     */
    public function findOrCreateCurrentSemester(): Semester
    {
        $semester = $this->findCurrentSemester();

        if ($semester === null) {
            $now = Carbon::now();
            $year = (string) $now->year;
            $month = $now->month;
            $semesterTime = ($month >= 1 && $month <= 7) ? 'Vår' : 'Høst';

            $semester = new Semester();
            $semester->year = $year;
            $semester->semester_time = $semesterTime;
            $semester->save();
        }

        return $semester;
    }

    /**
     * Find semester by time and year.
     *
     * @param string $semesterTime
     * @param string $year
     * @return Semester|null
     */
    public function findByTimeAndYear(string $semesterTime, string $year): ?Semester
    {
        return Semester::where('semester_time', $semesterTime)
            ->where('year', $year)
            ->first();
    }

    /**
     * Get next active semester.
     *
     * @param Semester $semester
     * @return Semester|null
     */
    public function getNextActive(Semester $semester): ?Semester
    {
        $currentSemester = $this->findOrCreateCurrentSemester();
        if ($semester->id === $currentSemester->id) {
            return null;
        }

        if ($semester->semester_time === 'Høst') {
            // Next is Vår of next year
            $nextYear = (string) ((int) $semester->year + 1);
            return $this->findByTimeAndYear('Vår', $nextYear);
        } else {
            // Next is Høst of same year
            return $this->findByTimeAndYear('Høst', $semester->year);
        }
    }
}

