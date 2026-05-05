## ADDED Requirements

### Requirement: Display import progress
The system SHALL display a progress bar during M3U import showing current status.

#### Scenario: Show progress bar during import
- **WHEN** user clicks "Sincronizar" and M3U sync starts
- **THEN** system shows progress bar at bottom of main content area
- **AND** progress bar displays: percentage, current channel name, channels processed/total

#### Scenario: Progress bar shows current channel
- **WHEN** import is in progress
- **THEN** system displays the name and logo of channel currently being processed
- **AND** updates in real-time as channels are processed

#### Scenario: Progress bar completes
- **WHEN** all channels have been processed
- **THEN** progress bar shows 100% and displays "Finalizando..."
- **AND** progress bar disappears after import completes successfully

#### Scenario: Progress bar shows errors
- **WHEN** import encounters an error
- **THEN** progress bar shows error message
- **AND** progress bar disappears after error is acknowledged

### Requirement: Remove bottom channel grid during import
The system SHALL replace the bottom channel grid with the progress panel during sync.

#### Scenario: Grid replaced during sync
- **WHEN** M3U sync starts
- **THEN** the "Todos os Canais" grid section is hidden
- **AND** progress panel is displayed in its place

#### Scenario: Grid restored after sync
- **WHEN** M3U sync completes or fails
- **THEN** the progress panel is hidden
- **AND** the channel grid area returns to empty state (no channels displayed at bottom)
