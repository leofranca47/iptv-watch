## Why

Aplicativo web para visualizar canais IPTV com interface moderna. O usuário precisa conseguir configurar a URL do M3U, sincronizar a lista de canais, e assistir aos streams diretamente no navegador com player integrado e suporte a fullscreen.

## What Changes

- Nova página principal (Dashboard) com layout responsivo
- Menu lateral colapsável com lista de canais
- Filtros de busca e grupo na parte superior
- Player de vídeo HLS.js integrado na página
- Botão de fullscreen para o player
- Componente de configurações para URL do M3U
- Sincronização manual da lista de canais (botão)
- Busca por nome e filtro por grupo de canais
- Histórico do último canal assistido
- Persistência no banco SQLite

## Capabilities

### New Capabilities

- `m3u-settings`: Interface para configurar URL do M3U e salvar no banco
- `m3u-parser`: Servicio para baixar e parsear arquivos M3U8/M3U
- `channel-list`: Exibição da lista de canais com grid responsivo
- `channel-search`: Busca por nome e filtro por grupo
- `iptv-player`: Player HLS.js com controles e fullscreen
- `channel-history`: Salvar e restaurar último canal assistido

### Modified Capabilities

(nenhum - projeto novo)

## Impact

- **Backend**: Novos Models (Channel, Setting), migrations, M3UParser service
- **Frontend**: Livewire components (Settings, Dashboard, Player)
- **Dependências**: hls.js para player HLS
- **Banco**: Tabelas `channels` e `settings` no SQLite