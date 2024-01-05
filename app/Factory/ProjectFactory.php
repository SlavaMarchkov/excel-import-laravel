<?php

declare(strict_types=1);

namespace App\Factory;

use App\Models\Type;
use DateTime;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;

final class ProjectFactory
{
    private int $typeId;
    private string $title;
    private DateTime $dateOfCreation;
    private DateTime $contractedAt;
    private ?DateTime $deadline;
    private ?bool $isChain;
    private ?bool $isOnTime;
    private ?bool $hasOutsource;
    private ?bool $hasInvestors;
    private int $workerCount;
    private int $serviceCount;
    private int $paymentFirstStep;
    private int $paymentSecondStep;
    private int $paymentThirdStep;
    private int $paymentFourthStep;
    private string $comments;
    private string $effectiveValue;

    /**
     * @param $typeId
     * @param $title
     * @param $dateOfCreation
     * @param $contractedAt
     * @param $deadline
     * @param $isChain
     * @param $isOnTime
     * @param $hasOutsource
     * @param $hasInvestors
     * @param $workerCount
     * @param $serviceCount
     * @param $paymentFirstStep
     * @param $paymentSecondStep
     * @param $paymentThirdStep
     * @param $paymentFourthStep
     * @param $comments
     * @param $effectiveValue
     */
    public function __construct(
        $typeId,
        $title,
        $dateOfCreation,
        $contractedAt,
        $deadline,
        $isChain,
        $isOnTime,
        $hasOutsource,
        $hasInvestors,
        $workerCount,
        $serviceCount,
        $paymentFirstStep,
        $paymentSecondStep,
        $paymentThirdStep,
        $paymentFourthStep,
        $comments,
        $effectiveValue
    ) {
        $this->typeId = $typeId;
        $this->title = $title;
        $this->dateOfCreation = $dateOfCreation;
        $this->contractedAt = $contractedAt;
        $this->deadline = $deadline;
        $this->isChain = $isChain;
        $this->isOnTime = $isOnTime;
        $this->hasOutsource = $hasOutsource;
        $this->hasInvestors = $hasInvestors;
        $this->workerCount = $workerCount;
        $this->serviceCount = $serviceCount;
        $this->paymentFirstStep = $paymentFirstStep;
        $this->paymentSecondStep = $paymentSecondStep;
        $this->paymentThirdStep = $paymentThirdStep;
        $this->paymentFourthStep = $paymentFourthStep;
        $this->comments = $comments;
        $this->effectiveValue = $effectiveValue;
    }

    public static function make($map, $row)
    : ProjectFactory {
        return new self(
            self::getTypeId($map, $row['tip']),
            $row['naimenovanie'],
            isset($row['data_sozdaniia']) ? Date::excelToDateTimeObject($row['data_sozdaniia']) : null,
            isset($row['podpisanie_dogovora']) ? Date::excelToDateTimeObject($row['podpisanie_dogovora']) : null,
            isset($row['dedlain']) ? Date::excelToDateTimeObject($row['dedlain']) : null,
            isset($row['setevik']) ? self::getBool($row['setevik']) : null,
            isset($row['sdaca_v_srok']) ? self::getBool($row['sdaca_v_srok']) : null,
            isset($row['nalicie_autsorsinga']) ? self::getBool($row['nalicie_autsorsinga']) : null,
            isset($row['nalicie_investorov']) ? self::getBool($row['nalicie_investorov']) : null,
            $row['kolicestvo_ucastnikov'] ?? null,
            $row['kolicestvo_uslug'] ?? null,
            $row['vlozenie_v_pervyi_etap'] ?? null,
            $row['vlozenie_vo_vtoroi_etap'] ?? null,
            $row['vlozenie_v_tretii_etap'] ?? null,
            $row['vlozenie_v_cetvertyi_etap'] ?? null,
            $row['kommentarii'] ?? null,
            $row['znacenie_effektivnosti'] ?? null,
        );
    }

    public function getValues()
    : array
    {
        $properties = get_object_vars($this);
        $result = [];
        foreach ($properties as $key => $property) {
            $result[Str::snake($key)] = $property;
        }
        return $result;
    }

    private static function getTypeId(array $map, string $title)
    : int {
        return $map[$title] ?? Type::create(['title' => $title])->id;
    }

    private static function getBool(string $str)
    : bool {
        return $str == 'Да';
    }

}
