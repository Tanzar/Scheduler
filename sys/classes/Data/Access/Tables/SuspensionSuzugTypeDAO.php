<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Container as Container;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;

/**
 * Description of SuspensionSuzugTypeDAO
 *
 * @author Tanzar
 */
class SuspensionSuzugTypeDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }
    
    protected function setDefaultTable(): string {
        return 'suspension_suzug_type';
    }
    
    public function getActiveByGroupId(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suspension_suzug_type')->where('active', 1)
                ->and()->where('id_suspension_suzug_group', $id);
        return $this->select($sql);
    }
    
    public function getByGroupId(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suspension_suzug_type')->where('id_suspension_suzug_group', $id);
        return $this->select($sql);
    }
    
    public function enable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('suspension_suzug_type', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
    
    public function  disable(int $id) {
        $sql = new MysqlBuilder();
        $sql->update('suspension_suzug_type', 'id', $id)->set('active', 0);
        $this->update($sql);
    }
}
