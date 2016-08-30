<?php

namespace StudentAnswer\Model;

/**
 * Description of UserSession
 *
 * @author tomi_weber
 */
class StudentAnswer extends \Db\Model {
    public function getDbTable() {
        return \Db\TableFactory::get('uczen_odpowiedzi');
    }

    public function setPrimaryColumn() {
        return 'uczen_odpowiedzi_id';
    }

//put your code here
}
