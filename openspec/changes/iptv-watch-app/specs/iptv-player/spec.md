## ADDED Requirements

### Requirement: Play HLS stream
The system SHALL play HLS streams using hls.js player.

#### Scenario: Start playback
- **WHEN** channel is selected with valid stream URL
- **THEN** hls.js loads and plays the HLS stream
- **AND** player shows loading spinner during buffer

#### Scenario: Stream error
- **WHEN** stream fails to load
- **THEN** player displays error message "Erro ao reproduzir canal"
- **AND** shows retry button

### Requirement: Fullscreen mode
The system SHALL allow player to enter fullscreen mode.

#### Scenario: Enter fullscreen
- **WHEN** user clicks fullscreen button
- **THEN** player enters browser fullscreen mode

#### Scenario: Exit fullscreen
- **WHEN** user presses Escape or clicks exit fullscreen button
- **THEN** player exits fullscreen mode

### Requirement: Player controls
The system SHALL provide standard video controls (play/pause, volume).

#### Scenario: Pause video
- **WHEN** user clicks pause button
- **THEN** video playback pauses

#### Scenario: Change volume
- **WHEN** user adjusts volume slider
- **THEN** video volume changes accordingly

### Requirement: Player states
The system SHALL display appropriate UI for each player state.

#### Scenario: Idle state
- **WHEN** no channel is selected
- **THEN** player shows placeholder "Selecione um canal para assistir"

#### Scenario: Loading state
- **WHEN** stream is buffering
- **THEN** player shows loading spinner centered

#### Scenario: Playing state
- **WHEN** stream is playing
- **THEN** player shows video with controls visible on hover