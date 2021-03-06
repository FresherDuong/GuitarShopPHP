<?php
class Model
{
    //Satus of class
    protected $connection = null;
    protected $statement = null;

    //Config
    protected $host = '';
    protected $user = '';
    protected $password = '';
    protected $db_name = '';

    //Variable
    protected $table = '';
    protected $limit = 10;
    protected $offset = 0;

    public function __construct()
    {
        require_once('Config.php');
        $config = Configuration::getConfig();
        $this->host = $config['host'];
        $this->user = $config['user'];
        $this->password = $config['password'];
        $this->db_name = $config['db_name'];
        $this->connect();
    }

    protected function connect()
    {
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->db_name);
        // Check connection
        if ($this->connection->connect_errno) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    protected function resetQuery(){
        $this->table = '';
        $this->limit = 10;
        $this->offset = 0;
    }

    public function table($tableName)
    {
        $this->table = $tableName;
        return $this;
    }

    public function limit($limit){
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset){
        $this->offset = $offset;
        return $this;
    }

    // $db->table('tableName')->insert(param: ['key'=>'value'])
    public function insert($data = [])
    {
        $fields = implode(',', array_keys($data));
        $valueQuestionMark = implode(',', array_fill(0, count($data), '?'));
        $values =array_values($data);

        $sql = "INSERT INTO $this->table($fields) VALUES ($valueQuestionMark)";
        $this->statement = $this->connection->prepare($sql);
        $this->statement->bind_param(str_repeat('s',count($data)), ...$values);
        $this->statement->execute();
        $this->resetQuery();

        return $this->statement->affected_rows;
    }

    public function insertAndReturnId($data = [])
    {
        $fields = implode(',', array_keys($data));
        $valueQuestionMark = implode(',', array_fill(0, count($data), '?'));
        $values =array_values($data);

        $sql = "INSERT INTO $this->table($fields) VALUES ($valueQuestionMark)";
        $this->statement = $this->connection->prepare($sql);
        $this->statement->bind_param(str_repeat('s',count($data)), ...$values);
        $this->statement->execute();
        $this->resetQuery();

        if($this->statement->affected_rows > 0) {
            return $this->statement->insert_id;
        }

        return 0;
    }

    // $db->table('tableName')->limit()->offset()->findAll()
    public function findAll()
    {
        $sql = "SELECT * FROM $this->table LIMIT ? OFFSET ?";
        $this->statement = $this->connection->prepare($sql);
        $this->statement->bind_param('ii',$this->limit, $this->offset);
        $this->statement->execute();
        $this->resetQuery();

        $result = $this->statement->get_result();
        $returnData = [];
        while ($row = $result->fetch_object()){
            $returnData[] = $row;
        }
        return $returnData;
    }

    public function findSomeFields($fields = [])
    {
        $selectedFields = implode(',', $fields);
        $sql = "SELECT $selectedFields FROM $this->table LIMIT ? OFFSET ?";
        $this->statement = $this->connection->prepare($sql);
        $this->statement->bind_param('ii',$this->limit, $this->offset);
        $this->statement->execute();
        $this->resetQuery();

        $result = $this->statement->get_result();
        $returnData = [];
        while ($row = $result->fetch_object()){
            $returnData[] = $row;
        }
        return $returnData;
    }

    public function updateById($id, $data = [])
    {
        $keyValue = [];
        foreach ($data as $key=>$value){
            $keyValue[] = $key.' = ?';
        }

        $setFields = implode(',',$keyValue);
        $values = array_values($data);
        $values[] = $id;
        $dataType = str_repeat('s',count($data)).'i';

        $sql = "UPDATE $this->table SET $setFields WHERE  id = ?";
        $this->statement = $this->connection->prepare($sql);
        $this->statement->bind_param($dataType, ...$values);
        $this->statement->execute();
        $this->resetQuery();
        return $this->statement->affected_rows;
    }

    // $db->table('tableName')->delete($id)
    public function deleteById($id)
    {
        $sql = "DELETE FROM $this->table WHERE id = ?";
        $this->statement = $this->connection->prepare($sql);
        $this->statement->bind_param('i',$id);
        $this->statement->execute();
        $this->resetQuery();
        return $this->statement->affected_rows;
    }

    public function findWhere($conditions = [])
    {
        $dem = 0;
        $sql = "SELECT * FROM $this->table ";
        foreach ($conditions as $key => $value){
            $dem > 0 ? $sql.= "AND ".$key." = ? " : $sql.= "WHERE ".$key." = ? " ;
            $dem++;
        }
        $strConditions = array_values($conditions);


        $this->statement = $this->connection->prepare($sql);
        $this->statement->bind_param(str_repeat('s',count($conditions)), ...$strConditions);
        $this->statement->execute();
        $this->resetQuery();

        $result = $this->statement->get_result();
        $returnData = [];
        while ($row = $result->fetch_object()){
            $returnData[] = $row;
        }
        return $returnData;
    }

