## ADDED Requirements

### Requirement: Display channel list
The system SHALL display all active channels in a responsive grid.

#### Scenario: Display channels
- **WHEN** dashboard loads
- **THEN** system shows all `Channel` records where `is_active = true`
- **AND** displays each channel with: logo, name, group badge

#### Scenario: Empty state
- **WHEN** no channels exist in database
- **THEN** system shows message "Nenhum canal encontrado. Configure o M3U nas configurações."

### Requirement: Channel card click
The system SHALL play the selected channel when user clicks on a channel card.

#### Scenario: Click channel
- **WHEN** user clicks on a channel card
- **THEN** system loads the stream URL in the player
- **AND** highlights the selected channel card

### Requirement: Toggle sidebar visibility
The system SHALL allow users to hide/show the channel list sidebar.

#### Scenario: Hide sidebar
- **WHEN** user clicks the toggle button (◀)
- **THEN** sidebar collapses to icons only
- **AND** toggle button changes to (▶)

#### Scenario: Show sidebar
- **WHEN** user clicks the toggle button (▶)
- **THEN** sidebar expands to full width
- **AND** toggle button changes to (◀)