<?php

// [クラス] データベースアクセス処理
// <説明> MySQLへのアクセスを隠蔽するために実装。SQLインジェクション対策済み。

// [コンストラクタ] データベースアクセスを開始
// <引数1> データベースのホスト名('localhost'など)
// <引数2> データベースの名前(NULL可)
// <引数3> データベースにログインするためのアカウント名
// <引数4> データベースにログインするためのパスワード
// new CDataBase(string $sHost, string $sDataBase, string $sAccount, string $sPassword)

// [メソッド] データベースを新規作成
// <引数1> データベース名
// createDataBase(string $sDataBase): void

// [メソッド] データベースを選択
// <引数1> データベース名
// selectDataBase(string $sDataBase): void

// [メソッド] データベースがあれば削除
// <引数1> データベース名
// destroyDataBase(string $sDataBase): void

// [メソッド] テーブルを新規作成
// <引数1> テーブル名
// <引数2> 主キーを格納するカラム名
// createTable(string $sTable, string $sPrimaryColumn): void

// [メソッド] テーブルを選択
// <引数1> テーブル名
// <引数2> 主キーを格納しているカラム名
// selectTable(string $sTable, string $sPrimaryColumn): void

// [メソッド] テーブルを削除
// <引数1> テーブル名
// destroyTable(string $sTable): void

// [メソッド] テーブルに数値を格納するカラムを追加
// <引数1> カラム名
// addIntColumn(string $sColumn): void

// [メソッド] レコードを新規作成
// <引数1> 主キー
// createRecord(string $sPrimaryKey): void

// [メソッド] レコードを選択
// <説明> 以下のメソッドの操作対象のレコードを選択する。
//          setStr()/setInt()/getStr()/getInt()
// <引数1> 主キー
// <戻り値> 存在するならTRUE、存在しないならFALSE
// selectRecord(string $sPrimaryKey): void

// [メソッド] レコードを削除
// <引数1> 主キー
// destroyRecord(string $sPrimaryKey): void

// [メソッド] フィールドに文字列を設定
// <引数1> カラム名
// <引数2> 文字列
// setStr(string $sColumn, string $sVal): void

// [メソッド] フィールドに数値を設定
// <引数1> カラム名
// <引数2> 数値
// setInt(string $sColumn, integer $iVal): void

// [メソッド] フィールドから文字列を取得
// <引数1> カラム名
// <戻り値> 文字列
// getStr(string $sColumn): string

// [メソッド] フィールドから数値を取得
// <引数1> カラム名
// <戻り値> 数値
// getInt(string $sColumn): integer

// [メソッド] フィールドの値に数値を加算
// <引数1> カラム名
// <引数2> 加算する値
// addInt(string $sColumn, integer $iAdd): void

// [メソッド] トランザクション処理を開始
// beginTransaction(): void

// [メソッド] トランザクション処理を確定
// commit(): void



// ■■■■■■■■■■■■■■■■
// ■         共通クラス         ■
// ■■■■■■■■■■■■■■■■

class CDataBase
{
    private $pdo;
    private $sTable;
    private $sPrimaryColumn;
    private $sPrimaryKey;

