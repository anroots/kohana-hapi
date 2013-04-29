<?php defined('SYSPATH') or die('No direct script access.');
interface Kohana_ORM_Interface_Searchable
{

    /**
     * Apply search conditions to the ORM model.
     *
     * Used to restrict the return dataset of a find_all() query, usually with plain ->where() clauses.
     *
     * @param string $query
     * @return ORM
     */
    public function search($query);
}