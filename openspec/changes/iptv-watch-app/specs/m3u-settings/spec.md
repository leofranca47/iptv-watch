## ADDED Requirements

### Requirement: Save M3U URL
The system SHALL allow users to save the M3U URL in the database settings table.

#### Scenario: Save valid URL
- **WHEN** user enters a valid M3U URL and clicks "Salvar"
- **THEN** system stores the URL in `settings` table with key `m3u_url`
- **AND** displays success message "URL salva com sucesso"

#### Scenario: Save invalid URL
- **WHEN** user enters an invalid URL format and clicks "Salvar"
- **THEN** system displays validation error "URL inválida"

#### Scenario: Retrieve saved URL
- **WHEN** user loads the settings page
- **THEN** system populates the input with the saved M3U URL from `settings` table

### Requirement: Last sync timestamp
The system SHALL store and display the last synchronization timestamp.

#### Scenario: Display last sync
- **WHEN** user views the dashboard
- **THEN** system shows "Última sinc.: {date}" if a timestamp exists
- **OR** shows "Nunca sincronizado" if no timestamp exists