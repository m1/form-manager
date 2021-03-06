<?php
namespace FormManager\Fields;

use FormManager\CollectionInterface;

class Duplicable extends Collection implements CollectionInterface
{
    public $field;

    protected $index = 0;
    protected $parentPath;

    public function __construct($children = null)
    {
        if (is_array($children)) {
            $this->field = new Collection($children);
        } else {
            $this->field = clone $children;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function add($key, $value = null)
    {
        $this->field->add($key, $value);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function load($value = null, $file = null)
    {
        if (($sanitizer = $this->sanitizer) !== null) {
            $value = $sanitizer($value);
        }

        $this->children = [];
        $this->index = 0;

        if ($value) {
            foreach ($value as $key => $value) {
                $child = $this->createDuplicate();

                $child->load($value, isset($file[$key]) ? $file[$key] : null);
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function val($value = null)
    {
        if ($value === null) {
            return parent::val();
        }

        $this->children = [];

        if ($value) {
            foreach ($value as $key => $value) {
                $child = isset($this->children[$key]) ? $this->children[$key] : $this->createDuplicate($key);

                $child->val($value);
            }
        }

        return $this;
    }

    /**
     * Creates a new cloned child
     *
     * @param null|string $index  The copy index
     * @param boolean     $insert Set false to return the copy without insert into
     *
     * @return Field The cloned field
     */
    protected function createDuplicate($index = null, $insert = true)
    {
        $child = clone $this->field;

        if ($index === null) {
            $index = $this->index++;
        }

        if ($insert) {
            $this->children[$index] = $child;
        }

        $child->setParent($this);
        $this->prepareChild($child, $index, $this->parentPath);

        return $child;
    }

    /**
     * Returns a new cloned child without insert into
     *
     * @return Field The cloned field
     */
    public function getDuplicate($index = '::n::')
    {
        return $this->createDuplicate($index, false);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareChildren($parentPath = null)
    {
        $this->parentPath = $parentPath;

        foreach ($this->children as $key => $child) {
            $this->prepareChild($child, $key, $this->parentPath);
        }
    }
}
