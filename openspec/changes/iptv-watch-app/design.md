## Context

Projeto novo em Laravel 13 + Livewire. Banco SQLite existente. O app precisa baixar e parsear arquivos M3U/M3U8, extrair informações de canais (nome, logo, grupo, stream URL) e reproduzir streams HLS no navegador.

## Goals / Non-Goals

**Goals:**
- Interface responsiva com menu lateral colapsável
- Player HLS integrado com suporte fullscreen
- Busca por nome e filtro por categoria/grupo
- Grid de canais abaixo do player
- Configuração de URL M3U persistida no banco

**Non-Goals:**
- Autenticação de usuários
- Gravação de streams
- Suporte a múltiplas playlists
- Apps mobile (web only)

## Decisions

### 1. Player HLS com hls.js

**Decisão**: Usar hls.js para reprodução de streams HLS nativamente no browser.

**Alternativas consideradas**:
- Video.js: Mais heavyweight, mais dependências
- Native HLS (`<video>` com `.m3u8`): Funciona só no Safari

** rationale**: hls.js é leve (~150KB), bem mantido, e funciona em todos os browsers modernos.

### 2. Layout com menu lateral colapsável

```
┌────────────────────────────────────────────────────────────┐
│ HEADER: [Logo] IPTV Watch           [⚙️ Settings]         │
├────────────────────────────────────────────────────────────┤
│ [🔍 Buscar...] [Grupo ▾] [🔄 Sync]            [◀ Toggle] │
├─────┬──────────────────────────────────────────────────────┤
│     │                                                      │
│  M  │              PLAYER (HLS.js)                        │
│  E  │           [Fullscreen button]                        │
│  N  │                                                      │
│  U  ├──────────────────────────────────────────────────────┤
│     │                                                      │
│  L  │                                                      │
│  A  │              GRID DE CANAIS                          │
│  T  │  ┌───┐ ┌───┐ ┌───┐ ┌───┐ ┌───┐ ┌───┐               │
│  E  │  │   │ │   │ │   │ │   │ │   │ │   │               │
│  R  │  └───┘ └───┘ └───┘ └───┘ └───┘ └───┘               │
│     │                                                      │
│  ◀  │                                                      │
└─────┴──────────────────────────────────────────────────────┘
```

** rationale**: Menu na lateral economiza espaço vertical. Colapsável para dar mais espaço ao player.

### 3. Parser M3U formato EXTINF

Formato M3U padrão:
```
#EXTINF:-1 tvg-name="Canal" tvg-logo="url" group-title="Grupo",Canal
http://stream.url/live.m3u8
```

** rationale**: A maioria das playlists IPTV usa esse formato. O parser deve extrair:
- `tvg-name`: Nome do canal
- `tvg-logo`: URL do logo
- `group-title`: Categoria/grupo
- URL do stream (linha seguinte)

### 4. Estrutura de Arquivos

```
app/
├── Http/Livewire/
│   ├── Dashboard.php      # Layout principal, gerencia estado
│   ├── ChannelList.php    # Lista de canais no sidebar
│   ├── ChannelGrid.php    # Grid de seleção
│   ├── Player.php         # Player HLS
│   └── Settings.php       # Configuração M3U
├── Models/
│   ├── Channel.php        # $fillable: name, logo, group, stream_url
│   └── Setting.php        # $fillable: key, value
├── Services/
│   └── M3UParser.php      # Parse e sync de channels
database/migrations/
├── xxxx_create_channels_table.php
└── xxxx_create_settings_table.php
resources/views/livewire/
├── dashboard.blade.php
├── channel-list.blade.php
├── channel-grid.blade.php
├── player.blade.php
└── settings.blade.php
```

### 5. Estados do Player

| Estado | Comportamento |
|--------|---------------|
| Idle | Mensagem "Selecione um canal" |
| Loading | Spinner central |
| Playing | Video + controles |
| Error | Mensagem de erro + retry |

## Risks / Trade-offs

- **Risk**: Stream pode não funcionar se CORS bloqueado
  - **Mitigation**: Avisar usuário; alguns streams têm CORS liberado

- **Risk**: M3U URL inválida ou fora do ar
  - **Mitigation**: Validação + mensagem de erro clara

- **Trade-off**: Player abaixo do grid vs ao lado
  - **Decisão**: Player acima, grid abaixo (mais natural para mobile)

## Migration Plan

1. Criar migrations para `channels` e `settings`
2. Criar Models e Services
3. Criar Livewire components
4. Criar views Blade
5. Testar parser com M3U real
6. Testar player com stream real