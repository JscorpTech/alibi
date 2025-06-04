<?php

namespace App\Services;

use App\Http\Helpers\ExceptionHelper;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

trait MainService
{
    public object $resource;
    public object $model;

    public function all(): AnonymousResourceCollection
    {
        return $this->resource::collection($this->model::all());
    }

    public function paginate(int $count = 10)
    {
        return $this->resource::paginate($this->model::paginate($count));
    }

    public function create($data)
    {
        return new $this->resource($this->model::create($data));
    }

    public function get($id)
    {
        try {
            return new $this->resource($this->model::findOrField($id, error: __('not:found')));
        } catch (\Throwable $e) {
            ExceptionHelper::sendError(__('not:found'));
        }
    }

    public function update($id, $data)
    {
        $class = $this->model::findOrField($id);
        $class->update($data);
        $class->save();

        return new $this->resource($class);
    }

    public function delete($id)
    {
        try {
            return $this->model::findOrField($id)->delete();
        } catch (\Throwable $e) {
            ExceptionHelper::sendError($e->getMessage());
        }
    }

    public function filter(array|string $keys)
    {
        return $this->resource::collection($this->model::query()->where($keys)->get());
    }

    public function filterPaginate(array|string $keys, $search = '', $lang = false, int $count = null, string $q = null)
    {
        $count = $count ?? Env::get('PAGE_SIZE');

        return $this->resource::paginate($this->model::query()->where($keys)->where(function ($query) use ($q, $search, $lang) {
            if ($lang) {
                $field = $search . '_' . App::getLocale();
            } else {
                $field = $search;
            }
            if ($q != null) {
                $query->where(DB::raw("LOWER($field)"), 'like', '%' . $q . '%');
            }
        })->paginate($count));
    }

    public function search($q)
    {
        return $q;
    }
}
