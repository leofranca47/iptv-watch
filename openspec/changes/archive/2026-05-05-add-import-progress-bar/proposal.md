## Why

When importing a large M3U playlist, users have no feedback on the import progress. The interface appears frozen or unresponsive, causing confusion about whether the import is working or if something broke. This creates a poor user experience especially with large lists containing thousands of channels.

## What Changes

- Add a visible progress bar during M3U import showing current channel being processed and overall completion percentage
- Replace the bottom channel grid with a channel selection interface on the left side
- Move channel information display to the bottom panel (current channel name, logo, status)
- Import runs asynchronously and completes only when all channels are fully processed

## Capabilities

### New Capabilities
- `import-progress`: Tracks and displays M3U import progress with real-time feedback

### Modified Capabilities
- `m3u-parser`: Requirement "Sync channels to database" is modified to include progress reporting
- `channel-list`: Requirement "Display channel list" is modified - channels are selected on left sidebar instead of displayed on bottom

## Impact

- Frontend: New progress bar component, layout changes to channel selection area
- Backend: M3U sync job needs to dispatch progress events
- Database: No schema changes required
