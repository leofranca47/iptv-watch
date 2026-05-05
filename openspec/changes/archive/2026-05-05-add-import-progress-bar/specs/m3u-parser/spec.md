## MODIFIED Requirements

### Requirement: Sync channels to database
The system SHALL save parsed channels to the database, updating existing or creating new.

#### Scenario: New channel
- **WHEN** parsed channel has `stream_url` not in database
- **THEN** system creates new `Channel` record

#### Scenario: Existing channel
- **WHEN** parsed channel has `stream_url` already in database
- **THEN** system updates `name`, `logo`, `group` of existing record

#### Scenario: Channel removed from M3U
- **WHEN** sync completes
- **THEN** system marks channels not in latest M3U as `is_active = false`

#### Scenario: Progress reporting during sync
- **WHEN** sync is in progress
- **THEN** system dispatches progress events after processing each channel
- **AND** each event includes: channel name, channel logo, processed count, total count
- **AND** events are dispatched to frontend via Livewire
