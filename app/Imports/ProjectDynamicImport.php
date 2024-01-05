<?php

namespace App\Imports;

use App\Factory\ProjectDynamicFactory;
use App\Factory\ProjectFactory;
use App\Models\FailedRow;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Validators\Failure;

class ProjectDynamicImport implements ToCollection, WithValidation, SkipsOnFailure, WithStartRow, WithEvents
{
    use RegistersEventListeners;

    private const STATIC_ROW_NUMBER = 12;
    private Task $task;
    private static array $headings;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    : void {
        $dynamicHeadings = $this->makeRowsMap(self::$headings)['dynamic'];

        foreach ($collection as $row) {
            if ($row->isEmpty()) {
                continue;
            }
            $types = Type::all();
            $typesMap = $this->makeTypesMap($types);
            $map = $this->makeRowsMap($row);

            $projectFactory = ProjectDynamicFactory::make($typesMap, $map['static']);

            $project = Project::updateOrCreate([
                'type_id'          => $projectFactory->getValues()['type_id'],
                'title'            => $projectFactory->getValues()['title'],
                'date_of_creation' => $projectFactory->getValues()['date_of_creation'],
                'contracted_at'    => $projectFactory->getValues()['contracted_at'],
            ], $projectFactory->getValues());

            if (!isset($map['dynamic'])) {
                continue;
            }

            foreach ($map['dynamic'] as $key => $value) {
                Payment::create([
                    'project_id' => $project->id,
                    'title'      => $dynamicHeadings[$key],
                    'value'      => $value,
                ]);
            }
        }
    }

    private function makeTypesMap(Collection $types)
    : array {
        $map = [];
        foreach ($types as $type) {
            $map[$type->title] = $type->id;
        }

        return $map;
    }

    private function makeRowsMap(Collection|array $row)
    : array {
        $static = [];
        $dynamic = [];
        foreach ($row as $key => $value) {
            if ($value) {
                $key > self::STATIC_ROW_NUMBER
                    ? $dynamic[$key] = $value
                    : $static[$key] = $value;
            }
        }
        return [
            'static'  => $static,
            'dynamic' => $dynamic,
        ];
    }

    public function rules()
    : array
    {
        return array_replace([
            '0'  => 'required|string',
            '1'  => 'required|string',
            '2'  => 'required|int',
            '3'  => 'required|int',
            '4'  => 'nullable|int',
            '5'  => 'nullable|string',
            '6'  => 'nullable|string',
            '7'  => 'nullable|int',
            '8'  => 'nullable|string',
            '9'  => 'nullable|int',
            '10' => 'nullable|int',
            '11' => 'nullable|string',
            '12' => 'nullable|numeric',
        ], $this->getDynamicValidation());
    }

    public function onFailure(Failure ...$failures)
    : void {
        processFailures($failures, $this->attributesMap(), $this->task);
    }

    public function customValidationMessages()
    : array
    {
        return [
            '4.string' => 'Это поле должно быть числом',
        ];
    }

    private function attributesMap()
    : array
    {
        return array_replace([
            '0'  => 'Тип',
            '1'  => 'Наименование',
            '2'  => 'Дата создания',
            '3'  => 'Подписание договора',
            '4'  => 'Дедлайн',
            '5'  => 'Сетевик',
            '6'  => 'Сдача в срок',
            '7'  => 'Наличие аутсорсинга',
            '8'  => 'Наличие инвесторов',
            '9'  => 'Количество участников',
            '10' => 'Количество услуг',
            '11' => 'Комментарий',
            '12' => 'Значение эффективности',
        ], $this->makeRowsMap(self::$headings)['dynamic']);
    }

    public function startRow()
    : int
    {
        return 2;
    }

    public static function beforeSheet(BeforeSheet $event)
    : void {
        self::$headings = $event->getSheet()->getDelegate()->toArray()[0];
    }

    private function getDynamicValidation()
    : array
    {
        $headers = $this->makeRowsMap(self::$headings)['dynamic'];
        foreach ($headers as $key => $value) {
            $headers[$key] = 'required|integer';
        }
        return $headers;
    }
}
