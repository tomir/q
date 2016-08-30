<?php

namespace Question\Model;

/**
 * Description of UserSession
 *
 * @author tomi_weber
 */
class Question extends \Db\Model
{
    public function getDbTable()
    {
        return \Db\TableFactory::get('pytanie');
    }

    public function setPrimaryColumn()
    {
        return 'pytanie_id';
    }

//put your code here
}