    function __construct($sHost, $sDataBase, $sAccount, $sPassword)
    {
        $this->pdo = new PDO("mysql:host={$sHost};dbname={$sDataBase};charset=utf8mb4", $sAccount, $sPassword, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    function createDataBase($sDataBase)
    {
        $this->pdo->query("CREATE DATABASE {$sDataBase} DEFAULT CHARACTER SET utf8mb4");
    }

    function selectDataBase($sHost, $sDataBase, $sAccount, $sPassword)
    {
        $this->pdo = NULL;
        $this->pdo = new PDO("mysql:host={$sHost};dbname={$sDataBase};charset=utf8mb4", $sAccount, $sPassword, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $this->sTable = '';
        $this->sPrimaryColumn = '';
        $this->sPrimaryKey = '';
    }

    function destroyDataBase($sDataBase)
    {
        $this->pdo->query("DROP DATABASE IF EXISTS {$sDataBase}");
    }

    function createTable($sTable, $sPrimaryColumn, $sColumnType)
    {
        $this->pdo->query("CREATE TABLE {$sTable} ( {$sPrimaryColumn} {$sColumnType} PRIMARY KEY )");
    }

    function selectTable($sTable, $sPrimaryColumn)
    {
        $this->sTable = $sTable;
        $this->sPrimaryColumn = $sPrimaryColumn;
    }

    function destroyTable($sTable)
    {
        $this->pdo->query("DROP TABLE IF EXISTS {$sTable}");
    }

    function addIntColumn($sColumn)
    {
        $this->pdo->query("ALTER TABLE {$this->sTable} ADD {$sColumn} INT");
    }

    function createRecord($sPrimaryKey)
    {
        $stm = $this->pdo->prepare("INSERT INTO {$this->sTable} ( {$this->sPrimaryColumn} ) VALUES ( ? )");
        $stm->bindValue(1, $sPrimaryKey, PDO::PARAM_STR);
        $stm->execute();
        $stm = NULL;
    }

    function selectRecord($sPrimaryKey)
    {
        $this->sPrimaryKey = $sPrimaryKey;
        $stm = $this->pdo->prepare("SELECT * FROM {$this->sTable} WHERE {$this->sPrimaryColumn}=? LIMIT 1");
        $stm->bindValue(1, $sPrimaryKey, PDO::PARAM_STR);
        $stm->execute();
        //$asRet = $stm->fetch(PDO::FETCH_NUM);
        //$stm = NULL;
        //return $asRet !== FALSE;
        $asRet = $stm->fetchAll();
        $stm = NULL;
        return $asRet !== [];
    }

    function destroyRecord($sPrimaryKey)
    {
        $stm = $this->pdo->prepare("DELETE FROM {$this->sTable} WHERE {$this->sPrimaryColumn}=?");
        $stm->bindValue(1, $sPrimaryKey, PDO::PARAM_STR);
        $stm->execute();
        $stm = NULL;
    }

    private function setVal($sColumn, $iParam, $mVal)
    {
        $stm = $this->pdo->prepare("UPDATE {$this->sTable} SET {$sColumn}=? WHERE {$this->sPrimaryColumn}=?");
        $stm->bindValue(1, $mVal, $iParam);
        $stm->bindValue(2, $this->sPrimaryKey, PDO::PARAM_STR);
        $stm->execute();
        $stm = NULL;
    }

    function setStr($sColumn, $sVal)
    {
        assertCond(is_string($sVal));

        $this->setVal($sColumn, PDO::PARAM_STR, $sVal);
    }

    function setInt($sColumn, $iVal)
    {
        assertCond(is_int($iVal));

        $this->setVal($sColumn, PDO::PARAM_INT, $iVal);
    }

    function getStr($sColumn)
    {
        $stm = $this->pdo->prepare("SELECT {$sColumn} FROM {$this->sTable} WHERE {$this->sPrimaryColumn}=? LIMIT 1");
        $stm->bindValue(1, $this->sPrimaryKey, PDO::PARAM_STR);
        $stm->execute();
        $asRet = $stm->fetch(PDO::FETCH_NUM);
        $stm = NULL;
        return $asRet[0];
    }

    function getInt($sColumn)
    {
        return intval($this->getStr($sColumn));
    }

    function addInt($sColumn, $iAdd)
    {
        $this->setInt($sColumn, $this->getInt($sColumn) + $iAdd);
    }

    function beginTransaction()
    {
        $this->pdo->beginTransaction();
        $stm = $this->pdo->prepare("SELECT * FROM {$this->sTable} WHERE {$this->sPrimaryColumn}=? FOR UPDATE");
        $stm->bindValue(1, $this->sPrimaryKey, PDO::PARAM_STR);
        $stm->execute();
    }

    function commit()
    {
        $this->pdo->commit();
    }

    function __destruct()
    {
        $this->pdo = NULL;
    }
}

?>
