## Context

Currently, the dashboard.blade.php shows:
- Left sidebar with channel list (searchable, filterable)
- Main area with video player
- Bottom grid showing all channels (channel-list section at lines 163-195)

When importing large M3U playlists via "Sincronizar" button, users see no feedback until import completes. For large lists (1000+ channels), this creates confusion about whether the import is working.

## Goals / Non-Goals

**Goals:**
- Show real-time progress bar during M3U import
- Display current channel being processed
- Prevent UI freeze during large imports
- Keep channel selection in left sidebar

**Non-Goals:**
- Change the player functionality
- Modify the sidebar channel selection behavior
- Add import cancellation (future enhancement)
- Change how channels are stored in database

## Decisions

### 1. Progress Bar Component
**Decision**: Create a dedicated progress bar component shown during sync operations
**Rationale**: Separates concerns, can be reused, easy to style independently
**Alternatives considered**:
- Inline progress text: Harder to style, less visible
- Toast notifications: Disappears, not persistent enough for long operations

### 2. Channel Processing Info Display
**Decision**: Show current channel name/logo being processed in bottom panel
**Rationale**: Gives users concrete feedback on what's happening during sync
**Layout**: Replace bottom channel grid with a status panel showing:
- Current channel being processed (name + logo)
- Progress bar with percentage
- Total channels processed / total channels

### 3. Backend Progress Events
**Decision**: Use Livewire polling or dispatches for progress updates
**Rationale**: Native to Laravel/Livewire stack, no additional dependencies
**Alternatives considered**:
- WebSockets: Overkill for this use case
- SSE: Requires separate endpoint, more complex

## Risks / Trade-offs

[Risk] Large imports still take time → Mitigation: Progress bar manages expectations
[Risk] Progress updates may slow down import → Mitigation: Batch updates (every 10-50 channels)
[Risk] User thinks import froze at 99% → Mitigation: Show "Finalizando..." phase
