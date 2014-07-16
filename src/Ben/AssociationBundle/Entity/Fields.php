<?php

namespace Ben\AssociationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fields
 *
 * @ORM\Table(name="fields")
 * @ORM\Entity(repositoryClass="Ben\AssociationBundle\Entity\FieldsRepository")
 */
class Fields
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="table_name", type="string", length=255)
     */
    private $table_name;

    /**
     * @var string
     *
     * @ORM\Column(name="field_name", type="string", length=255)
     */
    private $field_name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="required", type="boolean")
     */
    private $required;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="nicename", type="string", length=255)
     */
    private $nicename;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set table_name
     *
     * @param string $tableName
     * @return Fields
     */
    public function setTableName($tableName)
    {
        $this->table_name = $tableName;
    
        return $this;
    }

    /**
     * Get table_name
     *
     * @return string 
     */
    public function getTableName()
    {
        return $this->table_name;
    }

    /**
     * Set field_name
     *
     * @param string $fieldName
     * @return Fields
     */
    public function setFieldName($fieldName)
    {
        $this->field_name = $fieldName;
    
        return $this;
    }

    /**
     * Get field_name
     *
     * @return string 
     */
    public function getFieldName()
    {
        return $this->field_name;
    }

    /**
     * Set required
     *
     * @param boolean $required
     * @return Fields
     */
    public function setRequired($required)
    {
        $this->required = $required;
    
        return $this;
    }

    /**
     * Get required
     *
     * @return boolean 
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Fields
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Get nicename
     *
     * @return string 
     */
    public function isVisible()
    {
        return ($this->visible) ? 1 : 0;
    }
    /**
     * Set position
     *
     * @param integer $position
     * @return Fields
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set nicename
     *
     * @param string $nicename
     * @return Fields
     */
    public function setNicename($nicename)
    {
        $this->nicename = $nicename;
    
        return $this;
    }

    /**
     * Get nicename
     *
     * @return string 
     */
    public function getNicename()
    {
        return $this->nicename;
    }
}
