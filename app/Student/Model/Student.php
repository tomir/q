<?php

namespace Student\Model;

/**
 * Description of UserSession
 *
 * @author tomi_weber
 */
class Student extends \Db\Model
{
    public function getDbTable()
    {
        return \Db\TableFactory::get('uczen');
    }

    public function setPrimaryColumn()
    {
        return 'uczen_id';
    }

//put your code here
}
