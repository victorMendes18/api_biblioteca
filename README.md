# ApiBiblioteca
A ApiBiblioteca oferece um gerenciamento de empréstimos de livros em bibliotecas. A API permite o cadastro de dois tipos de usuários: administradores (ADM) e bibliotecários (librarian), com diferentes níveis de acesso. Com ela, é possível gerenciar o acervo de livros, cadastrar estudantes e controlar os empréstimos realizados pelos usuários.

Os administradores (ADM) têm acesso completo a todas as rotas da API, sendo responsáveis por cadastrar novos usuários. Por outro lado, os bibliotecários possuem acesso restrito: eles só podem visualizar e operar com livros públicos, támbém não possuem permissão para acessar rotas de gerenciamento de usuários. Todas as demais funcionalidades, relacionadas ao acervo e aos empréstimos, estão liberadas para os bibliotecários.

# Instalação / Execução (Windows)
O projeto pode ser executado de duas maneiras: utilizando o Apache e MySQL do [XAMPP](https://www.apachefriends.org/pt_br/download.html) ou via [Docker](https://www.docker.com/products/docker-desktop/), utilizando o arquivo `docker-compose.yml` localizado na raiz do projeto.

## Clonando o Repositório
1. Certifique-se de ter o [Git](https://git-scm.com/downloads) instalado em sua máquina.
2. Execute o seguinte comando no terminal para clonar o repositório:

   ```bash
   git clone https://github.com/victorMendes18/api_biblioteca


## XAMPP

### Instalação
1. Caso ainda não tenha, faça o download e a instalação do [XAMPP](https://www.apachefriends.org/pt_br/download.html).
2. Instale o [Composer](https://getcomposer.org/download/) se ele ainda não estiver instalado em sua máquina.

### Execução
1. Navegue até a pasta do projeto utilizando o explorador de arquivos e faça uma cópia do arquivo `.env.example`, renomeando-a para `.env`.
2. Inicie o XAMPP e ative os serviços **Apache** e **MySQL**.
3. Acesse o painel administrativo do MySQL (ou utilize uma ferramenta como o **phpMyAdmin**) e crie um banco de dados chamado **Biblioteca**.
5. Abra um terminal dentro do diretório do projeto clonado e execute os seguintes comandos:
   - Execute **composer install** para baixar as dependências do Laravel:
     ```bash
     composer install
     ```
   - Execute **php artisan key:generate** para gerar uma chave de 32 caracteres, usada pelo serviço de criptografia do Laravel:
     ```bash
     php artisan key:generate
     ```
   - Execute **php artisan jwt:secret** para gerar a chave secreta usada pelo JWT (JSON Web Token):
     ```bash
     php artisan jwt:secret
     ```
   - Execute os comandos **php artisan migrate** e **php artisan db:seed** para rodar as migrações e os seeders do projeto:
     ```bash
     php artisan migrate
     php artisan db:seed
     ```
   - Por fim, execute **php artisan serve** para iniciar o servidor local:
     ```bash
     php artisan serve
     ```

Com todos esses passos concluídos, a aplicação estará rodando no servidor local. Um usuário administrador foi criado automaticamente pelos seeders com o e-mail **admin@gmail.com** e a senha **12345678**. Esse usuário pode ser utilizado para acessar a aplicação e usar todas as rotas disponíveis.

## Docker

### Instalação
1. Caso ainda não tenha, faça o download e a instalação do [PHP](https://www.php.net/downloads.php).
2. Instale o [Composer](https://getcomposer.org/download/) se ele ainda não estiver instalado em sua máquina.
3. Faça o download e instale o [Docker Desktop](https://www.docker.com/products/docker-desktop/) para criar e gerenciar os containers.

### Execução
1. Navegue até a pasta do projeto utilizando o explorador de arquivos e faça uma cópia do arquivo `.env.example`, renomeando-a para `.env`.
2. Comente as variáveis relacionadas ao XAMPP no arquivo `.env` e descomente as variáveis específicas para o Docker.
3. No terminal, dentro do diretório do projeto, execute o seguinte comando para instalar as dependências do Laravel:
    ```bash
    composer install
    ```
4. Execute os seguintes comandos para configurar a aplicação:
   - Gerar a chave de criptografia do Laravel:
     ```bash
     php artisan key:generate
     ```
   - Gerar a chave secreta usada pelo JWT (JSON Web Token):
     ```bash
     php artisan jwt:secret
     ```
   - Inicie o Docker e execute o comando abaixo para criar os containers da API, banco de dados e servidor:
     ```bash
     docker compose up
     ```

5. No terminal, execute os seguintes comandos para rodar as migrações e seeders do projeto:
     ```bash
     php artisan migrate
     php artisan db:seed
     ```

Com todos esses passos concluídos, a aplicação estará rodando no servidor local. Um usuário administrador foi criado automaticamente pelos seeders com o e-mail **admin@gmail.com** e a senha **12345678**. Esse usuário pode ser utilizado para acessar a aplicação e utilizar todas as rotas disponíveis.











## Instalar o Composer
1. Acesse a página oficial do [Composer](https://getcomposer.org/download/).
2. Faça o download da ferramenta e siga as instruções fornecidas pelo instalador para completar a instalação.
3. No terminal, navegue até o diretório do projeto clonado e execute o seguinte comando:

   ```bash
   composer install

## Executando a Aplicação

Você pode executar o projeto de duas maneiras: utilizando o Apache e MySQL do [XAMPP](https://www.apachefriends.org/pt_br/download.html) ou através do [Docker](https://www.docker.com/products/docker-desktop/), utilizando o arquivo `docker-compose.yml` localizado na raiz do projeto.

### Passos:

1. Faça uma cópia do arquivo `.env.example` e renomeie-a para `.env`.

2. No arquivo `.env`, configure as variáveis de ambiente para a conexão com o MySQL. Por padrão, o arquivo está configurado para usar o XAMPP. Para isso:
   - Inicie o XAMPP e ative o Apache e o MySQL.
   - Acesse o painel administrativo do MySQL e crie um banco de dados chamado **Biblioteca**.
   - No terminal, dentro do diretório do projeto, execute o seguinte comando para iniciar o servidor local:

    ```bash
    php artisan serve
    ```

3. Para executar o projeto com o Docker:
   - Comente as variáveis relacionadas ao XAMPP no arquivo `.env` e descomente as variáveis específicas para o Docker.
   - Inicie o Docker Desktop e, no terminal, dentro do diretório do projeto, execute o comando:

    ```bash
    docker compose up
    ```

Agora, a aplicação será configurada de acordo com o ambiente escolhido.

4. Execute os seguintes comandos no terminal para rodar as migrações, rodar os seeders e gerar a chave de autenticação JWT:

    ```bash
    php artisan migrate
    php artisan db:seed
    php artisan jwt:secret
    ```

Após esses passos, a API estará em execução. Com os seeders, será criado um usuário administrador com o e-mail **adm@gmail.com** e a senha **12345678**, permitindo que você faça login e utilize todas as rotas da API.

A documentação da API, com todas as rotas disponíveis, pode ser acessada em: [http://localhost:8000/docs](http://localhost:8000/docs). 
