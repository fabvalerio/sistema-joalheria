<?php


require_once  dirname(__DIR__) .'/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__)); // Também ajustado
$dotenv->load();


// Define o fuso horário padrão
date_default_timezone_set('America/Sao_Paulo');

class db
{
    private $host;
    private $dbName;
    private $user;
    private $pass;
    private $port;
    private $dbh;
    private $error;
    private $qError;
    private $stmt;

    
    public function __construct()
    {
        // Atribui valores das variáveis de ambiente
        $this->host   = $_ENV['DB_HOST'];
        $this->dbName = $_ENV['DB_NAME'];
        $this->user   = $_ENV['DB_USER'];
        $this->pass   = $_ENV['DB_PASS'];
        $this->port   = $_ENV['DB_PORT'] ?: '3306'; // Padrão para a porta MySQL

        // Monta o DSN para conexão com MySQL
        $dsn = "mysql:host={$this->host};dbname={$this->dbName};port={$this->port};charset=utf8";

        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );

        try {
            // Cria uma nova conexão PDO
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            //echo "Conexão realizada com sucesso!";
        } catch (PDOException $e) {
            // Captura qualquer erro de conexão
            $this->error = $e->getMessage();
            echo "Erro de conexão: " . $this->error;
        }
    }




    //Aquisão
    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
    }

    //Conecatar
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    //Executar
    public function execute()
    {
        return $this->stmt->execute();

        $this->qError = $this->dbh->errorInfo();
        if (!is_null($this->qError[2])) {
            echo $this->qError[2];
        }
        echo 'done with query';
    }

    public function resultSet()
    {
        $this->execute();
        return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //Exibir em objeto
    public function object()
    {
        return $this->stmt->fetchObject();
    }

    //Exibir Array
    public function row()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //inico
    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    //lista de tabela
    public function table()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Numero de Linha
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    //Ultimo Id cadastrado
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    //Iniciar Transa�ão
    public function beginTransaction()
    {
        return $this->dbh->beginTransaction();
    }

    //final transa�ão
    public function endTransaction()
    {
        return $this->dbh->commit();
    }

    //cancelar trasa�ão
    public function cancelTransaction()
    {
        return $this->dbh->rollBack();
    }

    //depurar params de despejo
    public function debugDumpParams()
    {
        return $this->stmt->debugDumpParams();
    }

    //Erro query
    public function queryError()
    {
        $this->qError = $this->dbh->errorInfo(); // Armazena as informações de erro do PDO
        if (!is_null($this->qError[2])) { // O índice 2 contém a mensagem de erro, caso exista
            echo $this->qError[2];
        }
    }

    //***************************************************************




}