<?php 

namespace App;
use Aura\SqlQuery\QueryFactory;
use PDO;

class QueryBuilder {
    protected $pdo;
    protected $queryFactory;

    public function __construct(PDO $pdo, QueryFactory $factory)
    {
        $this->pdo = $pdo;
        $this->queryFactory = $factory;
        
    }

    public function getAll($table) 
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(["*"])->from($table);
        $sth = $this->pdo->prepare($select->getStatement());

        // bind the values and execute
        $sth->execute($select->getBindValues());
        

        // get the results back as an associative array
        
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllPosts($itemsPerPage, $currentPage)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(["posts.*", "users.image","users.username"])
            ->from("posts")
            ->leftJoin('users', 'posts.user_id = users.id')
            ->orderBy(['id DESC'])
            ->limit($itemsPerPage)
            ->page($currentPage) ;
        $sth = $this->pdo->prepare($select->getStatement());

        // bind the values and execute
        $sth->execute($select->getBindValues());


        // get the results back as an associative array

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function rowsCount($table)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(["COUNT(*)"])->from($table);
        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]['COUNT(*)'];
    }

    public function getOne($table, $id) 
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(["*"])
            ->from($table)
            ->where('id = :id')
            ->bindValue('id',$id);
        $sth = $this->pdo->prepare($select->getStatement());
        // bind the values and execute
        $sth->execute($select->getBindValues());
        

        // get the results back as an associative array
        
        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($table, $data) 
    {
        $insert = $this->queryFactory->newInsert();

        $insert
            ->into($table)                   // INTO this table
            ->cols($data);

        $sth = $this->pdo->prepare($insert->getStatement());

        // bind the values and execute
        $sth->execute($insert->getBindValues());
    }

    public function update($table,$id,$data)
    {
        $update = $this->queryFactory->newUpdate();

        $update
            ->table($table)                  // update this table
            ->cols($data)
            ->where('id = :id')           // AND WHERE these conditions
            ->bindValue('id', $id);   // bind one value to a placeholder

        $sth = $this->pdo->prepare($update->getStatement());
        
        // bind the values and execute
        $sth->execute($update->getBindValues());
            
    }

    public function delete($table,$id) 
    {
        $delete = $this->queryFactory->newDelete();

        $delete->from($table)
            ->where('id = :id')
            ->bindValue('id',$id);
        
        $sth = $this->pdo->prepare($delete->getStatement());
        
        // bind the values and execute
        $sth->execute($delete->getBindValues());
    }



}