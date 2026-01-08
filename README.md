# FutTeams - Gestor de Partidas de Futebol

Um sistema web moderno para gerir partidas de futebol, selecionar jogadores, registar resultados e gerar equipas balanceadas automaticamente.

## 📋 Índice

- [Características](#características)
- [Requisitos](#requisitos)
- [Instalação](#instalação)
- [Como Funciona](#como-funciona)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [API](#api)
- [Troubleshooting](#troubleshooting)

---

## ✨ Características

- ✅ **CRUD Completo** de jogadores, posições, equipas e partidas
- ✅ **Geração Automática de Equipas** com balanceamento por performance
- ✅ **Registo de Eventos** em tempo real (golos, assistências)
- ✅ **Seleção Inteligente de Jogadores** com filtros e busca
- ✅ **Paginação Dinâmica** (10, 12, 25, 50 jogadores/página)
- ✅ **Dashboard Responsivo** com Tailwind CSS v4
- ✅ **Confirmações Visuais** e notificações toast
- ✅ **Organização por Posição** (GR, DEF, MID, AV)
- ✅ **Estatísticas Agregadas** (golos/jogo, assistências, etc.)
- ✅ **Suporte para 4+ Jogadores** por partida

---

## 🔧 Requisitos

- **PHP**: 8.2+
- **Node.js**: 16+ (LTS recomendado)
- **Composer**: 2.0+
- **npm** ou **yarn**
- **Base de Dados**: SQLite (pré-configurado) ou MySQL/PostgreSQL
- **Herd** (opcional, para domínios locais): `futteams.test`

---

## 📦 Instalação

### 1. Clonar o Repositório

```bash
cd ~/sites/projects
git clone <repo-url> futteams
cd futteams
```

### 2. Instalar Dependências PHP

```bash
composer install
```

### 3. Instalar Dependências Node.js

```bash
npm install
```

### 4. Configurar Variáveis de Ambiente

```bash
cp .env.example .env
```

Edite `.env` e defina:

```env
APP_NAME=FutTeams
APP_ENV=local
APP_DEBUG=true
APP_URL=http://futteams.test

DB_CONNECTION=sqlite
# (ou MySQL/PostgreSQL conforme preferir)

VITE_APP_TITLE="FutTeams"
```

### 5. Gerar Chave de Aplicação

```bash
php artisan key:generate
```

### 6. Criar Base de Dados

```bash
php artisan migrate
```

### 7. Semear Dados (Opcional)

Escolha um dos seeders:

```bash
# Jogadores com boas estatísticas
php artisan migrate:fresh --seed --seeder=StrongStatsSeeder

# OU Jogadores com estatísticas fracas
php artisan migrate:fresh --seed --seeder=WeakStatsSeeder
```

### 8. Iniciar Servidor de Desenvolvimento

**Terminal 1 - Laravel:**

```bash
php artisan serve
```

**Terminal 2 - Vite (Hot Module Replacement):**

```bash
npm run dev
```

### 9. Aceder à Aplicação

Abra o navegador em:

```
http://futteams.test
```

(Se não usar Herd, use `http://localhost:8000`)

---

## 🎮 Como Funciona

### Fluxo Principal

```
1. Criar Nova Partida
   └─ Define: nome, data, posições (GR, DEF, MID, AV)

2. Selecionar Jogadores
   ├─ Filtrar por posição ou nome
   ├─ Ver estatísticas (golos, assistências, golos/jogo)
   └─ Escolher 4-18 jogadores (máximo 18)

3. Gerar Equipas
   ├─ Balanço automático por performance
   ├─ Distribuição por posição (mínimo 2 GR)
   └─ Divisão 8/7 para números ímpares

4. Registar Resultados
   ├─ Marcar golos e assistências ao vivo
   ├─ Desfazer eventos (undo)
   └─ Confirmar resultado final

5. Ver Estatísticas
   ├─ Jogadores melhor classificados
   ├─ Histórico de partidas
   └─ Rankings por equipa
```

### Algoritmo de Balanceamento de Equipas

O sistema utiliza os seguintes critérios:

1. **Métrica de Performance (Rácio)**
    - Se jogador tem ≥3 jogos: `ratio = golos / jogos`
    - Senão: `ratio = golos / 2` (bonus para novos)

2. **Distribuição de Posições**
    - Mínimo 2 Guarda-redes (GR) por equipa
    - Resto das posições (DEF, MID, AV) alternadas por rácio descendente

3. **Balanceamento**
    - Jogadores ordenados por rácio (melhor primeiro)
    - Alternância 1-1-1 entre equipas
    - Diferença máxima de 1 jogador quando total ímpar

**Exemplo: 15 jogadores (8 vs 7)**

```
Equipa 1: [Melhor GR, 3º DEF, 5º MID, 7º AV, ...]
Equipa 2: [2º GR, 4º DEF, 6º MID, 8º AV, ...]
```

### Páginas Principais

#### 🏠 Dashboard (Jogos)

- Lista todas as partidas
- Filtro por estado (planeado, em progresso, concluído)
- Criar nova partida
- Editar/eliminar existentes

#### 👥 Seleção de Jogadores

- Grid com 10-50 jogadores por página
- Busca por nome em tempo real
- Filtro por posição
- Sidebar com jogadores selecionados (organizados por posição)
- Badge com G/J (golos/jogo) para cada jogador

#### ⚙️ Geração de Equipas

- Resultado da distribuição automática
- Visualização por posição
- Resumo de métricas por equipa

#### 📊 Registar Resultados

- Interface com 2 equipas lado a lado
- Contador de golos por jogador
- Modal de confirmação para eventos
- Histórico de eventos com undo
- Toast notifications para feedback

#### 🏆 Rankings

- Jogadores melhor classificados
- Filtro por estatística (golos, assistências, etc.)
- Ordenação customizável

---

## 📁 Estrutura do Projeto

```
futteams/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Lógica das rotas
│   │   │   ├── GameController.php
│   │   │   ├── PlayerController.php
│   │   │   ├── PositionController.php
│   │   │   ├── RankingController.php
│   │   │   └── ResultController.php
│   │   └── Requests/           # Validações
│   │       └── GenerateTeamsRequest.php
│   ├── Models/                 # Modelos de base de dados
│   │   ├── Game.php
│   │   ├── Player.php
│   │   ├── Team.php
│   │   ├── Position.php
│   │   ├── PlayerGameStatistic.php
│   │   └── GameEvent.php
│   ├── Services/               # Lógica de negócio
│   │   ├── GameService.php
│   │   ├── TeamGenerationService.php
│   │   ├── ResultService.php
│   │   └── RankingService.php
│   └── Repositories/           # Acesso a dados
│       ├── GameRepository.php
│       ├── PlayerRepository.php
│       └── TeamRepository.php
├── database/
│   ├── migrations/             # Schema de BD
│   └── seeders/                # Dados iniciais
│       ├── StrongStatsSeeder.php
│       └── WeakStatsSeeder.php
├── resources/
│   ├── css/
│   │   └── app.css             # Tailwind
│   ├── js/
│   │   ├── app.ts              # Entry point
│   │   └── Pages/
│   │       ├── Games/
│   │       │   ├── Index.vue    # Lista de jogos
│   │       │   ├── Show.vue     # Detalhe + resultado
│   │       │   ├── SelectPlayers.vue  # Seleção inteligente
│   │       │   └── GenerateTeams.vue  # Distribuição
│   │       ├── Players/
│   │       ├── Positions/
│   │       └── Rankings/
│   └── views/
│       └── app.blade.php        # Layout HTML
├── routes/
│   ├── web.php                 # Rotas web
│   └── console.php             # Comandos Artisan
├── config/
│   ├── app.php
│   ├── database.php
│   └── inertia.php
├── vite.config.ts              # Configuração Vite
├── tailwind.config.js          # Tailwind v4
├── tsconfig.json               # TypeScript
├── phpunit.xml                 # Testes
├── artisan                      # CLI Laravel
└── README.md                    # Este ficheiro
```

---

## 🔌 API

### Endpoints Principais

#### Jogos

```
GET    /games                    # Listar jogos
POST   /games                    # Criar novo jogo
GET    /games/{id}               # Detalhe + resultado
PUT    /games/{id}               # Editar
DELETE /games/{id}               # Eliminar
POST   /games/{id}/generate-teams   # Gerar equipas
```

#### Jogadores

```
GET    /api/players              # Listar todos (com stats)
GET    /players                  # CRUD web
POST   /players
PUT    /players/{id}
DELETE /players/{id}
```

#### Resultados

```
POST   /games/{id}/record-event  # Registar evento (golo, assist)
POST   /games/{id}/undo-event    # Desfazer evento
POST   /games/{id}/complete      # Finalizar partida
GET    /games/{id}/events        # Listar eventos
```

#### Posições

```
GET    /positions                # Listar posições
POST   /positions                # Criar
PUT    /positions/{id}           # Editar
DELETE /positions/{id}           # Eliminar
```

---

## 🐛 Troubleshooting

### "Página em branco"

- Verificar logs: `tail storage/logs/laravel.log`
- Confirmar que Vite está a correr: `npm run dev`
- Limpar cache: `php artisan config:clear && php artisan view:clear`

### "Erro ao gerar equipas"

- Mínimo 4 jogadores requerido
- Mínimo 2 guarda-redes na seleção
- Máximo 18 jogadores

### "Base de dados não encontrada"

```bash
php artisan migrate --fresh --seed --seeder=StrongStatsSeeder
```

### "Domínio futteams.test não resolve"

Se usar Herd:

```bash
herd link
```

Se não usar Herd, aceda em `http://localhost:8000`

### "Vite HMR não funciona"

Certifique-se que Vite está a correr:

```bash
npm run dev
```

Se ainda não funcionar:

```bash
# Limpar cache Vite
rm -rf node_modules/.vite
npm run dev
```

---

## 📝 Notas de Desenvolvimento

### Executar Testes

```bash
php artisan test
```

### Formatar Código

```bash
npm run format          # Formatar
npm run format:check    # Verificar formato
```

### Comandos Úteis

```bash
# Reset completo (cuidado!)
php artisan migrate:fresh --seed

# Ver rotas
php artisan route:list

# Tinker REPL
php artisan tinker

# Gerar migration
php artisan make:migration create_table_name

# Gerar modelo
php artisan make:model ModelName -m
```

---

## 📄 Licença

MIT License - veja LICENSE para detalhes.

---

## 👤 Autor

Desenvolvido por Tiago Ferreira

---

## 🤝 Suporte

Para problemas ou sugestões, abra uma issue no repositório.

**Última atualização:** Janeiro de 2026
# futteams
