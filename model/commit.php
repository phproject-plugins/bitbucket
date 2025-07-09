<?php

/**
 * @package  Bitbucket
 */

namespace Plugin\Bitbucket\Model;

class Commit extends \Model
{
    protected $_table_name = "bitbucket_commit";

    /**
     * Save commit, setting insert_date
     * @return Commit
     */
    public function save()
    {
        if (!$this->query && !$this->get("insert_date")) {
            $this->set("insert_date", date("Y-m-d H:i:s"));
        }

        return parent::save();
    }
}
