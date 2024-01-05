<?php

declare(strict_types=1);

namespace App\Factory;

use App\Models\Type;
use DateTime;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;

final class ProjectDynamicFactory
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
        $this->comments = $comments;
        $this->effectiveValue = $effectiveValue;
    }

    public static function make($map, $row)
    : ProjectDynamicFactory {
        return new self(
            self::getTypeId($map, $row[0]),
            $row[1],
            isset($row[2]) ? Date::excelToDateTimeObject($row[2]) : null,
            isset($row[3]) ? Date::excelToDateTimeObject($row[3]) : null,
            isset($row[4]) ? Date::excelToDateTimeObject($row[4]) : null,
            isset($row[5]) ? self::getBool($row[5]) : null,
            isset($row[6]) ? self::getBool($row[6]) : null,
            isset($row[7]) ? self::getBool($row[7]) : null,
            isset($row[8]) ? self::getBool($row[8]) : null,
            $row[9] ?? null,
            $row[10] ?? null,
            $row[11] ?? null,
            $row[12] ?? null,
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
