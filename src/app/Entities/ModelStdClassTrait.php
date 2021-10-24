<?php

namespace App\Entities;

trait ModelStdClassTrait
{

    /**
     * Convert model to stdObject.
     *
     * @param  array $fill
     * @return stdObject
     */
    public function toStd($fill = ['*'], $add_table_name = false)
    {
        // backup visible
        $visible = $this->visible;

        // if ($fill == ['*']) $fill = array_keys($this->getAttributes());
        if ($fill == ['*']) $fill = [];

        // make sure we get all the fields we need
        $this->setVisible($fill);

        $std = (object) $this->attributesToArray();

        // restore visible
        $this->setVisible($visible);

        if ($add_table_name)
        {
            $std->TableName = $this->getTable();
        }

        return $std;
    }

    /**
     * Make a model from stdObject.
     *
     * @param  stdClass $std
     * @param  array    $fill
     * @param  boolean  $exists
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function newFromStd(\stdClass $std, $fill = ['*'], $exists = false)
    {
        $instance = new static;

        $values = ($fill == ['*'])
            ? (array) $std
            : array_intersect_key( (array) $std, array_flip($fill));

        // fill attributes and original arrays
        $instance->setRawAttributes($values, true);

        $instance->exists = $exists;

        return $instance;
    }
}