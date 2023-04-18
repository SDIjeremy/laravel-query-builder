<?php

namespace Spatie\QueryBuilder\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\Exceptions\InvalidAppendQuery;

trait AppendsAttributesToResults
{
    /** @var \Illuminate\Support\Collection */
    protected $allowedAppends;

    /** @var \Illuminate\Support\Collection */
    protected $withAppends;

    public function allowedAppends($appends): self
    {
        $appends = is_array($appends) ? $appends : func_get_args();

        $this->allowedAppends = collect($appends);

        $this->ensureAllAppendsExist();

        return $this;
    }

    public function withAppends($appends): self
    {
        $appends = is_array($appends) ? $appends : func_get_args();

        $this->withAppends = collect($appends);

        return $this;
    }

    protected function addAppendsToResults(Collection $results)
    {
        return $results->each(function (Model $result) {
            return $result->append($this->request->appends()->merge($this->withAppends ?? [])->toArray());
        });
    }

    protected function addAppendsToCursor($results)
    {
        return $results->each(function (Model $result) {
            return $result->append($this->request->appends()->merge($this->withAppends ?? [])->toArray());
        });
    }

    protected function ensureAllAppendsExist()
    {
        $appends = $this->request->appends();

        $diff = $appends->diff($this->allowedAppends);

        if ($diff->count()) {
            throw InvalidAppendQuery::appendsNotAllowed($diff, $this->allowedAppends);
        }
    }
}
