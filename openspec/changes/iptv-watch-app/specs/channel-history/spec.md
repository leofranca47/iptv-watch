## ADDED Requirements

### Requirement: Save last channel
The system SHALL save the last watched channel URL to settings.

#### Scenario: Save on watch
- **WHEN** user watches a channel
- **THEN** system saves `channel_id` to `settings` with key `last_channel_id`

### Requirement: Restore last channel
The system SHALL restore the last watched channel when page loads.

#### Scenario: Auto-select last channel
- **WHEN** user loads dashboard
- **AND** `last_channel_id` exists in settings
- **THEN** system auto-selects that channel
- **AND** starts playback automatically