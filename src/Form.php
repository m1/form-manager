<?php
namespace FormManager;

use FormManager\Traits\CollectionTrait;
use FormManager\Traits\ValidationTrait;
use FormManager\Traits\VarsTrait;

use Iterator;
use ArrayAccess;

class Form extends Element implements Iterator, ArrayAccess, InputInterface
{
    use CollectionTrait;
    use ValidationTrait;
    use VarsTrait;

    protected $name = 'form';
    protected $close = true;

    /**
     * Load the form values from global GET, POST, FILES values
     *
     * @param array $get
     * @param array $post
     * @param array $file
     *
     * @return $this
     */
    public function loadFromGlobal (array $get = array(), array $post = array(), array $file = array())
    {
        if (func_num_args() === 0) {
            $get = $_GET;
            $post = $_POST;
            $file = $_FILES;
        }

        $value = ($this->attr('method') === 'post') ? $post : $get;

        return $this->load($value, $file);
    }

    /**
     * {@inheritDoc}
     */
    public function html($html = null)
    {
        if ($html === null) {
            return $this->html.$this->childrenToHtml();
        }

        return parent::html($html);
    }

    /**
     * {@inheritDoc}
     */
    public function id($id = null)
    {
        return $this->attr('id', $id);
    }
}
