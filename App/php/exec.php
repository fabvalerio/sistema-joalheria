<?php

//Classe
require_once '../vendor/autoload.php';
use Dotenv\Dotenv;

class BackgroundProcess
{
    private string $phpBinary;
    private string $serverPath;

    public function __construct(string $dotenvPath)
    {
        $this->loadEnv($dotenvPath);
        $this->phpBinary = $this->findPhpBinary();
        $this->serverPath = $this->getServerPath();
    }

    /**
     * Carrega as variáveis do arquivo .env.
     */
    private function loadEnv(string $path): void
    {
        if (!file_exists($path . '.env')) {
            throw new Exception("Arquivo .env não encontrado no caminho: $path");
        }
        $dotenv = Dotenv::createImmutable($path);
        $dotenv->load();
    }

    /**
     * Obtém o caminho do binário do PHP.
     */
    private function findPhpBinary(): string
    {
        $phpBinary = trim(shell_exec('which php')) ?: 'php';
        if (!file_exists($phpBinary)) {
            throw new Exception("Binário do PHP não encontrado no sistema.");
        }
        return $phpBinary;
    }

    /**
     * Obtém o caminho do servidor a partir do arquivo .env.
     */
    private function getServerPath(): string
    {
        $path = $_ENV['pathServerRoot'] ?? null;
        if (!$path || !is_dir($path)) {
            throw new Exception("Caminho do servidor inválido ou não configurado no .env");
        }
        return rtrim($path, '/');
    }

    /**
     * Valida o ID recebido no POST.
     */
    public function validateId($id): int
    {
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) {
            throw new Exception("ID inválido. Deve ser um número inteiro maior que zero.");
        }
        return $id;
    }

    /**
     * Inicia o processo em segundo plano.
     */
    public function startProcess(int $id, string $urlPath): void
    {
       echo $scriptPath = $this->serverPath . $urlPath;
        if (!file_exists($scriptPath)) {
            throw new Exception("Arquivo background_task.php não encontrado no servidor.");
        }

        $command = escapeshellcmd("{$this->phpBinary} {$scriptPath} {$id}") . " > /dev/null 2>&1 &";

        $output = [];
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new Exception("Erro ao iniciar o processo. Código de retorno: $returnVar");
        }

        echo "Processo em segundo plano iniciado com sucesso! ID: $id";
    }
}


    #Modelo para execução em segundo plano

    // Inicializar a classe e processar a requisição
    /*
    $backgroundProcess = new BackgroundProcess('../');
    $numID = $backgroundProcess->validateId($_POST['id']);
    $urlPath = "/teste-exec/background_task.php";
    $backgroundProcess->startProcess($numID, $urlPath );
    */
        


