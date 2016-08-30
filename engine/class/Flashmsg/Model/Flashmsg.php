<?php

namespace Flashmsg\Model;

class Flashmsg extends \Enp\Db\Model
{

	public function getDbTableObject()
	{
		return \Enp\Db\TableFactory::get('flashmsg');
	}

}