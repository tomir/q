<?php

namespace School\Model;

/**
 * Description of UserSession
 *
 * @author tomi_weber
 */
class School extends \Db\Model
{
    public function getDbTable()
    {
        return \Db\TableFactory::get('szkoly');
    }

    public function setPrimaryColumn()
    {
        return 'szkoly_id';
    }

//put your code here
}
