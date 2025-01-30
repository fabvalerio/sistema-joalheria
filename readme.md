# Instalaçoes

npx webpack --config webpack.config.js


# Cpanel

[https://www.web4br.com:2083/](https://www.web4br.com:2083/)

Usuário: webbrcom
Senha: gK1QB3tRWLxTk5zA

# Banco de dados

Data: webbrcom_joias
User: webbrcom_joias
Pass:  8I~O%zHz(Z!8


# Diretórios

* APP
  * Models
    * Dir (Este nome de diretíro  é igual o nome da tabela do banco de dados)
      * File (Sempre salvar  com nome Controller.php)
  * php (todos arquivos de php que irá usar)
* assets
  * componentes (Onde tudo que é estrutura do layout)
  * css
  * js
  * logo.png
* node_modules (compose)
* pages (Todas as sessões e paginas)
  * DIR (Nome igual da tabela do banco de dados)
    * Files
  * home.php (Pagina do dashboard)
* tema (tema do layout)
* vendor (compose)
* .env (acesso ao banco de dados)
* .htaccess
* ...

# Estruture o namespace

App/Models/\/Modelo/NomeDaClasse.php

### Crie uma classe dentro do namespace

Exemplo da classe em `App/Models/Modelo/Controller.php`:

```
<?php

namespace App\Models\Modelo;

class Controller
{
    public function listar()
    {
        echo "Listando agentes comerciais";
    }
}
```

Configure o autoload com Composer

composer init

```
"autoload": {
    "psr-4": {
        "App\\": "App/"
    }
}
```

Configure o arquivo `index.php`

```
<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Modelo\Controller;

// Crie uma instância da classe do namespace desejado
$return= new Controller();
$return->listar();
```

# Controler

Cadastro

```


 public function cadastro($dados)
    {
        $lista = new db();

        // Cria os placeholders dinâmicos
        $campos = implode(", ", array_keys($dados));
        $placeholders = ":" . implode(", :", array_keys($dados));

        // Monta o SQL
        $sql = "INSERT INTO Modelo ($campos) VALUES ($placeholders)";
        $lista->query($sql);

        // Associa os valores dinâmicos
        foreach ($dados as $campo => $valor) {
            $lista->bind(":$campo", $valor);
        }

        // Executa e retorna o status
        return $lista->execute();
    }
```

Editar

```
    public function editar($id, $dados)
    {
        $editar = new db();

        // Cria os placeholders no formato campo = :campo
        $setPlaceholders = [];
        foreach ($dados as $campo => $valor) {
            $setPlaceholders[] = "$campo = :$campo";
        }

        // Monta a string final para o SET
        $setQuery = implode(", ", $setPlaceholders);

        // Monta o SQL
        $sql = "UPDATE Modelo SET $setQuery WHERE id = :id";
        $editar->query($sql);

        // Associa os valores dinâmicos
        foreach ($dados as $campo => $valor) {
            $editar->bind(":$campo", $valor);
        }
        $editar->bind(":id", $id);

        // Executa e retorna o status
        return $editar->execute();
  
    }
```

Deletar

```
    public function deletar($id)
    {
        $ver = new db();
        $ver->query("DELETE FROM Modelo WHERE id = :id");
        $ver->bind(":id", $id);
        $resultados = $ver->single();
        return $resultados ?: false;
    }
```

Ver

```
    public function ver($id)
    {
        $ver = new db();
        $ver->query("SELECT * FROM Modelo WHERE id = :id");
        $ver->bind(":id", $id);
        $resultados = $ver->single();
        return $resultados ?: false;
    }
```

Listar

```
    public function listar()
    {
        $lista = new db();
        $lista->query("SELECT * FROM Modelo ORDER BY id DESC");
        $resultados = $lista->resultSet(); // Supondo que o método resultSet() retorna os resultados
        return $resultados;
    }
```

# Como usar 2 CONTROLLER?

Você vai usar "AS", exemplo abaixo

```
use App\Models\AgentesComerciais\Controller as AgentesComerciaisController;
use App\Models\MetasAgentes\Controller as MetasAgentesController;

$id = $link[3];

$controller = new AgentesComerciaisController();
$agentes = $controller->ver($id);

$controller = new MetasAgentesController();
$metas = $controller->listar($id);
```