    public function findAllFilteredProducts($thuonghieu_id, $loaidan_id, $order)
    {
        if($order != 'ALLPRICE') {
            $sql = "SELECT * FROM $this->table WHERE thuonghieu_id = ? OR loaidan_id = ? ORDER BY giasp $order LIMIT ? OFFSET ?";
        } else {
            $sql = "SELECT * FROM $this->table WHERE thuonghieu_id = ? OR loaidan_id = ? LIMIT ? OFFSET ?";
        }

        $this->statement = $this->connection->prepare($sql);
        $this->statement->bind_param('iiii',$thuonghieu_id, $loaidan_id, $this->limit, $this->offset);
        $this->statement->execute();
        $this->resetQuery();

        $result = $this->statement->get_result();
        $returnData = [];
        while ($row = $result->fetch_object()){
            $returnData[] = $row;
        }
        return $returnData;
    }

    public function countRecord()
    {
        $sql = "SELECT COUNT(id) AS totalRecord FROM $this->table LIMIT ? OFFSET ?";
        $this->statement = $this->connection->prepare($sql);
        $this->statement->bind_param('ii',$this->limit, $this->offset);
        $this->statement->execute();
        $this->resetQuery();

        $result = $this->statement->get_result();
        $returnData = [];
        while ($row = $result->fetch_object()){
            $returnData[] = $row;
        }
        return $returnData[0]->totalRecord;
    }

    public function findLikeAnOrder($field, $text, $order) {
        $text = '%'.$text.'%';

        if($order != 'ALLPRICE') {
            $sql = "SELECT * FROM $this->table WHERE $field LIKE ? ORDER BY giasp $order LIMIT ? OFFSET ?";
        } else {
            $sql = "SELECT * FROM $this->table WHERE $field LIKE ? LIMIT ? OFFSET ?";
        }

        $this->statement = $this->connection->prepare($sql);
        $this->statement->bind_param('sii',$text, $this->limit, $this->offset);
        $this->statement->execute();
        $this->resetQuery();

        $result = $this->statement->get_result();
        $returnData = [];
        while ($row = $result->fetch_object()){
            $returnData[] = $row;
        }
        return $returnData;
    }

    public function findLoaiDanThuongHieu() {
        $sql = "select loaidan.ten as tenLoaiDan, loaidan.id as idLoaiDan, thuonghieu.ten as tenThuongHieu, thuonghieu.id as idThuongHieu from thuonghieu
        inner join thuonghieuloai on thuonghieu.id = thuonghieuloai.thuonghieu_id
        inner join loaidan on thuonghieuloai.loaidan_id = loaidan.id";

        $this->statement = $this->connection->prepare($sql);
        $this->statement->execute();
        $this->resetQuery();

        $result = $this->statement->get_result();
        $returnData = [];
        while ($row = $result->fetch_object()){
            $returnData[] = $row;
        }
        return $returnData;
    }

    public function findAllFilteredProductsforAnd($thuonghieu_id, $loaidan_id, $order)
    {
        if($order != 'ALLPRICE') {
            $sql = "SELECT * FROM $this->table WHERE thuonghieu_id = ? AND loaidan_id = ? ORDER BY giasp $order LIMIT ? OFFSET ?";
        } else {
            $sql = "SELECT * FROM $this->table WHERE thuonghieu_id = ? AND loaidan_id = ? LIMIT ? OFFSET ?";
        }

        $this->statement = $this->connection->prepare($sql);
        $this->statement->bind_param('iiii',$thuonghieu_id, $loaidan_id, $this->limit, $this->offset);
        $this->statement->execute();
        $this->resetQuery();

        $result = $this->statement->get_result();
        $returnData = [];
        while ($row = $result->fetch_object()){
            $returnData[] = $row;
        }
        return $returnData;
    }

    public function findAllFilteredProductsForSort($thuonghieu_id_int, $loaidan_id_int, $order)
    {
        $thuonghieu_id = strval($thuonghieu_id_int);
        $loaidan_id = strval($loaidan_id_int);

        if($order != 'ALLPRICE') {
            $sql = "SELECT * FROM $this->table WHERE thuonghieu_id LIKE ? AND loaidan_id LIKE ? ORDER BY giasp $order LIMIT ? OFFSET ?";
        } else {
            $sql = "SELECT * FROM $this->table WHERE thuonghieu_id LIKE ? AND loaidan_id LIKE ? LIMIT ? OFFSET ?";
        }

        $this->statement = $this->connection->prepare($sql);
        $this->statement->bind_param('ssii',$thuonghieu_id, $loaidan_id, $this->limit, $this->offset);
        $this->statement->execute();
        $this->resetQuery();

        $result = $this->statement->get_result();
        $returnData = [];
        while ($row = $result->fetch_object()){
            $returnData[] = $row;
        }
        return $returnData;
    }

}

?>