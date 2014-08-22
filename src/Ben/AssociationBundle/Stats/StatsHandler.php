<?php

namespace Ben\AssociationBundle\Stats;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;

class StatsHandler
{
    private $em;

    private $table;
    private $timeColumn;
    private $dataColumn;
    private $period;
    private $dateRange;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function processData() {
        $data = $this->em->getRepository('BenUserBundle:User')->{$this->getFunctionName()}();
        return $data;
    }

    /* setters */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }
    public function setTimeColumn($timeColumn)
    {
        $this->timeColumn = $timeColumn;
        return $this;
    }
    public function setDataColumn($dataColumn)
    {
        $this->dataColumn = $dataColumn;
        return $this;
    }
    public function setPeriod($Period)
    {
        $this->Period = $Period;
        return $this;
    }
    public function setDateRange($dateRange)
    {
        $this->dateRange = $dateRange;
        return $this;
    }
    public function getFunctionName()
    {
        return 'statsBy'.ucfirst($this->dataColumn);
    }
}