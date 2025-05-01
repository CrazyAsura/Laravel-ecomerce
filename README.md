# Laravel-ecomerce
# Documentação Completa do Sistema de E-commerce com Laravel

## Índice

1. [Visão Geral do Sistema](#visão-geral-do-sistema)
2. [Requisitos Técnicos](#requisitos-técnicos)
3. [Configuração do Ambiente](#configuração-do-ambiente)
4. [Estrutura do Projeto](#estrutura-do-projeto)
5. [Funcionalidades Principais](#funcionalidades-principais)
6. [Fluxos de Trabalho](#fluxos-de-trabalho)
7. [Configuração do Banco de Dados](#configuração-do-banco-de-dados)
8. [API Reference](#api-reference)
9. [Testes](#testes)
10. [Deploy](#deploy)
11. [FAQ](#faq)

---

## 1. Visão Geral do Sistema <a name="visão-geral-do-sistema"></a>

Sistema de e-commerce completo desenvolvido com Laravel que inclui:
- Autenticação de usuários (login/registro com email e Google)
- CRUD de produtos e categorias
- Carrinho de compras
- Processo de checkout com Mercado Pago (Pix, Boleto e Cartão)
- Área administrativa
- Gestão de pedidos

**Diagrama de Arquitetura**:
```
Cliente (Browser) → Laravel App → Banco de Dados
                      ↓
                Mercado Pago API
```

---

## 2. Requisitos Técnicos <a name="requisitos-técnicos"></a>

- PHP 8.1+
- Composer
- Node.js 16+
- SQLite/MySQL/PostgreSQL
- Extensões PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

---

## 3. Configuração do Ambiente <a name="configuração-do-ambiente"></a>

### 3.1 Instalação Inicial

```bash
git clone [repositório]
cd [nome-do-projeto]
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### 3.2 Configuração do SQLite

```bash
touch database/database.sqlite
```

No `.env`:
```ini
DB_CONNECTION=sqlite
# Comente outras configurações de DB
```

### 3.3 Executando o Sistema

```bash
php artisan migrate --seed
php artisan serve
npm run dev
```

---

## 4. Estrutura do Projeto <a name="estrutura-do-projeto"></a>

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── AdminController.php
│   │   ├── CartController.php
│   │   └── OrderController.php
│   └── Middleware/
database/
├── migrations/
├── seeders/
public/
resources/
├── views/
│   ├── auth/
│   ├── cart/
│   ├── orders/
│   └── admin/
routes/
├── web.php
tests/
```

---

## 5. Funcionalidades Principais <a name="funcionalidades-principais"></a>

### 5.1 Autenticação
- Login/Registro tradicional
- Login social com Google
- Middleware de proteção de rotas

**Código-Chave**:
```php
// LoginController
public function handleGoogleCallback()
{
    $googleUser = Socialite::driver('google')->user();
    // ... lógica de criação de usuário
}
```

### 5.2 Produtos e Categorias
- CRUD completo
- Upload de imagens
- Relacionamento Produto-Categoria

**Model**:
```php
class Produto extends Model
{
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
```

### 5.3 Carrinho de Compras
- Sessão persistente
- Atualização em tempo real
- Validação de estoque

**Controller**:
```php
class CartController
{
    public function update(Request $request, Cart $cart)
    {
        $request->validate(['quantity' => 'required|numeric|min:1']);
        $cart->update(['quantity' => $request->quantity]);
    }
}
```

---

## 6. Fluxos de Trabalho <a name="fluxos-de-trabalho"></a>

### 6.1 Fluxo de Compra
1. Usuário adiciona itens ao carrinho
2. Acessa checkout (/checkout)
3. Preenche dados de entrega
4. Seleciona método de pagamento
5. É redirecionado ao Mercado Pago
6. Retorna ao site após pagamento

**Diagrama de Sequência**:
```
Usuário → Laravel → MercadoPago → Webhook → Atualiza Pedido
```

---

## 7. Configuração do Banco de Dados <a name="configuração-do-banco-de-dados"></a>

### 7.1 Migrations Principais

```php
// Tabela de produtos
Schema::create('produtos', function (Blueprint $table) {
    $table->id();
    $table->string('nome');
    $table->decimal('preco', 10, 2);
    $table->foreignId('categoria_id')->constrained();
});
```

### 7.2 Relacionamentos
- User hasMany Orders
- Order hasMany OrderItems
- Product belongsTo Category

---

## 8. API Reference <a name="api-reference"></a>

### 8.1 Rotas Principais

| Método | Endpoint           | Descrição                |
|--------|--------------------|--------------------------|
| GET    | /produtos          | Lista produtos           |
| POST   | /carrinho/{produto}| Adiciona ao carrinho     |
| POST   | /pedidos           | Cria novo pedido         |

### 8.2 Webhook Mercado Pago
`POST /mercadopago/notification` - Atualiza status do pedido

---

## 9. Testes <a name="testes"></a>

```bash
php artisan test
```

**Exemplo de Teste**:
```php
public function test_add_to_cart()
{
    $user = User::factory()->create();
    $product = Product::factory()->create();
    
    $response = $this->actingAs($user)
        ->post(route('cart.store', $product));
    
    $response->assertRedirect(route('cart.index'));
    $this->assertDatabaseHas('carts', ['product_id' => $product->id]);
}
```

---

## 10. Deploy <a name="deploy"></a>

### 10.1 Para Produção
1. Configure `.env` para produção
2. Execute:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 10.2 Variáveis de Ambiente Obrigatórias
```ini
APP_ENV=production
APP_DEBUG=false
MP_ACCESS_TOKEN=seu_token
MP_PUBLIC_KEY=sua_chave
```

---

## 11. FAQ <a name="faq"></a>

**Q: Como criar um usuário admin?**
```bash
php artisan tinker
User::create(['name'=>'Admin', 'email'=>'admin@exemplo.com', 'password'=>bcrypt('senha'), 'role'=>'admin'])
```

**Q: Como resetar o banco de dados?**
```bash
php artisan migrate:fresh --seed
```

**Q: Onde configurar métodos de pagamento?**
Em `config/mercadopago.php` e `.env`

---

Esta documentação cobre todos os aspectos essenciais do sistema. Para detalhes específicos, consulte os comentários no código ou a documentação oficial do Laravel.
