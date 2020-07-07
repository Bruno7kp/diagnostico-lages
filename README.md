### Instalação

Clone o repositório

```
git clone https://github.com/bruno7kp/mural-virtual.git
```

No diretório raiz, execute o comando `composer install` para instalar as bibliotecas utilizadas.

Crie um arquivo `.env` dentro da pasta `public`, com as configurações do banco de dados (ver arquivo `.env.example` como exemplo).

Execute o arquivo `db.sql` no banco de dados utilizado para exportar a estrutura o banco e adicionar alguns usuários de acesso padrão.

Os usuário são:

- Nível Administrador (Acesso a tudo)
  - sabrina@uniplac.net / senha: 1234
- Nível Moderador (Acesso a tudo, menos gerenciamento de usuários)
  - bruno@uniplaclages.edu.br / senha: 1234
- Nível Editor (Acesso apenas aos dados de índices)
  - uniplac@uniplaclages.edu.br / senha: 1234

#### Estrutura dos arquivos/diretórios

- Pasta raiz
  - `cache` (pasta utilizada pela biblioteca *twig* para renderizar páginas mais rapidamente)
  - `public` (deve ser colocada como diretório inicial do servidor web contém apenas arquivos de acesso do cliente/navegador)
  - `src` (pasta onde se encontra toda a lógica de *models* e *controller* da aplicação)
  - `vendor` (pasta onde ficam salvos os arquivos das bibliotecas instaladas pelo *composer*)
  - `view` (pasta com os arquivos html/twig da aplicação)
  
#### Estrutura de organização dos dados

O sistema mostra dados de indicadores sociais para determinadas regiões, para isso o sistema tem a seguinte estrutura de dados:

![image](https://user-images.githubusercontent.com/6254886/86806156-f8a89000-c04e-11ea-8634-5a9055b1ca4c.png)
(Imagem da página inicial, mostrando todas as categorias, indicadores e índices.)

###### Categorias
As categorias dividem os índices por grandes áreas, por exemplo: Categoria de índices de: Educação, Saúde, Habitação, etc.

###### Indicadores
Os indicadores são os assuntos que precisam ser mensurados, exemplo: Dentro da categoria Educação, um dos indicadores é a Alfabetização.

###### Índices/Dados
O índice é a informação necessária de se coletar dados, por exemplo: Porcentagem de pessoas alfabetizadas. Essa informação pode ser segmentada de acordo com a necessidade, por exemplo: Pessoas alfabetizadas entre 30 e 40 anos.

###### Segmentações
Definem se o índice pertence a algum tipo de segmentação, assim é possível agrupar os índices dessa segmentação, por exemplo, uma lista de índices de pessoas alfabetizadas por faixa etária.

![image](https://user-images.githubusercontent.com/6254886/86810605-7d95a880-c053-11ea-9e8b-b5a54e1940c6.png)
(Imagem da página de um indicador, mostrando todos os índices com os valores para cada região)

![image](https://user-images.githubusercontent.com/6254886/86812763-c189ad00-c055-11ea-85a3-0d9f8d05a79c.png)
(Imagem da página de um índice, mostrando os dados de cada região por ano)

 ###### Regiões
Os dados de cada índice são referentes a uma região (bairro, cidade, etc.) e a um ano determinado no momento do seu cadastro.

![image](https://user-images.githubusercontent.com/6254886/86810416-3effee00-c053-11ea-8bb6-d1ab5816f2b8.png)
(Imagem da página de uma região, mostrando os dados de todos os índices cadastrados para a mesma)


![image](https://user-images.githubusercontent.com/6254886/86814094-5345ea00-c057-11ea-95c8-e850fc973f10.png)
Quando um dado, região, indicador ou outros tem alguma observação, o mesmo ficará com um sublinhado tracejado onde ao passar o mouse em cima ou clicar sobre o texto (no celular), aparecerá o texto da observação.
  