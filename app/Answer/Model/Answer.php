<?php

namespace Answer\Model;

/**
 * Description of UserSession
 *
 * @author tomi_weber
 */
class Answer extends \Db\Model
{
    public function getDbTable()
    {
        return \Db\TableFactory::get('odpowiedz');
    }

    public function setPrimaryColumn()
    {
        return 'id';
    }

//put your code here
}
