## ADDED Requirements

### Requirement: Fetch M3U from URL
The system SHALL fetch the M3U file from the configured URL.

#### Scenario: Successful fetch
- **WHEN** user clicks "Sincronizar"
- **AND** a valid M3U URL is configured
- **THEN** system downloads the M3U file from the URL
- **AND** parses the content

#### Scenario: Network error
- **WHEN** user clicks "Sincronizar"
- **AND** the URL is unreachable or returns error
- **THEN** system displays error message "Erro ao acessar URL"

### Requirement: Parse M3U content
The system SHALL parse M3U/M3U8 content and extract channel information.

#### Scenario: Parse valid M3U
- **WHEN** system receives valid M3U content
- **THEN** for each `#EXTINF` line, extract:
  - `tvg-name`: Channel name
  - `tvg-logo`: Logo URL
  - `group-title`: Category/group
  - Line immediately after: Stream URL

#### Scenario: Handle malformed M3U
- **WHEN** M3U content has missing or malformed data
- **THEN** system uses default values: name from URL, logo empty, group "Outros"

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