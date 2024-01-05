<?php

namespace App\Imports;

use App\Factory\ProjectFactory;
use App\Models\FailedRow;
use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class ProjectImport implements ToCollection, WithHeadingRow,
                               WithValidation, SkipsOnFailure
{

    private Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    : void {
        foreach ($collection as $row) {
//            dd($row);
            if (!isset($row['naimenovanie'])) {
                continue;
            }
            $types = Type::all();
            $typesMap = $this->makeTypesMap($types);

            $projectFactory = ProjectFactory::make($typesMap, $row);
            Project::updateOrCreate([
                'type_id'          => $projectFactory->getValues()['type_id'],
                'title'            => $projectFactory->getValues()['title'],
                'date_of_creation' => $projectFactory->getValues()['date_of_creation'],
                'contracted_at'    => $projectFactory->getValues()['contracted_at'],
            ], $projectFactory->getValues());
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

    public function rules()
    : array
    {
        return [
            'tip'                       => 'required|string',
            'naimenovanie'              => 'required|string',
            'data_sozdaniia'            => 'required|int',
            'podpisanie_dogovora'       => 'required|int',
            'dedlain'                   => 'nullable|int',
            'setevik'                   => 'nullable|string',
            'sdaca_v_srok'              => 'nullable|string',
            'nalicie_autsorsinga'       => 'nullable|string',
            'nalicie_investorov'        => 'nullable|string',
            'kolicestvo_ucastnikov'     => 'nullable|int',
            'kolicestvo_uslug'          => 'nullable|int',
            'vlozenie_v_pervyi_etap'    => 'nullable|int',
            'vlozenie_vo_vtoroi_etap'   => 'nullable|int',
            'vlozenie_v_tretii_etap'    => 'nullable|int',
            'vlozenie_v_cetvertyi_etap' => 'nullable|int',
            'kommentarii'               => 'nullable|string',
            'znacenie_effektivnosti'    => 'nullable|numeric',
        ];
    }


    public function onFailure(Failure ...$failures)
    : void {
        processFailures($failures, $this->attributesMap(), $this->task);
    }

    public function customValidationMessages()
    : array
    {
        return [
            'dedlain.string' => 'Это поле должно быть числом',
        ];
    }

    private function attributesMap()
    : array
    {
        return [
            'tip'                       => 'Тип',
            'naimenovanie'              => 'Наименование',
            'data_sozdaniia'            => 'Дата создания',
            'podpisanie_dogovora'       => 'Подписание договора',
            'dedlain'                   => 'Дедлайн',
            'setevik'                   => 'Сетевик',
            'sdaca_v_srok'              => 'Сдача в срок',
            'nalicie_autsorsinga'       => 'Наличие аутсорсинга',
            'nalicie_investorov'        => 'Наличие инвесторов',
            'kolicestvo_ucastnikov'     => 'Количество участников',
            'kolicestvo_uslug'          => 'Количество услуг',
            'vlozenie_v_pervyi_etap'    => 'Вложение в первый этап',
            'vlozenie_vo_vtoroi_etap'   => 'Вложение во второй этап',
            'vlozenie_v_tretii_etap'    => 'Вложение в третий этап',
            'vlozenie_v_cetvertyi_etap' => 'Вложение в четвертый этап',
            'kommentarii'               => 'Комментарий',
            'znacenie_effektivnosti'    => 'Значение эффективности',
        ];
    }
}
