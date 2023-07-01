Esse preojeto foi feito para atender desafio técnico para vaga de programador Back-end PHP na empresa Mosyle

O teste consiste na criação de uma api para registrar a quantidade de vezes que seus usuário bebem café por dia

- O projeto deve ser desenvolvido usando a linguagem de programação PHP e uma base de dados relacional.
- Nenhum framework deve ser útilizado.
- Todos os Inputs devem ser no formato JSON.
- A API deve seguir o padrão REST.
- O código deve ser orientado a objetos.
- Desafios plagiados/copiados serão descartados.

<h3>Instalação:</h3>

<small>Para gerar os autoloades:</small>
```bash
  composer update
```
```bash
  cp ./app/Core/Database/config.exemple.php ./app/Core/Database/config.php 
```

    'driver' => 'mysql',
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'development',
    'username' => 'root',
    'password' => ''

rode o script sql que está em: `./app/Core/Database/database.sql` 
para criar as estruturas necessárias.

```bash
  cd ./public && php -S localhost:8000 
```

<h3>Endpoints implementados:</h3>
<p> - obrigatórios:</p>

- `POST /users/` (cria um usuário)
- `POST /login` (Login via Bearer token)
- `GET /users/:userId` (retorna dados de um usuário)
- `GET /users` (retorna lista de usuários)
- `PUT /users/:userId` (edita um usuário)
- `DELETE /users/:userId` (deleta um usuário)
- `POST /users/:userId/drink` (registra uma quantidade de vezes de café tomado)

<p> - opcionais:</p>

- `GET /users?page=:page` (retorna lista de usuários com paginação)
- `GET /users/:userId/history` (retorna histórico por dia de um usuário)
- `GET /users/ranking/:interval` (retorna ranking dos maiores bebedores de café dos últimos x dias)
- `GET /users/ranking/:year/:month/:day` (retorna ranking dos maiores bebedores de café do dia especificado)

