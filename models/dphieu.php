<?php
class dphieu{
    public $Id;
    public $ten_dphieu;
    public $dia_chi;


    function __construct($Id,$ten_dphieu,$dia_chi)
    {
        $this->Id=$Id;
        $this->ten_dphieu=$ten_dphieu;
        $this->dia_chi=$dia_chi;

    }
    static function all()
    {
        $list = [];
        $db =DB::getInstance();
        $reg = $db->query('select *from dphieu');
        foreach ($reg->fetchAll() as $item){
            $list[] =new dphieu($item['Id'],$item['ten_dphieu'],$item['dia_chi']);
        }
        return $list;
    }
    static function find($Id)
    {
        $db = DB::getInstance();
        $req = $db->prepare('SELECT * FROM dphieu WHERE Id = :Id');
        $req->execute(array('Id' => $Id));
        
        $item = $req->fetch();
        if (isset($item['Id'])) {
            return new dphieu($item['Id'],$item['ten_dphieu'],$item['dia_chi']);
        }
        return null;
    }
    static function add($Id,$ten_dphieu,$dia_chi)
    {
        $db =DB::getInstance();
        $reg =$db->query('INSERT INTO dphieu(Id,ten_dphieu,dia_chi) VALUES ("'.$Id.'","'.$ten_dphieu.'","'.$dia_chi.'")');
        header('location:index.php?controller=dphieu&action=index');
    }
    static function update($Id,$ten_dphieu,$dia_chi)
    {
        $db =DB::getInstance();
        $reg =$db->query('UPDATE dphieu SET ten_dphieu ="'.$ten_dphieu.'",dia_chi="'.$dia_chi.'" WHERE Id='.$Id);
        header('location:index.php?controller=dphieu&action=index');
    }
      
    static function  delete($Id){
        $db =DB::getInstance();
        $reg =$db->query('DELETE FROM dphieu WHERE Id='.$Id);
        header('location:index.php?controller=dphieu&action=index');
    }
    
    static function filterByStatus($status, $start, $recordsPerPage) {
        $list = [];
        $db = DB::getInstance();
        
        $query = 'SELECT ks.Id, ks.ten_dphieu, ks.dia_chi, db.Id, db.NgayBan, nv.TaiKhoan, kh.TenKH, db.Tong, db.TrangThai 
        FROM DonBan db 
        JOIN dphieu ks ON ks.Id = db.Id
        WHERE db.TrangThai = :status 
        LIMIT :start, :recordsPerPage';

        $reg = $db->prepare($query);
        $reg->bindParam(':status', $status, PDO::PARAM_INT);
        $reg->bindParam(':start', $start, PDO::PARAM_INT);
        $reg->bindParam(':recordsPerPage', $recordsPerPage, PDO::PARAM_INT);
    
        if (!$reg->execute()) {
            // Xử lý lỗi, in thông báo hoặc log lỗi
            die('Query failed: ' . implode(":", $reg->errorInfo()));
        }
    
        $result = $reg->fetchAll();
    
        foreach ($result as $item) {
            $list[] =new dphieu($item['Id'],$item['ten_dphieu'],$item['dia_chi']);    
          }
    
        return $list;
    }

}