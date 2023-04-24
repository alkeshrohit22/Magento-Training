<?php

namespace Alk\Trainmodule\Block;

use Alk\Trainmodule\Model\ResourceModel\Training\Collection;
use Magento\Framework\View\Element\Template;

class Training extends Template
{
    private $collection;

    public function __construct(
        Template\Context $context,
        Collection $collection,
        array $data = []
    )
    {
        $this->collection = $collection;
        parent::__construct($context, $data);
    }

    public function getAllFormData()
    {
        return $this->collection;
    }

    public function getTrainigText()
    {
        return 'Hey Guys, I am in Magento Team.';
    }
}
