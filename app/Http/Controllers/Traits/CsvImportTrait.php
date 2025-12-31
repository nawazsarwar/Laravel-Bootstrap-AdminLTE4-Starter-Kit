<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SpreadsheetReader;

trait CsvImportTrait
{
    /**
     * Get the whitelist of allowed models for CSV import.
     * Only models in this list can be imported.
     *
     * @return array
     */
    protected function getAllowedModels(): array
    {
        return [
            'User',
            'Role',
            'Permission',
        ];
    }

    public function processCsvImport(Request $request)
    {
        try {
            $filename = $request->input('filename', false);
            if (!$filename || !preg_match('/^[a-zA-Z0-9_-]+\.csv$/', $filename)) {
                abort(400, 'Invalid filename');
            }

            $path = storage_path('app/csv_import/' . $filename);
            if (!file_exists($path) || !is_readable($path)) {
                abort(404, 'File not found');
            }

            $hasHeader = $request->input('hasHeader', false);

            $fields = $request->input('fields', false);
            if (!$fields || !is_array($fields)) {
                abort(400, 'Invalid fields');
            }
            $fields = array_flip(array_filter($fields));

            $modelName = $request->input('modelName', false);
            if (!$modelName || !in_array($modelName, $this->getAllowedModels())) {
                abort(400, 'Model not allowed for import');
            }

            $modelClass = "App\Models\\" . $modelName;
            if (!class_exists($modelClass)) {
                abort(400, 'Model class does not exist');
            }

            $model = new $modelClass();
            $fillable = $model->getFillable();

            // Validate that all fields are in the model's fillable array
            foreach (array_keys($fields) as $field) {
                if (!in_array($field, $fillable)) {
                    abort(400, "Field '{$field}' is not fillable for model {$modelName}");
                }
            }

            $reader = new SpreadsheetReader($path);
            $insert = [];

            foreach ($reader as $key => $row) {
                if ($hasHeader && $key == 0) {
                    continue;
                }

                $tmp = [];
                foreach ($fields as $header => $k) {
                    if (isset($row[$k]) && in_array($header, $fillable)) {
                        $tmp[$header] = $row[$k];
                    }
                }

                if (count($tmp) > 0) {
                    $insert[] = $tmp;
                }
            }

            $for_insert = array_chunk($insert, 100);

            // Use proper mass assignment instead of direct insert
            foreach ($for_insert as $insert_item) {
                foreach ($insert_item as $item) {
                    $modelClass::create($item);
                }
            }

            $rows  = count($insert);
            $table = Str::plural($modelName);

            File::delete($path);

            session()->flash('message', trans('global.app_imported_rows_to_table', ['rows' => $rows, 'table' => $table]));

            return redirect($request->input('redirect'));
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function parseCsvImport(Request $request)
    {
        $file = $request->file('csv_file');
        $request->validate([
            'csv_file' => 'mimes:csv,txt',
            'model' => ['required', 'string', 'in:' . implode(',', $this->getAllowedModels())],
        ]);

        $path      = $file->path();
        $hasHeader = $request->input('header', false) ? true : false;

        $reader  = new SpreadsheetReader($path);
        $headers = $reader->current();
        $lines   = [];

        $i = 0;
        while ($reader->next() !== false && $i < 5) {
            $lines[] = $reader->current();
            $i++;
        }

        $filename = Str::random(10) . '.csv';
        $file->storeAs('csv_import', $filename);

        $modelName = $request->input('model', false);
        if (!$modelName || !in_array($modelName, $this->getAllowedModels())) {
            abort(400, 'Model not allowed for import');
        }

        $fullModelName = "App\Models\\" . $modelName;
        if (!class_exists($fullModelName)) {
            abort(400, 'Model class does not exist');
        }

        $model     = new $fullModelName();
        $fillables = $model->getFillable();

        $redirect = url()->previous();

        $routeName = 'admin.' . strtolower(Str::plural(Str::kebab($modelName))) . '.processCsvImport';

        return view('csvImport.parseInput', compact('headers', 'filename', 'fillables', 'hasHeader', 'modelName', 'lines', 'redirect', 'routeName'));
    }
}
